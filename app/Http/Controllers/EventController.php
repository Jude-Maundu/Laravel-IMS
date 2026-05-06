<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Item;
use App\Models\EventItem;
use App\Models\EventItemImage;
use App\Models\ActivityLog;
use App\Models\EventStaff;
use App\Models\User;
use App\Models\ScanSession;
use App\Models\ReceiveSession;
use App\Models\ReceiveSessionPiece;
use App\Models\MissingItem;
use App\Models\ItemPiece;
use App\Models\EventPieceDispatch;
use App\Models\EventBorrowedItem;
use App\Models\EventOperationalItem;
use App\Services\PieceAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::withCount('eventItems')->with('creator');

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('venue', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        switch ($request->input('sort', 'event_date_desc')) {
            case 'event_date_asc':  $query->orderBy('event_date', 'asc');  break;
            case 'name_asc':        $query->orderBy('name', 'asc');        break;
            case 'created_desc':    $query->orderByDesc('created_at');     break;
            default:                $query->orderByDesc('event_date');     break;
        }

        $events = $query->paginate(15)->withQueryString();

        $statusCounts = Event::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('events.index', compact('events', 'statusCounts'));
    }

    public function requests(Request $request)
    {
        $requests = Event::withCount('eventItems')
            ->with('creator')
            ->whereIn('status', ['Draft', 'Cancelled'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('events.requests', compact('requests'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'client_name'    => 'required|string|max:255',
            'venue'          => 'required|string|max:255',
            'location_name'  => 'nullable|string|max:255',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
            'loading_date'   => 'required|date',
            'setup_date'     => 'required|date|after_or_equal:loading_date',
            'event_date'     => 'required|date|after_or_equal:setup_date',
            'setdown_date'   => 'required|date|after_or_equal:event_date',
            'cost'           => 'nullable|numeric|min:0',
            'amount_due'     => 'nullable|numeric|min:0',
            'customer_phone' => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status']     = 'Draft';

        $event = Event::create($validated);

        return redirect()->route('events.checklist', $event)
            ->with('success', 'Event created successfully. Now select items for the checklist.')
            ->with('toast_sound', 'true');
    }

    public function checklist(Event $event)
    {
        // Load all items grouped by category with pieces relationship
        $itemsByCategory = Item::with('pieces')
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->map(function($item) {
                $item->available_count = $item->pieces()->where('status', 'Available')->count();
                return $item;
            })
            ->groupBy('category');

        // Load active operational items grouped by category
        $operationalItems = \App\Models\OperationalItem::active()->get()->groupBy('category');

        // Load existing event data
        $existingEventItems = $event->eventItems()->get()->keyBy('item_id');
        $existingBorrowedItems = $event->borrowedItems;
        $existingOperationalItems = $event->operationalItems;

        return view('events.checklist', compact(
            'event',
            'itemsByCategory',
            'operationalItems',
            'existingEventItems',
            'existingBorrowedItems',
            'existingOperationalItems'
        ));
    }

    public function saveChecklist(Request $request, Event $event)
    {
        // Validate own inventory items
        $request->validate([
            'items'              => 'nullable|array',
            'items.*.quantity'   => 'nullable|integer|min:1',
        ]);

        // Process own inventory items
        if ($request->has('items') && is_array($request->items)) {
            $event->eventItems()->delete();

            foreach ($request->items as $itemId => $data) {
                $quantity = (int) ($data['quantity'] ?? 1);
                $item = Item::find($itemId);

                if ($item) {
                    $availableCount = $item->pieces()->where('status', 'Available')->count();

                    if ($quantity > $availableCount) {
                        return back()->withErrors([
                            "items.{$itemId}" => "Only {$availableCount} pieces available for {$item->name}"
                        ])->withInput();
                    }

                    EventItem::create([
                        'event_id' => $event->id,
                        'item_id' => $itemId,
                        'quantity_requested' => $quantity,
                    ]);
                }
            }
        }

        // Process borrowed items
        $event->borrowedItems()->delete();
        if ($request->has('borrowed_enabled') && $request->borrowed_enabled) {
            if ($request->has('borrowed') && is_array($request->borrowed)) {
                foreach ($request->borrowed as $borrowed) {
                    if (!empty($borrowed['item_name'])) {
                        \App\Models\EventBorrowedItem::create([
                            'event_id' => $event->id,
                            'item_name' => $borrowed['item_name'],
                            'source_company' => $borrowed['source_company'] ?? null,
                            'quantity_dispatched' => (int) ($borrowed['quantity'] ?? 1),
                            'notes' => $borrowed['notes'] ?? null,
                        ]);
                    }
                }
            }
        }

        // Process operational items
        $event->operationalItems()->delete();
        if ($request->has('operational') && is_array($request->operational)) {
            foreach ($request->operational as $op) {
                \App\Models\EventOperationalItem::create([
                    'event_id' => $event->id,
                    'operational_item_id' => $op['operational_item_id'] ?? null,
                    'custom_name' => $op['custom_name'] ?? null,
                    'quantity_dispatched' => (int) ($op['quantity'] ?? 1),
                ]);
            }
        }

        return redirect()->route('events.team', $event)
            ->with('success', 'Packing list updated successfully.');
    }

    public function dispatch(Event $event)
    {
        $eventItems = $event->eventItems()->with('item')->get();
        return view('events.dispatch', compact('event', 'eventItems'));
    }

    public function confirmDispatch(Request $request, Event $event)
    {
        $request->validate([
            'items'                  => 'required|array',
            'items.*.event_item_id'  => 'required|exists:event_items,id',
            'items.*.condition'      => 'required|integer|between:1,5',
            'items.*.notes'          => 'nullable|string',
        ]);

        foreach ($request->items as $data) {
            $eventItem = EventItem::findOrFail($data['event_item_id']);

            $eventItem->update([
                'condition_on_dispatch' => $data['condition'],
                'dispatch_notes'        => $data['notes'] ?? null,
                'dispatched_at'         => now(),
                'dispatched_by'         => auth()->id(),
            ]);

            $item = $eventItem->item;
            $item->update([
                'status'          => 'Assigned',
                'location'        => $event->venue,
                'last_updated_at' => now(),
                'last_updated_by' => auth()->user()->name,
            ]);

            ActivityLog::create([
                'item_id'     => $item->id,
                'event_id'    => $event->id,
                'action'      => 'dispatched',
                'description' => "Dispatched to event: {$event->name}. Condition: {$eventItem->condition_label}.",
                'user_id'     => auth()->id(),
            ]);
        }

        $event->update(['status' => 'Scheduled']);

        return redirect()->route('events.show', $event)
            ->with('success', count($request->items) . ' item' . (count($request->items) === 1 ? '' : 's') . ' dispatched successfully. Event is now Scheduled.')
            ->with('toast_sound', 'true');
    }

    public function uploadDispatchImage(Request $request, Event $event)
    {
        $request->validate([
            'event_item_id' => 'required|exists:event_items,id',
            'image'         => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
            'type'          => 'in:dispatch,return',
        ]);

        $path = $request->file('image')->store("events/{$event->id}/items", 'public');

        $image = EventItemImage::create([
            'event_item_id' => $request->event_item_id,
            'image_path'    => $path,
            'type'          => $request->input('type', 'dispatch'),
            'uploaded_by'   => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'id'      => $image->id,
            'url'     => $image->url,
        ]);
    }

    public function receive(Event $event)
    {
        if (!in_array($event->status, ['Set Down', 'Completed'])) {
            return redirect()->route('events.show', $event)
                ->with('error', 'Items can only be received after the event is set down.');
        }

        $eventItems = $event->eventItems()
            ->with(['item', 'images'])
            ->where('return_processed', false)
            ->whereNotNull('dispatched_at')
            ->get();

        $processedItems = $event->eventItems()
            ->with(['item'])
            ->where('return_processed', true)
            ->get();

        return view('events.receive', compact('event', 'eventItems', 'processedItems'));
    }

    public function processReceive(Request $request, Event $event)
    {
        $request->validate([
            'items'                        => 'required|array|min:1',
            'items.*.event_item_id'        => 'required|exists:event_items,id',
            'items.*.destination'          => 'required|in:warehouse,cleaning,repair',
            'items.*.condition_on_return'  => 'required|integer|between:1,5',
            'items.*.return_notes'         => 'nullable|string|max:500',
        ]);

        foreach ($request->items as $data) {
            $eventItem = \App\Models\EventItem::with('item')->findOrFail($data['event_item_id']);
            $item      = $eventItem->item;
            $dest      = $data['destination'];

            $newStatus   = match($dest) {
                'warehouse' => 'Available',
                'cleaning'  => 'Cleaning',
                'repair'    => 'Under Repair',
            };
            $newLocation = match($dest) {
                'warehouse' => 'Warehouse',
                'cleaning'  => 'Cleaning Bay',
                'repair'    => 'Repair Workshop',
            };

            $eventItem->update([
                'condition_on_return'  => $data['condition_on_return'],
                'return_notes'         => $data['return_notes'] ?? null,
                'return_destination'   => $dest,
                'returned_at'          => now(),
                'returned_by'          => auth()->id(),
                'return_processed'     => true,
            ]);

            $item->update([
                'status'          => $newStatus,
                'location'        => $newLocation,
                'assigned_to'     => null,
                'last_updated_at' => now(),
                'last_updated_by' => auth()->user()->name,
            ]);

            if ($dest === 'repair') {
                \App\Models\Repair::create([
                    'item_id'         => $item->id,
                    'repair_type'     => 'Post-Event Damage',
                    'description'     => $data['return_notes'] ?? 'Damage found on return from event: ' . $event->name,
                    'status'          => 'Pending',
                    'started_at'      => now()->toDateString(),
                    'estimated_cost'  => 0,
                ]);
            }

            \App\Models\ActivityLog::create([
                'item_id'     => $item->id,
                'event_id'    => $event->id,
                'action'      => 'returned',
                'description' => "Returned from event: {$event->name}. Destination: {$newStatus}. Condition: " . ($data['condition_on_return']) . "/5.",
                'user_id'     => auth()->id(),
            ]);
        }

        $remaining = $event->eventItems()->where('return_processed', false)->count();
        if ($remaining === 0) {
            $event->update(['status' => 'Completed']);
        }

        $count = count($request->items);
        $message = "{$count} item" . ($count > 1 ? 's' : '') . " processed successfully.";
        if ($remaining === 0) {
            $message .= ' All items received. Event marked as Completed.';
        } else {
            $message .= " {$remaining} item" . ($remaining === 1 ? '' : 's') . " still pending.";
        }

        return redirect()->route('events.receive', $event)
            ->with('success', $message)
            ->with('toast_sound', $remaining === 0 ? 'true' : 'false');
    }

    public function uploadReturnImage(Request $request, Event $event)
    {
        $request->validate([
            'event_item_id' => 'required|exists:event_items,id',
            'image'         => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $path = $request->file('image')->store("events/{$event->id}/returns", 'public');

        $image = \App\Models\EventItemImage::create([
            'event_item_id' => $request->event_item_id,
            'image_path'    => $path,
            'type'          => 'return',
            'uploaded_by'   => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'id'      => $image->id,
            'url'     => asset('storage/' . $path),
        ]);
    }

    public function show(Event $event)
    {
        $event->load(['eventItems.item', 'eventItems.images', 'creator', 'linkedFromEvent', 'linkedEvents']);
        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'client_name'   => 'required|string|max:255',
            'venue'         => 'required|string|max:255',
            'location_name' => 'nullable|string|max:255',
            'loading_date'  => 'required|date',
            'setup_date'    => 'required|date',
            'event_date'    => 'required|date',
            'setdown_date'  => 'required|date',
            'cost'          => 'nullable|numeric|min:0',
            'notes'         => 'nullable|string',
            'status'        => 'nullable|in:Draft,Scheduled,Active,Set Down,Completed,Cancelled',
        ]);

        $oldStatus = $event->status;
        $statusChanged = false;

        if (isset($validated['status']) && $validated['status'] !== $oldStatus) {
            $event->status = $validated['status'];
            $statusChanged = true;
        }

        $event->update($validated);

        $message = 'Event "' . $event->name . '" updated successfully.';
        if ($statusChanged) {
            $message = 'Event status changed from "' . $oldStatus . '" to "' . $validated['status'] . '" successfully.';
        }

        return redirect()->route('events.show', $event)
            ->with('success', $message)
            ->with('toast_sound', $statusChanged ? 'true' : 'false');
    }

    public function destroy(Event $event)
    {
        // Use transaction to ensure all items are reverted atomically
        DB::transaction(function () use ($event) {
            $itemsReverted = 0;

            // Revert ALL items associated with this event back to warehouse
            foreach ($event->eventItems as $eventItem) {
                if ($eventItem->item) {
                    $item = $eventItem->item;

                    // Only revert items that are currently assigned to this event or trapped
                    // Skip items that have already been properly returned to warehouse
                    if (in_array($item->status, ['Assigned', 'In Use', 'Set Down', 'Cleaning', 'Under Repair'])) {
                        $item->update([
                            'status'          => 'Available',
                            'location'        => 'Warehouse',
                            'assigned_to'     => null,
                            'last_updated_at' => now(),
                            'last_updated_by' => auth()->user()->name ?? 'System',
                        ]);

                        // Log the reversion
                        ActivityLog::create([
                            'item_id'     => $item->id,
                            'event_id'    => $event->id,
                            'action'      => 'returned',
                            'description' => "Automatically returned to warehouse due to event deletion: {$event->name}",
                            'user_id'     => auth()->id(),
                        ]);

                        $itemsReverted++;
                    }
                }
            }

            // Delete all event items
            $event->eventItems()->delete();

            // Delete event staff assignments
            $event->staff()->detach();

            // Delete the event itself
            $event->delete();
        });

        return redirect()->route('events.index')
            ->with('success', "Event '{$event->name}' deleted successfully. All associated items have been returned to warehouse.")
            ->with('toast_sound', 'true');
    }

    public function team(Event $event)
    {
        $assignedStaff = $event->staff()->withPivot('role')->get();
        $availableUsers = User::whereNotIn('id', $assignedStaff->pluck('id'))
            ->orderBy('name')
            ->select('id', 'name', 'email')
            ->get();
        return view('events.team', compact('event', 'assignedStaff', 'availableUsers'));
    }

    public function teamSearch(Request $request, Event $event)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where(function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%");
        })
        ->select('id', 'name', 'email')
        ->limit(10)
        ->get();

        return response()->json($users);
    }

    public function teamStore(Request $request, Event $event)
    {
        $request->validate([
            'team'          => 'required|array',
            'team.*.user_id' => 'required|exists:users,id',
            'team.*.role'    => 'required|in:member,leader',
        ]);

        $leaderCount = collect($request->team)->where('role', 'leader')->count();
        if ($leaderCount > 1) {
            return response()->json([
                'success' => false,
                'message' => 'Only one team leader can be assigned.'
            ], 422);
        }

        EventStaff::where('event_id', $event->id)->delete();

        foreach ($request->team as $member) {
            EventStaff::create([
                'event_id' => $event->id,
                'user_id'  => $member['user_id'],
                'role'     => $member['role'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => count($request->team) . ' staff members assigned.'
        ]);
    }

    /**
     * Show site-to-site linking wizard
     */
    public function siteToSiteWizard(Event $event)
    {
        // Ensure event is in Set Down status and has items not yet received
        if ($event->status !== 'Set Down') {
            return redirect()->route('events.show', $event)
                ->with('error', 'Site-to-site linking is only available for events in Set Down status.');
        }

        $pendingItems = $event->eventItems()
            ->with('item')
            ->where('return_processed', false)
            ->whereNotNull('dispatched_at')
            ->get();

        if ($pendingItems->count() === 0) {
            return redirect()->route('events.show', $event)
                ->with('error', 'No items available for site-to-site linking. All items have been received.');
        }

        // Get all available items from warehouse for additional selection
        $categories = Item::where('status', 'Available')
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        // Pre-select items from current event
        $preSelectedIds = $pendingItems->pluck('item_id')->toArray();

        return view('events.site_to_site_wizard', compact('event', 'categories', 'preSelectedIds', 'pendingItems'));
    }

    /**
     * Create linked event from site-to-site
     */
    public function createSiteToSite(Request $request, Event $event)
    {
        // Session-based lock to prevent double submission
        $lockKey = "creating_s2s_for_event_{$event->id}";

        if (session()->has($lockKey)) {
            $lockedEventId = session()->get($lockKey);
            $lockedEvent = Event::find($lockedEventId);

            if ($lockedEvent) {
                return redirect()->route('events.team', $lockedEvent)
                    ->with('success', "Site-to-site event '{$lockedEvent->name}' was already created. Now assign the team.");
            }
        }

        // Check if a linked event was recently created (within last 10 seconds) to prevent double submission
        $recentLinked = Event::where('linked_from_event_id', $event->id)
            ->where('link_type', 'site-to-site')
            ->where('created_at', '>=', now()->subSeconds(10))
            ->first();

        if ($recentLinked) {
            session()->put($lockKey, $recentLinked->id);
            return redirect()->route('events.team', $recentLinked)
                ->with('success', "Site-to-site event '{$recentLinked->name}' was already created. Now assign the team.");
        }

        // Validate the request
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'client_name'   => 'required|string|max:255',
            'venue'         => 'required|string|max:255',
            'location_name' => 'nullable|string|max:255',
            'latitude'      => 'nullable|numeric|between:-90,90',
            'longitude'     => 'nullable|numeric|between:-180,180',
            'loading_date'  => 'required|date',
            'setup_date'    => 'required|date|after_or_equal:loading_date',
            'event_date'    => 'required|date|after_or_equal:setup_date',
            'setdown_date'  => 'required|date|after_or_equal:event_date',
            'cost'          => 'nullable|numeric|min:0',
            'notes'         => 'nullable|string',
            'item_ids'      => 'required|array|min:1',
            'item_ids.*'    => 'exists:items,id',
            'items_to_return' => 'nullable|array',
            'items_to_return.*' => 'exists:event_items,id',
        ]);

        // Use database transaction to ensure atomicity and prevent duplicate submissions
        $result = DB::transaction(function () use ($validated, $event, $request) {
            // Create the new linked event
            $validated['created_by'] = auth()->id();
            $validated['status'] = 'Draft';
            $validated['linked_from_event_id'] = $event->id;
            $validated['link_type'] = 'site-to-site';

            $linkedEvent = Event::create($validated);

            // Process items to return to warehouse
            if (!empty($request->items_to_return)) {
                foreach ($request->items_to_return as $eventItemId) {
                    $eventItem = EventItem::with('item')->findOrFail($eventItemId);
                    $item = $eventItem->item;

                    // Mark as returned to warehouse
                    $eventItem->update([
                        'condition_on_return'  => 5, // Default to excellent for auto-processing
                        'return_notes'         => 'Returned to warehouse (not needed for site-to-site transfer)',
                        'return_destination'   => 'warehouse',
                        'returned_at'          => now(),
                        'returned_by'          => auth()->id(),
                        'return_processed'     => true,
                    ]);

                    $item->update([
                        'status'          => 'Available',
                        'location'        => 'Warehouse',
                        'assigned_to'     => null,
                        'last_updated_at' => now(),
                        'last_updated_by' => auth()->user()->name,
                    ]);

                    ActivityLog::create([
                        'item_id'     => $item->id,
                        'event_id'    => $event->id,
                        'action'      => 'returned',
                        'description' => "Returned to warehouse from {$event->name} (excluded from site-to-site link to {$linkedEvent->name})",
                        'user_id'     => auth()->id(),
                    ]);
                }
            }

            // Remove duplicates from item_ids (items may appear in both current event and warehouse sections)
            $itemIds = array_unique($request->item_ids);

            // Get items from current event that are being transferred
            $currentEventItemIds = $event->eventItems()
                ->where('return_processed', false)
                ->pluck('item_id')
                ->toArray();

            // Filter and validate items
            $validItemIds = array_filter($itemIds, function($id) use ($currentEventItemIds) {
                $item = Item::find($id);
                if (!$item) {
                    return false;
                }

                // Allow items from current event (being transferred) OR items that are available
                if (in_array($id, $currentEventItemIds)) {
                    return true; // Item is being transferred from current event
                }

                return $item->isAvailableForDispatch(); // New item from warehouse
            });

            // Create event items for the new linked event (avoiding duplicates)
            foreach ($validItemIds as $itemId) {
                // Check if this item is already in the event (shouldn't happen but just in case)
                $exists = EventItem::where('event_id', $linkedEvent->id)
                    ->where('item_id', $itemId)
                    ->exists();

                if (!$exists) {
                    EventItem::create([
                        'event_id' => $linkedEvent->id,
                        'item_id'  => $itemId,
                    ]);

                    // If this item is from the current event, mark it as processed (transferred)
                    if (in_array($itemId, $currentEventItemIds)) {
                        $parentEventItem = $event->eventItems()->where('item_id', $itemId)->first();
                        if ($parentEventItem) {
                            $parentEventItem->update([
                                'return_processed'    => true,
                                'return_destination'  => 'site-to-site',
                                'return_notes'        => "Transferred to linked event: {$linkedEvent->name}",
                                'returned_at'         => now(),
                                'returned_by'         => auth()->id(),
                            ]);
                        }
                    }
                }
            }

            // Check if parent event should be completed
            $remainingParent = $event->eventItems()->where('return_processed', false)->count();
            if ($remainingParent === 0) {
                $event->update(['status' => 'Completed']);
            }

            // Log the site-to-site link creation
            ActivityLog::create([
                'item_id'     => null,
                'event_id'    => $event->id,
                'action'      => 'site_to_site_link',
                'description' => "Site-to-site link created: {$event->name} → {$linkedEvent->name}. " . count($validItemIds) . " items transferred.",
                'user_id'     => auth()->id(),
            ]);

            return ['event' => $linkedEvent, 'itemCount' => count($validItemIds)];
        });

        // Set session lock with the created event ID
        session()->put($lockKey, $result['event']->id);

        // Clear the lock after 30 seconds (in case user doesn't complete the flow)
        session()->put("{$lockKey}_expires", now()->addSeconds(30)->timestamp);

        return redirect()->route('events.team', $result['event'])
            ->with('success', "Site-to-site event '{$result['event']->name}' created successfully with {$result['itemCount']} item" . ($result['itemCount'] === 1 ? '' : 's') . ". Now assign the team.")
            ->with('toast_sound', 'true');
    }

    /**
     * Step 4 — Review & Confirm
     */
    public function review(Event $event)
    {
        $event->load([
            'eventItems.item',
            'borrowedItems',
            'operationalItems.operationalItem',
            'staff'
        ]);

        $planRef = \App\Models\Event::generatePlanRef();
        $totalOwnItems = $event->eventItems()->count();
        $totalBorrowedItems = $event->borrowedItems()->count();
        $totalOperationalItems = $event->operationalItems()->count();

        return view('events.review', compact(
            'event',
            'planRef',
            'totalOwnItems',
            'totalBorrowedItems',
            'totalOperationalItems'
        ));
    }

    /**
     * Confirm & Schedule Event
     */
    public function confirm(Request $request, Event $event)
    {
        $planRef = \App\Models\Event::generatePlanRef();

        $event->update([
            'status' => 'Awaiting Payment',
            'plan_ref' => $planRef,
        ]);

        ActivityLog::create([
            'item_id' => null,
            'event_id' => $event->id,
            'action' => 'event_scheduled',
            'description' => "Event scheduled — packing list confirmed. Plan ref: {$planRef}",
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('events.show', $event)
            ->with('success', 'Event scheduled successfully.')
            ->with('toast_sound', 'true');
    }

    /**
     * Generate Planning Packing List PDF
     */
    public function planningPackingList(Event $event)
    {
        $event->load([
            'eventItems.item.pieces',
            'eventItems.item.primaryImage',
            'borrowedItems',
            'operationalItems.operationalItem'
        ]);

        $planRef = $event->plan_ref ?? \App\Models\Event::generatePlanRef();

        $pdf = \PDF::loadView('reports.planning_packing_list', compact('event', 'planRef'));
        $filename = 'PLANNING-DRAFT-' . strtoupper($event->name) . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Generate Dispatch Note PDF
     */
    public function dispatchNote(Event $event)
    {
        // Load all dispatched items
        $event->load([
            'eventItems.item',
            'borrowedItems',
            'operationalItems.operationalItem'
        ]);

        // Get dispatched items only
        $dispatchedItems = $event->eventItems->filter(function($eventItem) {
            return $eventItem->dispatched_at && $eventItem->quantity_dispatched > 0;
        });

        // Calculate totals
        $totalPiecesDispatched = $dispatchedItems->sum('quantity_dispatched');
        $totalItems = $dispatchedItems->count();
        $totalBorrowed = $event->borrowedItems->count();
        $totalOperational = $event->operationalItems->count();

        // Generate dispatch reference
        $dispatchRef = 'DISP-' . now()->format('Y-m') . '-' . str_pad($event->id, 3, '0', STR_PAD_LEFT);

        // Get dispatch date (from first dispatched item or first completed scan session)
        $dispatchedDate = $dispatchedItems->first()?->dispatched_at?->format('D, j M Y H:i') ?? now()->format('D, j M Y H:i');

        $pdf = \PDF::loadView('reports.dispatch_note', compact(
            'event',
            'dispatchedItems',
            'dispatchRef',
            'dispatchedDate',
            'totalPiecesDispatched',
            'totalItems',
            'totalBorrowed',
            'totalOperational'
        ));

        $filename = 'DISPATCH-NOTE-' . strtoupper($event->name) . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Start scan session for dispatch
     */
    public function scanStart(Request $request, Event $event)
    {
        // Validate event status
        if (!in_array($event->status, ['Scheduled', 'Active'])) {
            return redirect()->route('events.show', $event)
                ->with('error', 'Events can only be dispatched when Scheduled or Active.');
        }

        // Check if this is an additional dispatch
        $isAdditional = session('is_additional_dispatch', false);
        $dispatchBatch = 1;

        if ($isAdditional) {
            // Get current max batch and increment
            $maxBatch = $event->eventItems()
                ->whereNotNull('dispatched_at')
                ->max('dispatch_batch') ?? 0;
            $dispatchBatch = $maxBatch + 1;
        }

        // Cancel any existing active or cancelled sessions for this event
        // This ensures a completely fresh start
        ScanSession::where('event_id', $event->id)
            ->whereIn('status', ['active', 'cancelled'])
            ->update(['status' => 'cancelled']);

        // Create new scan session
        $session = ScanSession::create([
            'event_id' => $event->id,
            'session_token' => ScanSession::generateToken(),
            'created_by' => auth()->id(),
            'expires_at' => now()->addHours(4),
            'status' => 'active',
            'dispatch_batch' => $dispatchBatch,
        ]);

        // Generate dispatch reference and store in session
        $dispatchRef = Event::generateDispatchRef();
        session(['dispatch_ref_' . $event->id => $dispatchRef]);
        session(['current_dispatch_batch' => $dispatchBatch]);

        // Clear any wizard state to ensure it shows on the new session
        session()->forget('dispatch_wizard_seen_' . $event->id);

        return redirect()->route('events.scan.monitor', [$event, $session])
            ->with('success', 'New scan session started successfully.');
    }

    /**
     * Monitor scan session
     */
    public function scanMonitor(Event $event, ScanSession $scanSession)
    {
        // Validate session belongs to this event
        if ($scanSession->event_id !== $event->id) {
            abort(404);
        }

        // Validate session is valid (active and not expired)
        if (!$scanSession->isValid()) {
            $message = $scanSession->status === 'cancelled'
                ? 'This scan session was cancelled. Please start a new one.'
                : 'This scan session has expired.';

            return redirect()->route('events.show', $event)
                ->with('error', $message);
        }

        $event->load([
            'eventItems.item',
            'borrowedItems',
            'operationalItems.operationalItem'
        ]);

        $dispatchRef = session('dispatch_ref_' . $event->id);

        // Generate QR code for modal
        $sessionUrl = config('app.url') . '/scan/' . $scanSession->session_token;
        $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(180)
            ->errorCorrection('H')
            ->generate($sessionUrl);

        return view('events.scan-monitor-premium', compact('event', 'scanSession', 'dispatchRef', 'qrCodeSvg'));
    }

    /**
     * Extend scan session
     */
    public function scanExtend(Event $event, ScanSession $scanSession)
    {
        if ($scanSession->event_id !== $event->id) {
            return response()->json(['success' => false, 'message' => 'Invalid session'], 403);
        }

        $scanSession->update([
            'expires_at' => now()->addHours(4),
        ]);

        return response()->json([
            'success' => true,
            'expires_at' => $scanSession->expires_at->toISOString(),
        ]);
    }

    /**
     * Cancel scan session
     */
    public function scanCancel(Event $event, ScanSession $scanSession)
    {
        if ($scanSession->event_id !== $event->id) {
            abort(404);
        }

        $scanSession->update(['status' => 'cancelled']);

        return redirect()->route('events.show', $event)
            ->with('info', 'Scan session cancelled. You can start a new dispatch session anytime.');
    }

    /**
     * Undo last scan in session
     */
    public function scanUndo(Event $event, ScanSession $scanSession)
    {
        if ($scanSession->event_id !== $event->id) {
            return response()->json(['success' => false, 'message' => 'Invalid session'], 403);
        }

        // Get the most recent scan
        $lastScan = \App\Models\ScanSessionPiece::where('scan_session_id', $scanSession->id)
            ->orderByDesc('scanned_at')
            ->first();

        if (!$lastScan) {
            return response()->json(['success' => false, 'message' => 'No scans to undo'], 400);
        }

        try {
            \DB::transaction(function () use ($lastScan, $scanSession) {
                // Revert piece status to Available
                $lastScan->itemPiece->update([
                    'status' => 'Available',
                    'current_event_id' => null,
                ]);

                // Delete event_piece_dispatches record
                \App\Models\EventPieceDispatch::where('event_id', $scanSession->event_id)
                    ->where('item_piece_id', $lastScan->item_piece_id)
                    ->delete();

                // Decrement quantities
                $scanSession->decrement('scanned_count');

                \App\Models\EventItem::where('event_id', $scanSession->event_id)
                    ->where('item_id', $lastScan->item_id)
                    ->decrement('quantity_dispatched');

                // Delete scan record
                $lastScan->delete();

                // Log activity
                ActivityLog::create([
                    'event_id' => $scanSession->event_id,
                    'action' => 'scan_undone',
                    'description' => 'Undid scan of piece: ' . $lastScan->unique_code,
                    'user_id' => auth()->id(),
                ]);
            });

            return response()->json([
                'success' => true,
                'undone_code' => $lastScan->unique_code,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to undo scan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Final dispatch confirmation from monitor dashboard
     */
    public function scanConfirmDispatch(Request $request, Event $event, ScanSession $scanSession)
    {
        if ($scanSession->event_id !== $event->id) {
            return response()->json(['success' => false, 'message' => 'Invalid session'], 403);
        }

        if ($scanSession->status === 'completed') {
            return response()->json(['success' => false, 'message' => 'Session already completed'], 400);
        }

        $event->load('eventItems', 'borrowedItems', 'operationalItems');

        $borrowedConfirmed = $request->input('borrowed_confirmed', []);
        $operationalConfirmed = $request->input('operational_confirmed', []);

        $totalRequested = $event->eventItems->sum('quantity_requested');
        $totalScanned   = $scanSession->scanned_count;

        if ($totalScanned < $totalRequested) {
            return response()->json([
                'success' => false,
                'message' => 'Not all items have been scanned yet.',
            ], 422);
        }

        $borrowedCount = $event->borrowedItems->count();
        $operationalCount = $event->operationalItems->count();

        if ($borrowedCount > 0 && count($borrowedConfirmed) !== $borrowedCount) {
            return response()->json([
                'success' => false,
                'message' => 'Please confirm all borrowed items before completing dispatch.',
            ], 422);
        }

        if ($operationalCount > 0 && count($operationalConfirmed) !== $operationalCount) {
            return response()->json([
                'success' => false,
                'message' => 'Please confirm all operational items before completing dispatch.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $scanSession->update([
                'status'       => 'completed',
                'completed_at' => now(),
            ]);

            $event->update(['status' => 'Active']);

            ActivityLog::create([
                'event_id' => $event->id,
                'action' => 'dispatch_completed',
                'description' => 'Dispatch completed via scan session — ' . $scanSession->scanned_count . ' pieces, ' . count($borrowedConfirmed) . ' borrowed items, ' . count($operationalConfirmed) . ' operational items confirmed.',
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while completing dispatch.',
            ], 500);
        }
    }

    /**
     * Show manual dispatch page
     */
    public function manualDispatch(Event $event)
    {
        $event->load([
            'eventItems.item',
            'borrowedItems',
            'operationalItems.operationalItem'
        ]);

        $operationalItems = \App\Models\OperationalItem::active()->get();

        return view('events.manual-dispatch', compact('event', 'operationalItems'));
    }

    /**
     * Process manual dispatch
     */
    public function manualDispatchStore(Request $request, Event $event)
    {
        $request->validate([
            'dispatched_pieces' => 'nullable|array',
            'dispatched_pieces.*' => 'nullable|array',
            'conditions' => 'nullable|array',
            'conditions.*' => 'nullable|integer|between:1,5',
            'dispatch_notes' => 'nullable|array',
            'dispatch_notes.*' => 'nullable|string',
            'borrowed_confirmed' => 'nullable|array',
            'borrowed_confirmed.*' => 'nullable|integer|min:0',
            'operational_confirmed' => 'nullable|array',
            'operational_confirmed.*' => 'nullable|integer|min:0',
        ]);

        $service = new PieceAvailabilityService();
        $errors = [];
        $validPiecesPerItem = [];

        // Process own inventory pieces
        if ($request->has('dispatched_pieces')) {
            foreach ($request->dispatched_pieces as $itemId => $pieceCodes) {
                if (!is_array($pieceCodes) || empty($pieceCodes)) {
                    continue;
                }

                $validPieces = [];

                // Validate all pieces for this item
                foreach ($pieceCodes as $code) {
                    $code = trim($code);
                    if (empty($code)) {
                        continue;
                    }

                    $result = $service->validatePieceForDispatch($code, $itemId);
                    if (!$result['valid']) {
                        $errors["dispatched_pieces.{$itemId}"][] = "Piece {$code}: " . $result['message'];
                    } else {
                        $validPieces[] = $result['piece'];
                    }
                }

                // If validation passed, store valid pieces for later dispatch
                if (empty($errors["dispatched_pieces.{$itemId}"] ?? [])) {
                    $validPiecesPerItem[$itemId] = collect($validPieces);
                }
            }
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        // Determine dispatch batch
        $isAdditional = session('is_additional_dispatch', false);
        $dispatchBatch = 1;

        if ($isAdditional) {
            $maxBatch = $event->eventItems()->whereNotNull('dispatched_at')->max('dispatch_batch') ?? 0;
            $dispatchBatch = $maxBatch + 1;
            session()->forget('is_additional_dispatch');
        }

        // Dispatch all valid pieces
        foreach ($validPiecesPerItem as $itemId => $pieces) {
            $condition = $request->conditions[$itemId] ?? 5;
            $notes = $request->dispatch_notes[$itemId] ?? null;

            try {
                $service->dispatchPieces($pieces, $event->id, $condition, auth()->id());

                // Update event_items quantity_dispatched
                $eventItem = EventItem::where('event_id', $event->id)
                    ->where('item_id', $itemId)
                    ->first();

                if ($eventItem) {
                    $eventItem->update([
                        'quantity_dispatched' => $pieces->count(),
                        'condition_on_dispatch' => $condition,
                        'dispatch_notes' => $notes,
                        'dispatched_at' => now(),
                        'dispatched_by' => auth()->id(),
                        'dispatch_batch' => $dispatchBatch,
                    ]);
                }
            } catch (\Exception $e) {
                return back()->withErrors([
                    "dispatched_pieces.{$itemId}" => $e->getMessage()
                ])->withInput();
            }
        }

        // Process borrowed items
        if ($request->has('borrowed_confirmed')) {
            foreach ($request->borrowed_confirmed as $id => $qty) {
                $borrowedItem = \App\Models\EventBorrowedItem::find($id);
                if ($borrowedItem && $borrowedItem->event_id === $event->id) {
                    $borrowedItem->update(['quantity_dispatched' => (int) $qty]);
                }
            }
        }

        // Process operational items
        if ($request->has('operational_confirmed')) {
            foreach ($request->operational_confirmed as $id => $qty) {
                $opItem = \App\Models\EventOperationalItem::find($id);
                if ($opItem && $opItem->event_id === $event->id) {
                    $opItem->update(['quantity_dispatched' => (int) $qty]);
                }
            }
        }

        // Update event status to Active
        $event->update(['status' => 'Active']);

        // Log activity
        $itemCount = count($validPiecesPerItem);
        ActivityLog::create([
            'item_id' => null,
            'event_id' => $event->id,
            'action' => 'dispatch_completed',
            'description' => "Dispatch completed — {$itemCount} item type(s) dispatched manually for event: {$event->name}",
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('events.show', $event)
            ->with('success', 'Dispatch completed successfully.')
            ->with('toast_sound', 'true');
    }

    /**
     * Store additional dispatch items
     */
    public function dispatchAdditionalStore(Request $request, Event $event)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*' => 'exists:items,id',
        ]);

        // Get current max batch number
        $maxBatch = $event->eventItems()
            ->whereNotNull('dispatched_at')
            ->max('dispatch_batch') ?? 0;
        $newBatch = $maxBatch + 1;

        // Store selected items in session for dispatch modal
        session([
            'additional_dispatch_items' => $request->items,
            'additional_dispatch_batch' => $newBatch,
            'additional_dispatch_event_id' => $event->id,
        ]);

        // Redirect to dispatch modal selection (scan or manual)
        return redirect()->route('events.show', $event)
            ->with('open_dispatch_modal', true)
            ->with('is_additional_dispatch', true);
    }

    /**
     * Validate piece code via API
     */
    public function validatePiece(Request $request)
    {
        $code = $request->input('code');
        $itemId = $request->input('item_id');

        $service = new PieceAvailabilityService();
        $result = $service->validatePieceForDispatch($code, $itemId);

        if ($result['valid']) {
            return response()->json([
                'valid' => true,
                'message' => $result['message'],
                'piece' => [
                    'unique_code' => $result['piece']->unique_code,
                    'status' => $result['piece']->status,
                ],
            ]);
        }

        return response()->json([
            'valid' => false,
            'message' => $result['message'],
            'piece' => null,
        ]);
    }

    /**
     * Get scan progress via API
     */
    public function scanProgress(Event $event, ScanSession $scanSession)
    {
        if ($scanSession->event_id !== $event->id) {
            return response()->json(['error' => 'Invalid session'], 403);
        }

        // CRITICAL: Force fresh data from database - no caching
        \DB::connection()->disableQueryLog();
        $scanSession = ScanSession::find($scanSession->id);

        if (!$scanSession) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        $event->load('eventItems.item');
        $totalRequested = $event->eventItems->sum('quantity_requested');

        // Optimized: Use raw count query for per-item progress
        $itemProgress = $event->eventItems->map(function ($eventItem) use ($scanSession) {
            // Direct count query - fastest method
            $scannedCount = \DB::table('scan_session_pieces')
                ->where('scan_session_id', $scanSession->id)
                ->where('item_id', $eventItem->item_id)
                ->count();

            return [
                'item_id'      => $eventItem->item_id,
                'item_name'    => $eventItem->item->name,
                'required'     => $eventItem->quantity_requested,
                'scanned'      => $scannedCount,
                'complete'     => $scannedCount >= $eventItem->quantity_requested,
            ];
        });

        $response = [
            'scanned_count'  => $scanSession->scanned_count,
            'total_pieces'   => $totalRequested,
            'percentage'     => $totalRequested > 0
                                ? round(($scanSession->scanned_count / $totalRequested) * 100)
                                : 0,
            'session_valid'  => $scanSession->isValid(),
            'session_status' => $scanSession->status,
            'item_progress'  => $itemProgress,
            'all_complete'   => $scanSession->scanned_count >= $totalRequested,
            'timestamp'      => now()->timestamp,
        ];

        // No caching headers - force fresh data
        return response()->json($response)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Server-Sent Events endpoint for real-time scan updates
     */
    public function scanProgressStream(Event $event, ScanSession $scanSession)
    {
        if ($scanSession->event_id !== $event->id) {
            abort(403);
        }

        return response()->stream(function () use ($event, $scanSession) {
            // Send SSE headers
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no'); // Disable nginx buffering

            $lastCount = 0;

            while (true) {
                // Fresh data from database
                $scanSession = $scanSession->fresh();

                if (!$scanSession || $scanSession->status === 'completed' || $scanSession->status === 'cancelled') {
                    echo "event: session_ended\n";
                    echo "data: {\"status\": \"" . ($scanSession->status ?? 'unknown') . "\"}\n\n";
                    ob_flush();
                    flush();
                    break;
                }

                // Only send update if count changed
                if ($scanSession->scanned_count !== $lastCount) {
                    $event->load('eventItems.item');
                    $totalRequested = $event->eventItems->sum('quantity_requested');

                    $itemProgress = $event->eventItems->map(function ($eventItem) use ($scanSession) {
                        $scannedCount = \App\Models\ScanSessionPiece::where('scan_session_id', $scanSession->id)
                            ->where('item_id', $eventItem->item_id)
                            ->count();

                        return [
                            'item_id'  => $eventItem->item_id,
                            'item_name'=> $eventItem->item->name,
                            'required' => $eventItem->quantity_requested,
                            'scanned'  => $scannedCount,
                            'complete' => $scannedCount >= $eventItem->quantity_requested,
                        ];
                    });

                    $data = [
                        'scanned_count' => $scanSession->scanned_count,
                        'total_pieces'  => $totalRequested,
                        'percentage'    => $totalRequested > 0 ? round(($scanSession->scanned_count / $totalRequested) * 100) : 0,
                        'item_progress' => $itemProgress,
                        'all_complete'  => $scanSession->scanned_count >= $totalRequested,
                    ];

                    echo "data: " . json_encode($data) . "\n\n";
                    ob_flush();
                    flush();

                    $lastCount = $scanSession->scanned_count;
                }

                // Check every 500ms
                usleep(500000);

                // Check connection status
                if (connection_aborted()) {
                    break;
                }
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Generate dispatch packing list PDF with QR code
     */
    public function dispatchPackingList(Event $event, ScanSession $scanSession, Request $request)
    {
        \Log::info('Dispatch Packing List called', [
            'event_id' => $event->id,
            'session_id' => $scanSession->id,
        ]);

        if ($scanSession->event_id !== $event->id) {
            \Log::error('Event/Session mismatch', [
                'event_id' => $event->id,
                'session_event_id' => $scanSession->event_id,
            ]);
            abort(404);
        }

        $type = $request->get('type', 'full'); // 'full' or 'additional'

        // Load event items based on type
        if ($type === 'additional') {
            // Get max batch number (latest additional dispatch)
            $maxBatch = $event->eventItems()->whereNotNull('dispatched_at')->max('dispatch_batch') ?? 1;
            $event->load([
                'eventItems' => function ($query) use ($maxBatch) {
                    $query->where('dispatch_batch', $maxBatch)->with('item.pieces', 'item.primaryImage');
                },
                'borrowedItems',
                'operationalItems.operationalItem'
            ]);
        } else {
            $event->load([
                'eventItems.item.pieces',
                'eventItems.item.primaryImage',
                'borrowedItems',
                'operationalItems.operationalItem'
            ]);
        }

        // Group items by batch for display
        $batches = $event->eventItems()
            ->whereNotNull('dispatched_at')
            ->select('dispatch_batch', \DB::raw('MIN(dispatched_at) as batch_date'), \DB::raw('MIN(dispatched_by) as batch_user'))
            ->groupBy('dispatch_batch')
            ->orderBy('dispatch_batch')
            ->get();

        $dispatchRef = session('dispatch_ref_' . $event->id) ?? Event::generateDispatchRef();

        // Generate QR code for scan session URL (use config URL for consistency)
        $sessionUrl = config('app.url') . '/scan/' . $scanSession->session_token;
        $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(180)
            ->errorCorrection('H')
            ->generate($sessionUrl);

        $sessionExpiresAt = $scanSession->expires_at->format('d M Y \a\t H:i') . ' EAT';

        $pdf = \PDF::loadView('reports.dispatch_packing_list', compact(
            'event',
            'dispatchRef',
            'scanSession',
            'sessionUrl',
            'qrCodeSvg',
            'sessionExpiresAt',
            'type',
            'batches'
        ));

        $filename = $type === 'full'
            ? 'DISPATCH-FINAL-' . strtoupper($event->name) . '.pdf'
            : 'DISPATCH-ADDITIONAL-' . strtoupper($event->name) . '.pdf';

        return $pdf->download($filename);
    }

    // =====================================================================
    // RECEIVE SESSION METHODS
    // =====================================================================

    public function receiveStart(Request $request, Event $event)
    {
        // Validate event is in correct status
        if (!in_array($event->status, ['Set Down', 'Active'])) {
            return back()->with('error', 'This event is not ready for receiving.');
        }

        // Cancel any existing active receive sessions for this event
        ReceiveSession::where('event_id', $event->id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        // Generate receive reference
        $receiveRef = ReceiveSession::generateReceiveRef();

        // Create new receive session
        $session = ReceiveSession::create([
            'event_id'      => $event->id,
            'session_token' => ReceiveSession::generateToken(),
            'created_by'    => auth()->id(),
            'expires_at'    => now()->addHours(4),
            'status'        => 'active',
            'receive_ref'   => $receiveRef,
        ]);

        // Store receive ref on event
        $event->update(['receive_ref' => $receiveRef]);

        // Log activity
        ActivityLog::create([
            'event_id' => $event->id,
            'action' => 'receive_session_started',
            'description' => 'Receive session started — Ref: ' . $receiveRef,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('events.receive.monitor', [$event, $session]);
    }

    public function receiveMonitor(Event $event, ReceiveSession $receiveSession)
    {
        // Validate session belongs to this event
        abort_if($receiveSession->event_id !== $event->id, 404);

        // Redirect if expired
        if ($receiveSession->isExpired()) {
            return redirect()->route('events.show', $event)
                ->with('error', 'Receive session has expired.');
        }

        $event->load([
            'eventItems.item.primaryImage',
            'borrowedItems',
            'operationalItems.operationalItem',
            'eventPieceDispatches.itemPiece.item',
            'missingItems.item',
        ]);

        $totalDispatched = $event->eventPieceDispatches->count();

        // Per-item progress
        $itemProgress = $event->eventItems->map(function($eventItem) use ($receiveSession) {
            $received = ReceiveSessionPiece::where('receive_session_id', $receiveSession->id)
                ->where('item_id', $eventItem->item_id)
                ->count();

            $imagePath = $eventItem->item->primaryImage?->image_path ?? $eventItem->item->image_path;

            return [
                'item_id'    => $eventItem->item_id,
                'item_name'  => $eventItem->item->name,
                'category'   => $eventItem->item->category,
                'image_path' => $imagePath,
                'dispatched' => $eventItem->quantity_dispatched,
                'received'   => $received,
                'complete'   => $received >= $eventItem->quantity_dispatched,
            ];
        });

        $receiveRef = $event->receive_ref ?? $receiveSession->receive_ref;

        // Generate QR code
        $sessionUrl = config('app.url') . '/receive/' . $receiveSession->session_token;
        $qrCodeSvg  = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(220)->errorCorrection('H')->generate($sessionUrl);

        return view('events.receive-monitor-premium', compact(
            'event', 'receiveSession', 'receiveRef',
            'totalDispatched', 'itemProgress', 'qrCodeSvg'
        ));
    }

    public function receiveExtend(Event $event, ReceiveSession $receiveSession)
    {
        $receiveSession->update(['expires_at' => now()->addHours(4)]);
        return response()->json(['success' => true, 'expires_at' => $receiveSession->expires_at->toISOString()]);
    }

    public function receiveCancel(Event $event, ReceiveSession $receiveSession)
    {
        $receiveSession->update(['status' => 'cancelled']);
        return redirect()->route('events.show', $event)->with('info', 'Receive session cancelled.');
    }

    public function receiveConfirm(Request $request, Event $event, ReceiveSession $receiveSession)
    {
        // Validate session
        abort_if($receiveSession->event_id !== $event->id, 404);

        // Parse borrowed and operational items (for tracking purposes)
        $borrowedChecked = json_decode($request->borrowed_checked ?? '[]', true);
        $operationalChecked = json_decode($request->operational_checked ?? '[]', true);

        DB::transaction(function() use ($event, $receiveSession, $borrowedChecked, $operationalChecked) {
            // Update borrowed items quantity returned
            if (!empty($borrowedChecked)) {
                foreach ($borrowedChecked as $borrowedId) {
                    $borrowed = EventBorrowedItem::find($borrowedId);
                    if ($borrowed) {
                        $borrowed->update(['quantity_returned' => $borrowed->quantity_dispatched]);
                    }
                }
            }

            // For operational items, we just track that they were checked
            // (no specific return tracking needed in current schema)

            // Update receive session status
            $receiveSession->update(['status' => 'completed']);

            // Update event status to Completed
            $event->update(['status' => 'Completed']);

            // Log activity
            ActivityLog::create([
                'event_id' => $event->id,
                'action' => 'receive_completed',
                'description' => 'Receive session completed — ' . $receiveSession->received_count . ' pieces received, ' . count($borrowedChecked) . ' borrowed items, ' . count($operationalChecked) . ' operational items confirmed.',
                'user_id' => auth()->id(),
            ]);
        });

        return redirect()->route('events.show', $event)
            ->with('success', 'Receive session completed successfully! All items have been received.');
    }

    public function manualReceive(Event $event)
    {
        $event->load([
            'eventPieceDispatches.itemPiece.item',
            'borrowedItems',
            'operationalItems.operationalItem',
        ]);

        return view('events.manual-receive', compact('event'));
    }

    public function manualReceiveStore(Request $request, Event $event)
    {
        $request->validate([
            'received_pieces'          => 'nullable|array',
            'received_pieces.*'        => 'array',
            'conditions'               => 'nullable|array',
            'destinations'             => 'nullable|array',
            'damage_notes'             => 'nullable|array',
            'missing_pieces'           => 'nullable|array',
            'borrowed_returned'        => 'nullable|array',
            'operational_returned'     => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $event) {

            // Process own inventory pieces
            if ($request->received_pieces) {
                foreach ($request->received_pieces as $itemId => $codes) {
                    foreach ($codes as $code) {
                        if (empty(trim($code))) continue;

                        $uniqueCode = strtoupper(trim($code));
                        $piece = ItemPiece::where('unique_code', $uniqueCode)->first();
                        if (!$piece) continue;

                        $destination = $request->destinations[$itemId] ?? 'warehouse';
                        $condition   = $request->conditions[$itemId] ?? null;
                        $damageNote  = $request->damage_notes[$itemId] ?? null;

                        // Update piece status
                        $statusMap = [
                            'warehouse' => 'Available',
                            'cleaning'  => 'Cleaning',
                            'repair'    => 'Under Repair',
                        ];
                        $piece->update([
                            'status'           => $statusMap[$destination] ?? 'Available',
                            'condition_score'  => $condition,
                            'current_event_id' => $destination === 'warehouse' ? null : $piece->current_event_id,
                        ]);

                        // Update dispatch record
                        EventPieceDispatch::where('event_id', $event->id)
                            ->where('item_piece_id', $piece->id)
                            ->update([
                                'condition_on_return' => $condition,
                                'return_destination'  => $destination,
                                'return_notes'        => $damageNote,
                                'returned_at'         => now(),
                                'returned_by'         => auth()->id(),
                            ]);

                        // Create repair record if needed
                        if ($destination === 'repair') {
                            \App\Models\Repair::firstOrCreate(
                                ['item_id' => $piece->item_id, 'status' => 'Pending'],
                                [
                                    'description' => $damageNote ?? 'Flagged on return from ' . $event->name,
                                    'started_at'  => now(),
                                ]
                            );
                        }

                        ActivityLog::create([
                            'item_id'     => $piece->item_id,
                            'event_id'    => $event->id,
                            'action'      => 'Returned',
                            'description' => $piece->unique_code . ' returned from ' . $event->name . ' → ' . ucfirst($destination),
                            'user_id'     => auth()->id(),
                        ]);
                    }
                }
            }

            // Mark explicitly missing pieces
            if ($request->missing_pieces) {
                foreach ($request->missing_pieces as $code) {
                    $piece = ItemPiece::where('unique_code', strtoupper($code))->first();
                    if (!$piece) continue;

                    MissingItem::updateOrCreate(
                        ['event_id' => $event->id, 'item_piece_id' => $piece->id],
                        [
                            'unique_code' => $piece->unique_code,
                            'item_id'     => $piece->item_id,
                            'marked_by'   => auth()->id(),
                            'marked_at'   => now(),
                            'status'      => 'missing',
                            'notes'       => 'Marked missing during manual receive.',
                        ]
                    );
                }
            }

            // Process borrowed items
            if ($request->borrowed_returned) {
                foreach ($request->borrowed_returned as $id => $qty) {
                    EventBorrowedItem::where('id', $id)
                        ->where('event_id', $event->id)
                        ->update(['quantity_returned' => (int) $qty]);
                }
            }

            // Process operational items
            if ($request->operational_returned) {
                foreach ($request->operational_returned as $id => $qty) {
                    EventOperationalItem::where('id', $id)
                        ->where('event_id', $event->id)
                        ->update(['quantity_returned' => (int) $qty]);
                }
            }

            // Update event status
            $event->update(['status' => 'Completed']);

            // Log
            ActivityLog::create([
                'event_id' => $event->id,
                'action' => 'manual_receive_completed',
                'description' => 'Manual receiving completed for event: ' . $event->name,
                'user_id' => auth()->id(),
            ]);
        });

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Items received successfully. Event marked as Completed.');
    }

    /**
     * Generate Receive Note PDF (similar to Dispatch Note)
     */
    public function receiptNote(Event $event, ReceiveSession $receiveSession)
    {
        $event->load([
            'eventItems.item',
            'borrowedItems',
            'operationalItems.operationalItem',
            'eventPieceDispatches.itemPiece.item',
        ]);

        // Get dispatched items (only those that were actually dispatched)
        $dispatchedItems = $event->eventItems()->where('quantity_dispatched', '>', 0)->with('item')->get();

        $totalPiecesDispatched = $event->eventPieceDispatches->count();
        $totalItems = $dispatchedItems->count();
        $totalBorrowed = $event->borrowedItems()->where('quantity_dispatched', '>', 0)->count();
        $totalOperational = $event->operationalItems()->where('quantity_dispatched', '>', 0)->count();

        $receiveRef = $event->receive_ref ?? $receiveSession->receive_ref;

        $sessionUrl = config('app.url') . '/receive/' . $receiveSession->session_token;
        $qrCodeSvg  = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(180)
            ->errorCorrection('H')
            ->generate($sessionUrl);

        $pdf = \PDF::loadView('reports.receive_note', compact(
            'event', 'receiveSession', 'qrCodeSvg', 'receiveRef',
            'dispatchedItems', 'totalPiecesDispatched', 'totalItems',
            'totalBorrowed', 'totalOperational'
        ));

        return $pdf->download('RECEIVE-NOTE-' . strtoupper(str_replace(' ', '-', $event->name)) . '.pdf');
    }

    public function receivingReport(Event $event)
    {
        $event->load([
            'eventItems.item',
            'borrowedItems',
            'operationalItems.operationalItem',
            'eventPieceDispatches.itemPiece.item',
            'missingItems.item',
        ]);

        // Get the completed receive session
        $receiveSession = ReceiveSession::where('event_id', $event->id)
            ->where('status', 'completed')
            ->latest()
            ->first();

        // Get all received pieces with their details
        $receivedPieces = ReceiveSessionPiece::whereIn(
                'receive_session_id',
                ReceiveSession::where('event_id', $event->id)->pluck('id')
            )
            ->with('item')
            ->get()
            ->groupBy('item_id');

        // Destination summary counts
        $toWarehouse = ReceiveSessionPiece::whereIn(
                'receive_session_id',
                ReceiveSession::where('event_id', $event->id)->pluck('id')
            )->where('destination', 'warehouse')->count();

        $toCleaning = ReceiveSessionPiece::whereIn(
                'receive_session_id',
                ReceiveSession::where('event_id', $event->id)->pluck('id')
            )->where('destination', 'cleaning')->count();

        $toRepair = ReceiveSessionPiece::whereIn(
                'receive_session_id',
                ReceiveSession::where('event_id', $event->id)->pluck('id')
            )->where('destination', 'repair')->count();

        $missingItems = MissingItem::where('event_id', $event->id)
            ->where('status', 'missing')
            ->with('item')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'reports.receiving_report',
            compact(
                'event', 'receiveSession',
                'receivedPieces',
                'toWarehouse', 'toCleaning', 'toRepair',
                'missingItems'
            )
        )->setPaper('a4', 'portrait');

        return $pdf->stream('receiving-report-' . $event->receive_ref . '.pdf');
    }

    /**
     * Receive progress API endpoint for polling
     */
    public function receiveProgress(Event $event, ReceiveSession $receiveSession)
    {
        \DB::connection()->disableQueryLog();
        $receiveSession = ReceiveSession::find($receiveSession->id);

        if (!$receiveSession) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        $event->load('eventItems.item');
        $totalDispatched = EventPieceDispatch::where('event_id', $event->id)
            ->whereNotNull('dispatched_at')
            ->count();

        // Per-item progress
        $itemProgress = $event->eventItems->map(function($eventItem) use ($receiveSession, $event) {
            $dispatchedCount = EventPieceDispatch::where('event_id', $event->id)
                ->whereHas('itemPiece', fn($q) => $q->where('item_id', $eventItem->item_id))
                ->count();

            $receivedCount = \DB::table('receive_session_pieces')
                ->where('receive_session_id', $receiveSession->id)
                ->where('item_id', $eventItem->item_id)
                ->count();

            // Get receiver name
            $latestReceive = ReceiveSessionPiece::where('receive_session_id', $receiveSession->id)
                ->where('item_id', $eventItem->item_id)
                ->with('receiver')
                ->orderByDesc('received_at')
                ->first();

            return [
                'item_id'       => $eventItem->item_id,
                'item_name'     => $eventItem->item->name,
                'dispatched'    => $dispatchedCount,
                'received'      => $receivedCount,
                'complete'      => $receivedCount >= $dispatchedCount,
                'receiver_name' => $latestReceive?->receiver?->name ?? null,
            ];
        });

        // Recent receives
        $recentReceives = ReceiveSessionPiece::where('receive_session_id', $receiveSession->id)
            ->with('item', 'receiver')
            ->orderByDesc('received_at')
            ->limit(8)
            ->get()
            ->map(fn($r) => [
                'unique_code'   => $r->unique_code,
                'item_name'     => $r->item->name,
                'destination'   => $r->destination,
                'condition'     => $r->condition_score,
                'receiver_name' => $r->receiver?->name ?? 'System',
                'received_at'   => $r->received_at->format('H:i:s'),
            ]);

        // Missing items so far
        $missingCount = MissingItem::where('event_id', $event->id)
            ->where('status', 'missing')
            ->count();

        $response = [
            'received_count'  => $receiveSession->received_count,
            'total_dispatched'=> $totalDispatched,
            'percentage'      => $totalDispatched > 0
                ? round(($receiveSession->received_count / $totalDispatched) * 100)
                : 0,
            'session_valid'   => !$receiveSession->isExpired(),
            'session_status'  => $receiveSession->status,
            'expires_at'      => $receiveSession->expires_at->toISOString(),
            'recent_receives' => $recentReceives,
            'item_progress'   => $itemProgress,
            'missing_count'   => $missingCount,
            'all_complete'    => $receiveSession->received_count >= $totalDispatched,
            'timestamp'       => now()->timestamp,
        ];

        return response()->json($response)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function validatePieceReturn(Request $request)
    {
        $code    = strtoupper(trim($request->query('code')));
        $eventId = $request->query('event_id');

        $piece = ItemPiece::where('unique_code', $code)->first();

        if (!$piece) {
            return response()->json([
                'valid'   => false,
                'message' => 'Code ' . $code . ' does not exist in the system.',
            ]);
        }

        // Check piece was actually dispatched for this event
        $dispatched = EventPieceDispatch::where('event_id', $eventId)
            ->where('item_piece_id', $piece->id)
            ->whereNull('returned_at')
            ->first();

        if (!$dispatched) {
            return response()->json([
                'valid'   => false,
                'message' => $code . ' was not dispatched for this event.',
                'item_name' => $piece->item->name,
            ]);
        }

        return response()->json([
            'valid'      => true,
            'message'    => 'Valid — ' . $piece->item->name,
            'item_name'  => $piece->item->name,
            'item_id'    => $piece->item_id,
            'unique_code'=> $code,
        ]);
    }

    public function resolveMissing(Event $event, MissingItem $missing)
    {
        $missing->update(['status' => 'found']);

        // Update piece status back to Available
        if ($missing->itemPiece) {
            $missing->itemPiece->update([
                'status'           => 'Available',
                'current_event_id' => null,
            ]);
        }

        ActivityLog::create([
            'item_id'     => $missing->item_id,
            'event_id'    => $event->id,
            'action'      => 'Found',
            'description' => $missing->unique_code . ' marked as found — previously missing from ' . $event->name,
            'user_id'     => auth()->id(),
        ]);

        return back()->with('success', $missing->unique_code . ' marked as found.');
    }
}
