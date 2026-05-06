<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReceiveSession;
use App\Models\ItemPiece;
use App\Models\ReceiveSessionPiece;
use App\Models\EventPieceDispatch;
use App\Models\EventItem;
use App\Models\Event;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class ReceiveController extends Controller
{
    /**
     * Show the receive session mobile scan interface
     */
    public function show(Request $request, string $token)
    {
        $session = $request->attributes->get('receive_session');
        $event   = $session->load('event')->event;
        $event->load(['eventItems.item', 'borrowedItems', 'operationalItems']);

        // Get all pieces that were dispatched for this event
        $dispatchedPieces = EventPieceDispatch::where('event_id', $event->id)
            ->whereNotNull('dispatched_at')
            ->with('itemPiece.item')
            ->get();

        $totalRequired  = $dispatchedPieces->count();
        $alreadyScanned = $session->received_count;

        $processUrl      = route('receive.process',       $session->session_token);
        $submitUrl       = route('receive.submit',        $session->session_token);
        $saveProgressUrl = route('receive.save-progress', $session->session_token);
        $csrfToken       = csrf_token();

        // Calculate first suggestion (first item that has pieces not yet received)
        $firstSuggestion = null;
        foreach ($event->eventItems as $eventItem) {
            $dispatchedForItem = EventPieceDispatch::where('event_id', $event->id)
                ->whereHas('itemPiece', fn($q) => $q->where('item_id', $eventItem->item_id))
                ->count();

            $receivedForItem = ReceiveSessionPiece::where('receive_session_id', $session->id)
                ->where('item_id', $eventItem->item_id)
                ->count();

            $remaining = $dispatchedForItem - $receivedForItem;
            if ($remaining > 0) {
                $firstSuggestion = [
                    'item_name' => $eventItem->item->name,
                    'remaining' => $remaining,
                ];
                break;
            }
        }

        return view('receive.show', compact(
            'session', 'event',
            'totalRequired', 'alreadyScanned',
            'processUrl', 'submitUrl', 'saveProgressUrl',
            'csrfToken', 'firstSuggestion', 'dispatchedPieces'
        ));
    }

    /**
     * Process a scanned item during receive
     */
    public function process(Request $request, string $token)
    {
        $session = $request->attributes->get('receive_session');

        if (!$session) {
            return response()->json([
                'status' => 'error',
                'code' => 'SESSION_NOT_FOUND',
                'message' => 'Receive session not found. Please refresh and try again.',
            ], 404);
        }

        $session->load('event');
        $event = $session->event;

        // Handle condition + destination update (second call after selections on phone)
        if ($request->boolean('update_details_only')) {
            $uniqueCode = strtoupper(trim($request->unique_code));

            $receivePiece = ReceiveSessionPiece::where('receive_session_id', $session->id)
                ->where('unique_code', $uniqueCode)
                ->first();

            if ($receivePiece && $request->filled('condition_score') && $request->filled('destination')) {
                $receivePiece->update([
                    'condition_score' => $request->condition_score,
                    'destination' => $request->destination,
                ]);

                // Update the piece condition score
                $receivePiece->itemPiece->update(['condition_score' => $request->condition_score]);

                // Update event_piece_dispatches record
                EventPieceDispatch::where('event_id', $session->event_id)
                    ->where('item_piece_id', $receivePiece->item_piece_id)
                    ->update(['condition_on_return' => $request->condition_score]);
            }

            return response()->json(['status' => 'success', 'code' => 'DETAILS_UPDATED']);
        }

        // Validate request
        $request->validate([
            'unique_code'     => 'required|string',
            'condition_score' => 'nullable|integer|between:1,5',
            'destination'     => 'nullable|in:warehouse,cleaning,repair',
        ]);

        $uniqueCode = strtoupper(trim($request->unique_code));

        // STEP 1 — Find the piece
        $piece = ItemPiece::where('unique_code', $uniqueCode)->first();

        if (!$piece) {
            return response()->json([
                'status'  => 'error',
                'code'    => 'PIECE_NOT_FOUND',
                'message' => 'This code does not match any item in the system.',
            ], 404);
        }

        // STEP 2 — Check this piece was dispatched for this event (and not yet returned)
        $dispatchRecord = EventPieceDispatch::where('event_id', $event->id)
            ->where('item_piece_id', $piece->id)
            ->whereNotNull('dispatched_at')
            ->whereNull('returned_at')
            ->first();

        if (!$dispatchRecord) {
            // Check if it was dispatched but already returned
            $alreadyReturned = EventPieceDispatch::where('event_id', $event->id)
                ->where('item_piece_id', $piece->id)
                ->whereNotNull('dispatched_at')
                ->whereNotNull('returned_at')
                ->exists();

            if ($alreadyReturned) {
                return response()->json([
                    'status'    => 'warning',
                    'code'      => 'ALREADY_RETURNED',
                    'message'   => 'This piece was already returned for this event.',
                    'item_name' => $piece->item->name,
                    'unique_code' => $uniqueCode,
                ], 422);
            }

            return response()->json([
                'status'    => 'error',
                'code'      => 'NOT_DISPATCHED',
                'message'   => 'This specific piece was not dispatched for this event.',
                'item_name' => $piece->item->name,
                'unique_code' => $uniqueCode,
            ], 422);
        }

        // STEP 3 — Verify piece is currently assigned to this event
        if ($piece->status !== 'Assigned' || $piece->current_event_id != $event->id) {
            return response()->json([
                'status'    => 'error',
                'code'      => 'INVALID_STATUS',
                'message'   => 'This piece is not currently assigned to this event.',
                'item_name' => $piece->item->name,
                'unique_code' => $uniqueCode,
            ], 422);
        }

        // STEP 4 — Check piece not already received in this session
        $alreadyInSession = ReceiveSessionPiece::where('receive_session_id', $session->id)
            ->where('item_piece_id', $piece->id)
            ->exists();

        if ($alreadyInSession) {
            return response()->json([
                'status'      => 'warning',
                'code'        => 'ALREADY_RECEIVED',
                'message'     => 'This piece was already received in this session.',
                'item_name'   => $piece->item->name,
                'unique_code' => $uniqueCode,
            ], 422);
        }

        // STEP 5 — If no condition/destination, this is the FIRST scan - just validate and return success
        // The mobile app will show the overlay, user selects condition+destination, then calls again
        if (!$request->filled('condition_score') || !$request->filled('destination')) {
            // Calculate progress for response
            $totalDispatched = EventPieceDispatch::where('event_id', $event->id)
                ->whereNotNull('dispatched_at')
                ->count();
            $totalReceived = $session->received_count;

            // Next suggested item
            $nextSuggestion = $this->getNextSuggestion($session, $event);

            return response()->json([
                'status'          => 'success',
                'code'            => 'RECEIVED',
                'message'         => 'Item validated successfully. Please select condition and destination.',
                'item_name'       => $piece->item->name,
                'unique_code'     => $uniqueCode,
                'category'        => $piece->item->category,
                'image_url'       => $piece->item->image_path
                                        ? asset('storage/' . $piece->item->image_path)
                                        : null,
                'total_received'  => $totalReceived,
                'total_required'  => $totalDispatched,
                'all_complete'    => false, // Not saved yet
                'next_suggestion' => $nextSuggestion,
            ]);
        }

        // STEP 6 — Record the receive scan (condition + destination provided)
        // Double-check this piece hasn't been received yet (race condition prevention)
        $alreadyReceived = ReceiveSessionPiece::where('receive_session_id', $session->id)
            ->where('item_piece_id', $piece->id)
            ->exists();

        if ($alreadyReceived) {
            return response()->json([
                'status'      => 'warning',
                'code'        => 'ALREADY_RECEIVED',
                'message'     => 'This piece was already received in this session.',
                'item_name'   => $piece->item->name,
                'unique_code' => $uniqueCode,
            ], 422);
        }

        DB::transaction(function () use ($session, $piece, $event, $request, $dispatchRecord) {

            // Create receive session piece record
            ReceiveSessionPiece::create([
                'receive_session_id' => $session->id,
                'item_piece_id'      => $piece->id,
                'unique_code'        => $piece->unique_code,
                'item_id'            => $piece->item_id,
                'condition_score'    => $request->condition_score,
                'destination'        => $request->destination ?? 'warehouse',
                'received_by'        => auth()->id(),
                'received_at'        => now(),
            ]);

            // Update receive session count
            $session->increment('received_count');

            // Update event_items quantity_returned
            $eventItem = EventItem::where('event_id', $event->id)
                ->where('item_id', $piece->item_id)
                ->first();

            if ($eventItem) {
                $eventItem->increment('quantity_returned');

                // Set returned_at if first piece
                if ($eventItem->fresh()->quantity_returned == 1) {
                    $eventItem->update([
                        'returned_at' => now(),
                        'returned_by' => auth()->id(),
                    ]);
                }
            }
        });

        // STEP 5 — Calculate progress for response
        $totalDispatched = EventPieceDispatch::where('event_id', $event->id)
            ->whereNotNull('dispatched_at')
            ->count();
        $totalReceived = $session->fresh()->received_count;
        $allComplete = $totalReceived >= $totalDispatched;

        // Next suggested item
        $nextSuggestion = $this->getNextSuggestion($session, $event);

        return response()->json([
            'status'          => 'success',
            'code'            => 'RECEIVED',
            'message'         => 'Item received successfully.',
            'item_name'       => $piece->item->name,
            'unique_code'     => $uniqueCode,
            'category'        => $piece->item->category,
            'image_url'       => $piece->item->image_path
                                    ? asset('storage/' . $piece->item->image_path)
                                    : null,
            'condition_score' => $request->condition_score,
            'destination'     => $request->destination ?? 'warehouse',
            'total_received'  => $totalReceived,
            'total_required'  => $totalDispatched,
            'all_complete'    => $allComplete,
            'next_suggestion' => $nextSuggestion,
        ]);
    }

    /**
     * Submit completed receive session
     */
    public function submit(Request $request, string $token)
    {
        try {
            $session = $request->attributes->get('receive_session');

            if (!$session) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 'SESSION_NOT_FOUND',
                    'message' => 'Session data not found. Please refresh and try again.',
                ], 404);
            }

            $event = $session->event;

            // Verify all dispatched pieces have been received
            $totalDispatched = EventPieceDispatch::where('event_id', $event->id)
                ->whereNotNull('dispatched_at')
                ->count();
            $totalReceived = $session->received_count;

            if ($totalReceived < $totalDispatched) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 'INCOMPLETE',
                    'message' => 'Not all items have been received yet.',
                    'received' => $totalReceived,
                    'required'=> $totalDispatched,
                ], 422);
            }

            DB::transaction(function () use ($session, $event) {

                // Mark session as completed
                $session->update([
                    'status'       => 'completed',
                    'completed_at' => now(),
                ]);

                // Process each received piece
                $receivedPieces = ReceiveSessionPiece::where('receive_session_id', $session->id)
                    ->with('itemPiece')
                    ->get();

                foreach ($receivedPieces as $receivePiece) {
                    $destination = $receivePiece->destination ?? 'warehouse';

                    // Update piece status and location based on destination
                    $status = match($destination) {
                        'cleaning' => 'Cleaning',
                        'repair' => 'Under Repair',
                        default => 'Available',
                    };

                    $location = match($destination) {
                        'cleaning' => 'Cleaning Bay',
                        'repair' => 'Repair Workshop',
                        default => 'Warehouse',
                    };

                    $receivePiece->itemPiece->update([
                        'status' => $status,
                        'location' => $location,
                        'current_event_id' => null,
                        'condition_score' => $receivePiece->condition_score,
                    ]);

                    // Update event_piece_dispatches
                    EventPieceDispatch::where('event_id', $event->id)
                        ->where('item_piece_id', $receivePiece->item_piece_id)
                        ->update([
                            'condition_on_return' => $receivePiece->condition_score,
                            'return_destination' => $destination,
                            'returned_at' => now(),
                            'returned_by' => auth()->id(),
                        ]);

                    // If piece is going to repair, create repair record
                    if ($destination === 'repair') {
                        \App\Models\Repair::firstOrCreate(
                            ['item_id' => $receivePiece->item_id, 'status' => 'Pending'],
                            [
                                'description' => 'Flagged on return from ' . $event->name,
                                'started_at' => now(),
                            ]
                        );
                    }

                    // Log activity
                    ActivityLog::create([
                        'item_id' => $receivePiece->item_id,
                        'event_id' => $event->id,
                        'action' => 'item_received',
                        'description' => 'Piece ' . $receivePiece->unique_code . ' received → ' . ucfirst($destination),
                        'user_id' => auth()->id(),
                    ]);
                }

                // Update event status
                $event->update(['status' => 'Completed']);

                // Log overall activity
                ActivityLog::create([
                    'event_id' => $event->id,
                    'action' => 'receive_completed',
                    'description' => 'Receive completed via scan session — ' . $session->received_count . ' pieces received.',
                    'user_id' => auth()->id(),
                ]);
            });

            return response()->json([
                'status'     => 'success',
                'code'       => 'RECEIVE_COMPLETE',
                'message'    => 'Receive completed successfully.',
                'redirect'   => route('receive.complete', $session->session_token),
                'event_name' => $event->name,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 'SERVER_ERROR',
                'message' => 'An error occurred while completing receive. Please try again.',
            ], 500);
        }
    }

    /**
     * Save progress without submitting
     */
    public function saveProgress(Request $request, string $token)
    {
        $session = $request->attributes->get('receive_session');

        $totalDispatched = EventPieceDispatch::where('event_id', $session->event_id)
            ->whereNotNull('dispatched_at')
            ->count();

        return response()->json([
            'status'        => 'success',
            'code'          => 'PROGRESS_SAVED',
            'message'       => 'Progress saved. Resume by scanning the session QR again.',
            'received'      => $session->received_count,
            'required'      => $totalDispatched,
            'session_token' => $session->session_token,
            'expires_at'    => $session->expires_at->toISOString(),
        ]);
    }

    /**
     * Show receive complete page (mobile-friendly)
     */
    public function complete(Request $request, string $token)
    {
        $session = $request->attributes->get('receive_session');
        $event   = $session->event;

        return view('receive.complete', compact('session', 'event'));
    }

    /**
     * Get next item suggestion for receiving
     */
    private function getNextSuggestion(ReceiveSession $session, Event $event): ?array
    {
        foreach ($event->eventItems as $eventItem) {
            $dispatchedForItem = EventPieceDispatch::where('event_id', $event->id)
                ->whereHas('itemPiece', fn($q) => $q->where('item_id', $eventItem->item_id))
                ->count();

            $receivedForItem = ReceiveSessionPiece::where('receive_session_id', $session->id)
                ->where('item_id', $eventItem->item_id)
                ->count();

            $remaining = $dispatchedForItem - $receivedForItem;

            if ($remaining > 0) {
                return [
                    'item_name' => $eventItem->item->name,
                    'category'  => $eventItem->item->category,
                    'remaining' => $remaining,
                    'image_url' => $eventItem->item->image_path
                                    ? asset('storage/' . $eventItem->item->image_path)
                                    : null,
                ];
            }
        }

        return null; // All items complete
    }
}
