<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScanSession;
use App\Models\ItemPiece;
use App\Models\ScanSessionPiece;
use App\Models\EventPieceDispatch;
use App\Models\EventItem;
use App\Models\Event;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class ScanController extends Controller
{
    /**
     * Show the scan session interface
     */
    public function show(Request $request, string $token)
    {
        $session = $request->attributes->get('scan_session');
        $event   = $session->load('event')->event;
        $event->load(['eventItems.item', 'borrowedItems', 'operationalItems']);

        $totalRequired  = $event->eventItems->sum('quantity_requested');
        $alreadyScanned = $session->scanned_count;

        $processUrl      = route('scan.process',       $session->session_token);
        $submitUrl       = route('scan.submit',        $session->session_token);
        $saveProgressUrl = route('scan.save-progress', $session->session_token);
        $csrfToken       = csrf_token();

        // Calculate first suggestion
        $firstSuggestion = null;
        foreach ($event->eventItems as $eventItem) {
            $scanned = ScanSessionPiece::where('scan_session_id', $session->id)
                ->where('item_id', $eventItem->item_id)
                ->count();
            $remaining = $eventItem->quantity_requested - $scanned;
            if ($remaining > 0) {
                $firstSuggestion = [
                    'item_name' => $eventItem->item->name,
                    'remaining' => $remaining,
                ];
                break;
            }
        }

        return view('scan.show', compact(
            'session', 'event',
            'totalRequired', 'alreadyScanned',
            'processUrl', 'submitUrl', 'saveProgressUrl',
            'csrfToken', 'firstSuggestion'
        ));
    }

    /**
     * Process a scanned item
     */
    public function process(Request $request, string $token)
    {
        // Get session from middleware
        $session = $request->attributes->get('scan_session');
        $event = $session->event;

        // Handle condition-only update (second call after condition selected on phone)
        if ($request->boolean('update_condition_only')) {
            $uniqueCode = strtoupper(trim($request->unique_code));

            // Find the scan session piece record
            $scanPiece = ScanSessionPiece::where('scan_session_id', $session->id)
                ->where('unique_code', $uniqueCode)
                ->first();

            if ($scanPiece && $request->filled('condition_score')) {
                $scanPiece->update(['condition_score' => $request->condition_score]);

                // Update the piece condition score
                $scanPiece->itemPiece->update(['condition_score' => $request->condition_score]);

                // Update event_piece_dispatches record
                EventPieceDispatch::where('event_id', $session->event_id)
                    ->where('item_piece_id', $scanPiece->item_piece_id)
                    ->update(['condition_on_dispatch' => $request->condition_score]);
            }

            return response()->json(['status' => 'success', 'code' => 'CONDITION_UPDATED']);
        }

        // Validate request
        $request->validate([
            'unique_code'     => 'required|string',
            'condition_score' => 'nullable|integer|between:1,5',
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

        // STEP 2 — Check item is on this event packing list
        $eventItem = $event->eventItems()
            ->where('item_id', $piece->item_id)
            ->first();

        if (!$eventItem) {
            return response()->json([
                'status'    => 'error',
                'code'      => 'NOT_ON_LIST',
                'message'   => 'This item is not on the packing list for this event.',
                'item_name' => $piece->item->name,
                'unique_code' => $uniqueCode,
            ], 422);
        }

        // STEP 3 — Check quantity not already met
        $alreadyScanned = ScanSessionPiece::where('scan_session_id', $session->id)
            ->where('item_id', $piece->item_id)
            ->count();

        if ($alreadyScanned >= $eventItem->quantity_requested) {
            return response()->json([
                'status'    => 'error',
                'code'      => 'QUANTITY_MET',
                'message'   => 'Required quantity for ' . $piece->item->name . ' has already been scanned.',
                'item_name' => $piece->item->name,
                'scanned'   => $alreadyScanned,
                'required'  => $eventItem->quantity_requested,
            ], 422);
        }

        // STEP 4 — Check piece not already scanned in this session
        $alreadyInSession = ScanSessionPiece::where('scan_session_id', $session->id)
            ->where('item_piece_id', $piece->id)
            ->exists();

        if ($alreadyInSession) {
            return response()->json([
                'status'      => 'warning',
                'code'        => 'ALREADY_SCANNED',
                'message'     => 'This piece was already scanned in this session.',
                'item_name'   => $piece->item->name,
                'unique_code' => $uniqueCode,
            ], 422);
        }

        // STEP 5 — Check piece availability
        // STRICT: Only allow 'Available' pieces
        // If piece is 'Assigned', it's already dispatched and cannot be re-dispatched
        if ($piece->status === 'Assigned') {
            // Check if assigned to THIS event (already dispatched in this session)
            if ($piece->current_event_id == $event->id) {
                $alreadyDispatched = EventPieceDispatch::where('event_id', $event->id)
                    ->where('item_piece_id', $piece->id)
                    ->whereNotNull('dispatched_at')
                    ->whereNull('returned_at')
                    ->exists();

                if ($alreadyDispatched) {
                    return response()->json([
                        'status'      => 'warning',
                        'code'        => 'ALREADY_DISPATCHED',
                        'message'     => 'This piece is already dispatched for this event and awaiting return.',
                        'item_name'   => $piece->item->name,
                        'unique_code' => $uniqueCode,
                    ], 422);
                }
            } else {
                // Assigned to different event
                return response()->json([
                    'status'      => 'error',
                    'code'        => 'ASSIGNED_ELSEWHERE',
                    'message'     => 'This piece is currently assigned to another event and cannot be dispatched.',
                    'item_name'   => $piece->item->name,
                    'unique_code' => $uniqueCode,
                ], 422);
            }
        }

        if ($piece->status !== 'Available') {
            return response()->json([
                'status'      => 'error',
                'code'        => 'NOT_AVAILABLE',
                'message'     => 'This piece is currently ' . $piece->status . ' and cannot be dispatched.',
                'item_name'   => $piece->item->name,
                'piece_status'=> $piece->status,
                'unique_code' => $uniqueCode,
            ], 422);
        }

        // STEP 6 — Record the scan
        DB::transaction(function () use ($session, $piece, $event, $request) {

            // Create scan session piece record
            ScanSessionPiece::create([
                'scan_session_id' => $session->id,
                'item_piece_id'   => $piece->id,
                'unique_code'     => $piece->unique_code,
                'item_id'         => $piece->item_id,
                'condition_score' => $request->condition_score,
                'scanned_by'      => auth()->id(),
                'scanned_at'      => now(),
            ]);

            // Update piece status
            $piece->update([
                'status'           => 'Assigned',
                'current_event_id' => $event->id,
                'condition_score'  => $request->condition_score,
            ]);

            // Create event_piece_dispatches record
            EventPieceDispatch::create([
                'event_id'            => $event->id,
                'item_piece_id'       => $piece->id,
                'condition_on_dispatch' => $request->condition_score,
                'dispatched_at'       => now(),
                'dispatched_by'       => auth()->id(),
            ]);

            // Update scan session scanned count
            $session->increment('scanned_count');

            // Update event_items quantity_dispatched and set dispatch info
            $eventItem = EventItem::where('event_id', $event->id)
                ->where('item_id', $piece->item_id)
                ->first();

            if ($eventItem) {
                $eventItem->increment('quantity_dispatched');

                // Set dispatched_at and batch if first piece
                if ($eventItem->fresh()->quantity_dispatched == 1) {
                    $eventItem->update([
                        'dispatched_at' => now(),
                        'dispatched_by' => auth()->id(),
                        'dispatch_batch' => $session->dispatch_batch ?? 1,
                    ]);
                }
            }
        });

        // STEP 7 — Calculate progress for response
        $totalRequested = $event->eventItems->sum('quantity_requested');
        $totalScanned   = $session->fresh()->scanned_count;
        $allComplete    = $totalScanned >= $totalRequested;

        // Next suggested item
        $nextSuggestion = $this->getNextSuggestion($session, $event);

        return response()->json([
            'status'          => 'success',
            'code'            => 'SCANNED',
            'message'         => 'Item scanned successfully.',
            'item_name'       => $piece->item->name,
            'unique_code'     => $uniqueCode,
            'category'        => $piece->item->category,
            'image_url'       => $piece->item->image_path
                                    ? asset('storage/' . $piece->item->image_path)
                                    : null,
            'condition_score' => $request->condition_score,
            'total_scanned'   => $totalScanned,
            'total_required'  => $totalRequested,
            'all_complete'    => $allComplete,
            'next_suggestion' => $nextSuggestion,
        ]);
    }

    /**
     * Submit completed scan session
     */
    public function submit(Request $request, string $token)
    {
        try {
            $session = $request->attributes->get('scan_session');

            if (!$session) {
                \Log::error('Scan submit failed: session not found in request attributes', [
                    'token' => $token,
                    'user_id' => auth()->id(),
                ]);

                return response()->json([
                    'status'  => 'error',
                    'code'    => 'SESSION_NOT_FOUND',
                    'message' => 'Session data not found. Please refresh and try again.',
                ], 404);
            }

            $event = $session->event;

            // Verify all required pieces have been scanned
            $totalRequested = $event->eventItems->sum('quantity_requested');
            $totalScanned   = $session->scanned_count;

            \Log::info('Scan submit attempt', [
                'session_id' => $session->id,
                'event_id' => $event->id,
                'scanned' => $totalScanned,
                'required' => $totalRequested,
                'user_id' => auth()->id(),
            ]);

            if ($totalScanned < $totalRequested) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 'INCOMPLETE',
                    'message' => 'Not all items have been scanned yet.',
                    'scanned' => $totalScanned,
                    'required'=> $totalRequested,
                ], 422);
            }

            DB::transaction(function () use ($session, $event) {

                // Mark session as completed
                $session->update([
                    'status'       => 'completed',
                    'completed_at' => now(),
                ]);

                // Update event status to Active
                $event->update(['status' => 'Active']);

                // Log activity
                ActivityLog::create([
                    'event_id' => $event->id,
                    'action' => 'dispatch_completed',
                    'description' => 'Dispatch completed via scan session — ' . $session->scanned_count . ' pieces dispatched.',
                    'user_id' => auth()->id(),
                ]);
            });

            \Log::info('Scan submit successful', [
                'session_id' => $session->id,
                'event_id' => $event->id,
            ]);

            return response()->json([
                'status'     => 'success',
                'code'       => 'DISPATCH_COMPLETE',
                'message'    => 'Dispatch completed successfully.',
                'redirect'   => route('scan.complete', $session->session_token),
                'event_name' => $event->name,
            ]);

        } catch (\Exception $e) {
            \Log::error('Scan submit exception', [
                'token' => $token,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => 'error',
                'code'    => 'SERVER_ERROR',
                'message' => 'An error occurred while completing dispatch. Please try again.',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Save progress without submitting
     */
    public function saveProgress(Request $request, string $token)
    {
        $session = $request->attributes->get('scan_session');

        // Session stays active — pieces already recorded
        // Just return current progress so phone can show summary before exit
        $totalRequested = $session->event->eventItems->sum('quantity_requested');

        return response()->json([
            'status'        => 'success',
            'code'          => 'PROGRESS_SAVED',
            'message'       => 'Progress saved. Resume by scanning the session QR again.',
            'scanned'       => $session->scanned_count,
            'required'      => $totalRequested,
            'session_token' => $session->session_token,
            'expires_at'    => $session->expires_at->toISOString(),
        ]);
    }

    /**
     * Show dispatch complete page (mobile-friendly)
     */
    public function complete(Request $request, string $token)
    {
        $session = $request->attributes->get('scan_session');
        $event   = $session->event;

        return view('scan.complete', compact('session', 'event'));
    }

    /**
     * Get next item suggestion for scanning
     */
    private function getNextSuggestion(ScanSession $session, Event $event): ?array
    {
        // For each item on the packing list, calculate remaining pieces needed
        foreach ($event->eventItems as $eventItem) {
            $scannedForItem = ScanSessionPiece::where('scan_session_id', $session->id)
                ->where('item_id', $eventItem->item_id)
                ->count();

            $remaining = $eventItem->quantity_requested - $scannedForItem;

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
