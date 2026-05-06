<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Item::query();

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($location = $request->input('location')) {
            $query->where('location', $location);
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $sortField = $request->input('sort', 'last_updated_at');
        $sortDir   = $request->input('dir', 'desc');
        $allowedSorts = ['name', 'status', 'category', 'location', 'last_updated_at'];
        if (!in_array($sortField, $allowedSorts)) $sortField = 'last_updated_at';
        if (!in_array($sortDir, ['asc', 'desc'])) $sortDir = 'desc';

        // Load all images to properly populate primaryImageUrl accessor
        $items = $query->with('images')
            ->orderBy($sortField, $sortDir)
            ->paginate(15)
            ->withQueryString();

        $statusCounts = Item::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $itemCategories = Item::distinct()->orderBy('category')->pluck('category')->toArray();
        $dbCategories = [];
        if (Schema::hasTable('categories')) {
            $dbCategories = Category::orderBy('name')->pluck('name')->toArray();
        }
        $categories = array_unique(array_merge($itemCategories, $dbCategories));
        sort($categories);

        $locations  = ['Warehouse', 'Site A', 'Site B'];
        $statuses   = ['Available','Assigned','In Use','Under Inspection','Cleaning','Cleaned','Under Repair','Repaired','Damaged','Irreparable'];
        $totalItems = Item::count();

        return view('inventory.index', compact(
            'items', 'statusCounts', 'categories',
            'locations', 'statuses', 'totalItems'
        ));
    }

    /**
     * Display available items only.
     */
    public function available(Request $request)
    {
        $query = Item::where('status', 'Available');

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        if ($location = $request->input('location')) {
            $query->where('location', $location);
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $sortField = $request->input('sort', 'last_updated_at');
        $sortDir   = $request->input('dir', 'desc');
        $allowedSorts = ['name', 'status', 'category', 'location', 'last_updated_at'];
        if (!in_array($sortField, $allowedSorts)) $sortField = 'last_updated_at';
        if (!in_array($sortDir, ['asc', 'desc'])) $sortDir = 'desc';

        // Load all images to properly populate primaryImageUrl accessor
        $items = $query->with('images')
            ->orderBy($sortField, $sortDir)
            ->paginate(15)
            ->withQueryString();

        $statusCounts = Item::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $itemCategories = Item::distinct()->orderBy('category')->pluck('category')->toArray();
        $dbCategories = [];
        if (Schema::hasTable('categories')) {
            $dbCategories = Category::orderBy('name')->pluck('name')->toArray();
        }
        $categories = array_unique(array_merge($itemCategories, $dbCategories));
        sort($categories);

        $locations  = ['Warehouse', 'Site A', 'Site B'];
        $statuses   = ['Available','Assigned','In Use','Under Inspection','Cleaning','Cleaned','Under Repair','Repaired','Damaged','Irreparable'];
        $totalItems = Item::count();
        $availableCount = Item::where('status', 'Available')->count();

        return view('inventory.available', compact(
            'items', 'statusCounts', 'categories',
            'locations', 'statuses', 'totalItems', 'availableCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statuses = ['Available', 'Assigned', 'In Use', 'Under Inspection', 'Cleaning', 'Cleaned', 'Under Repair', 'Repaired', 'Damaged', 'Irreparable'];
        $locations = ['Warehouse', 'Site A', 'Site B'];
        
        $itemCategories = Item::distinct()->orderBy('category')->pluck('category')->toArray();
        $dbCategories = [];
        if (Schema::hasTable('categories')) {
            $dbCategories = Category::orderBy('name')->pluck('name')->toArray();
        }
        $categories = array_unique(array_merge($itemCategories, $dbCategories));
        sort($categories);

        return view('inventory.create', compact('statuses', 'locations', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model_number' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'specifications' => 'nullable|string',
            'dimensions' => 'nullable|string|max:255',
            'weight' => 'nullable|string|max:255',
            'status' => 'required|in:Available,Assigned,In Use,Under Inspection,Cleaning,Cleaned,Under Repair,Repaired,Damaged,Irreparable',
            'location' => 'required|in:Warehouse,Site A,Site B',
            'assigned_to' => 'nullable|string|max:255',
            'assigned_by' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'total_pieces' => 'nullable|integer|min:1',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('items', 'public');
        }
        $validated['image_path'] = $imagePath;

        $validated['last_updated_by'] = auth()->user()->name;
        $validated['last_updated_at'] = now();

        // Set default total_pieces if not provided
        if (!isset($validated['total_pieces'])) {
            $validated['total_pieces'] = 1;
        }

        $item = Item::create($validated);

        // Generate pieces for this item
        $item->generatePieces($validated['total_pieces']);

        // If an image was uploaded in Step 1, also create an ItemImage record
        if ($imagePath) {
            \App\Models\ItemImage::create([
                'item_id' => $item->id,
                'image_path' => $imagePath,
                'is_primary' => true,
                'uploaded_by' => auth()->id(),
            ]);
        }

        $this->logActivity($item, 'created', "Item created by " . auth()->user()->name);

        return redirect()->route('inventory.show', $item->id)
            ->with('success', 'Item "' . $item->name . '" created successfully. You can now add more images in the Media tab.')
            ->with('toast_sound', 'true');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Category "' . $validated['name'] . '" created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Item::findOrFail($id);

        $item->load([
            'images',
            'pieces' => fn($q) => $q->with('currentEvent'),
            'activityLogs' => fn($q) => $q->orderByDesc('created_at')->limit(50),
            'repairs'      => fn($q) => $q->orderByDesc('created_at'),
            'events'       => fn($q) => $q->withPivot([
                'condition_on_dispatch','condition_on_return',
                'dispatch_notes','return_notes',
                'dispatched_at','returned_at',
            ])->orderByDesc('event_items.created_at'),
        ]);

        $activityLogs = $item->activityLogs;

        $itemCategories = Item::distinct()->orderBy('category')->pluck('category')->toArray();
        $dbCategories = [];
        if (Schema::hasTable('categories')) {
            $dbCategories = Category::orderBy('name')->pluck('name')->toArray();
        }
        $categories = array_unique(array_merge($itemCategories, $dbCategories));
        sort($categories);

        return view('inventory.show', compact('item', 'activityLogs', 'categories'));
    }

    public function uploadImage(Request $request, Item $item)
    {
        $request->validate([
            'image'   => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
            'caption' => 'nullable|string|max:255',
        ]);

        $path = $request->file('image')->store("items/{$item->id}", 'public');

        $isPrimary = $item->images()->count() === 0;

        $image = \App\Models\ItemImage::create([
            'item_id'     => $item->id,
            'image_path'  => $path,
            'is_primary'  => $isPrimary,
            'caption'     => $request->input('caption'),
            'uploaded_by' => auth()->id(),
        ]);

        if ($isPrimary) {
            $item->update(['image_path' => $path]);
        }

        return response()->json([
            'success'    => true,
            'id'         => $image->id,
            'url'        => $image->url,
            'is_primary' => $image->is_primary,
        ]);
    }

    public function setPrimaryImage(Request $request, Item $item, \App\Models\ItemImage $image)
    {
        \App\Models\ItemImage::where('item_id', $item->id)->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);
        $item->update(['image_path' => $image->image_path]);

        \App\Models\ActivityLog::create([
            'item_id'     => $item->id,
            'action'      => 'image_updated',
            'description' => 'Primary image changed.',
            'user_id'     => auth()->id(),
        ]);

        return response()->json(['success' => true]);
    }

    public function deleteImage(Request $request, Item $item, \App\Models\ItemImage $image)
    {
        \Storage::disk('public')->delete($image->image_path);

        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $next = \App\Models\ItemImage::where('item_id', $item->id)->first();
            if ($next) {
                $next->update(['is_primary' => true]);
                $item->update(['image_path' => $next->image_path]);
            } else {
                $item->update(['image_path' => null]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = Item::findOrFail($id);
        $statuses = ['Available', 'Assigned', 'In Use', 'Under Inspection', 'Cleaning', 'Cleaned', 'Under Repair', 'Repaired', 'Damaged', 'Irreparable'];
        $locations = ['Warehouse', 'Site A', 'Site B'];

        $itemCategories = Item::distinct()->orderBy('category')->pluck('category')->toArray();
        $dbCategories = [];
        if (Schema::hasTable('categories')) {
            $dbCategories = Category::orderBy('name')->pluck('name')->toArray();
        }
        $categories = array_unique(array_merge($itemCategories, $dbCategories));
        sort($categories);

        return view('inventory.edit', compact('item', 'statuses', 'locations', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|string|max:255',
            'status'      => 'required|string',
            'location'    => 'nullable|string|max:255',
            'assigned_to' => 'nullable|string|max:255',
            'assigned_by' => 'nullable|string|max:255',
            'notes'       => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'total_pieces' => 'nullable|integer|min:1',
        ]);

        if ($request->hasFile('image')) {
            if ($item->image_path) {
                \Storage::disk('public')->delete($item->image_path);
            }
            $path = $request->file('image')->store("items/{$item->id}", 'public');
            $validated['image_path'] = $path;

            \App\Models\ItemImage::where('item_id', $item->id)
                ->update(['is_primary' => false]);

            \App\Models\ItemImage::create([
                'item_id'     => $item->id,
                'image_path'  => $path,
                'is_primary'  => true,
                'uploaded_by' => auth()->id(),
            ]);
        }

        $oldStatus = $item->status;
        $oldPiecesCount = $item->total_pieces;
        $validated['last_updated_at'] = now();
        $validated['last_updated_by'] = auth()->user()->name;

        $item->update($validated);

        // Handle pieces count change
        if (isset($validated['total_pieces']) && $validated['total_pieces'] != $oldPiecesCount) {
            $item->generatePieces($validated['total_pieces']);
        }

        if ($oldStatus !== $item->status) {
            \App\Models\ActivityLog::create([
                'item_id'     => $item->id,
                'action'      => 'status_changed',
                'description' => "Status changed from {$oldStatus} to {$item->status}.",
                'user_id'     => auth()->id(),
            ]);
        }

        return redirect()->route('inventory.show', $item)
            ->with('success', 'Item "' . $item->name . '" updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Item::findOrFail($id);
        $itemName = $item->name;

        $this->logActivity($item, 'deleted', "Item deleted by " . auth()->user()->name);
        $item->delete();

        return redirect()->route('inventory.index')
            ->with('warning', 'Item "' . $itemName . '" has been permanently deleted.');
    }

    /**
     * Change the status of an item with validation.
     */
    public function changeStatus(Request $request, string $id)
    {
        $item = Item::findOrFail($id);
        $newStatus = $request->input('status');

        $validTransitions = [
            'Available' => ['Assigned', 'In Use'],
            'Assigned' => ['In Use', 'Under Inspection'],
            'In Use' => ['Under Inspection', 'Cleaning'],
            'Under Inspection' => ['Cleaning', 'Damaged'],
            'Cleaning' => ['Cleaned'],
            'Cleaned' => ['Available'],
            'Under Repair' => ['Repaired'],
            'Repaired' => ['Available'],
            'Damaged' => ['Under Repair'],
            'Irreparable' => []
        ];

        $currentStatus = $item->status;
        if (!in_array($newStatus, $validTransitions[$currentStatus] ?? [])) {
            return back()->with('error', 'Invalid status transition from ' . $currentStatus . ' to ' . $newStatus);
        }

        // Business rule: damaged items cannot be assigned
        if ($item->status == 'Damaged' && $newStatus == 'Assigned') {
            return back()->with('error', 'Cannot assign a damaged item.');
        }

        $item->update([
            'status' => $newStatus,
            'last_updated_by' => auth()->user()->name,
            'last_updated_at' => now()
        ]);

        $this->logActivity($item, 'status_changed', "Status changed from {$currentStatus} to {$newStatus}");

        return back()->with('success', 'Item status changed from "' . $currentStatus . '" to "' . $newStatus . '" successfully.');
    }

    /**
     * Assign an item to a user.
     */
    public function assign(Request $request, string $id)
    {
        $item = Item::findOrFail($id);

        $validated = $request->validate([
            'assigned_to' => 'required|string|max:255',
        ]);

        // Cannot assign damaged items
        if ($item->status == 'Damaged') {
            return back()->with('error', 'Cannot assign a damaged item.');
        }

        $item->update([
            'assigned_to' => $validated['assigned_to'],
            'assigned_by' => auth()->user()->name,
            'status' => 'Assigned',
            'last_updated_by' => auth()->user()->name,
            'last_updated_at' => now()
        ]);

        $this->logActivity($item, 'assigned', "Assigned to {$validated['assigned_to']}");

        return back()->with('success', 'Item "' . $item->name . '" assigned to ' . $validated['assigned_to'] . ' successfully.');
    }

    /**
     * Return an item (set status to Available and clear assignment).
     */
    public function returnItem(string $id)
    {
        $item = Item::findOrFail($id);

        $item->update([
            'status' => 'Available',
            'assigned_to' => null,
            'assigned_by' => null,
            'last_updated_by' => auth()->user()->name,
            'last_updated_at' => now()
        ]);

        $this->logActivity($item, 'returned', 'Item returned to available');

        return back()->with('success', 'Item "' . $item->name . '" returned to warehouse and is now available.');
    }

    /**
     * Display item pieces register page.
     */
    public function pieces(Request $request)
    {
        $query = Item::query()->with('pieces');

        // Search
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('pieces', function($pq) use ($search) {
                      $pq->where('unique_code', 'like', "%{$search}%");
                  });
            });
        }

        // Category filter
        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        // Status filter for pieces
        if ($status = $request->input('status')) {
            $query->whereHas('pieces', function($q) use ($status) {
                $q->where('status', $status);
            });
        }

        $items = $query->orderBy('category')->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        // Calculate piece stats for each item
        foreach ($items as $item) {
            $item->available_count = $item->pieces->where('status', 'Available')->count();
            $item->assigned_count = $item->pieces->where('status', 'Assigned')->count();
            $item->other_count = $item->pieces->whereNotIn('status', ['Available', 'Assigned'])->count();
        }

        $categories = Item::distinct()->orderBy('category')->pluck('category');
        $statuses = ['Available', 'Assigned', 'Cleaning', 'Under Repair', 'Damaged', 'Written Off'];

        return view('inventory.pieces', compact('items', 'categories', 'statuses'));
    }

    /**
     * Bulk update item pieces quantities.
     */
    public function bulkUpdatePieces(Request $request)
    {
        $updates = $request->input('items', []);
        $updated = 0;

        foreach ($updates as $itemId => $newQuantity) {
            $item = Item::find($itemId);
            if (!$item) continue;

            $newQuantity = (int) $newQuantity;
            if ($newQuantity === $item->total_pieces) continue;

            $item->update(['total_pieces' => $newQuantity]);
            $item->generatePieces($newQuantity);
            $updated++;
        }

        return redirect()->route('inventory.pieces')
            ->with('success', "Updated piece quantities for {$updated} item(s).");
    }

    /**
     * API endpoint: returns availability information for an item
     */
    public function availability(Item $item)
    {
        $totalPieces = $item->total_pieces ?? 0;
        $availableCount = $item->pieces()->where('status', 'Available')->count();
        $assignedCount = $item->pieces()->where('status', 'Assigned')->count();
        $inServiceCount = $item->pieces()->whereIn('status', ['In Use', 'Cleaning', 'Under Repair'])->count();

        return response()->json([
            'item_id' => $item->id,
            'name' => $item->name,
            'total_pieces' => $totalPieces,
            'available' => $availableCount,
            'assigned' => $assignedCount,
            'in_service' => $inServiceCount,
            'sufficient' => $availableCount > 0,
        ]);
    }

    /**
     * Get QR code for a single piece.
     */
    public function pieceQR(\App\Models\ItemPiece $piece)
    {
        return response()->json([
            'piece_id' => $piece->id,
            'unique_code' => $piece->unique_code,
            'qr_svg' => $piece->getQrCodeSvg(),
        ]);
    }

    /**
     * Get QR codes for all pieces of an item.
     */
    public function itemPiecesQR(Item $item)
    {
        $pieces = $item->pieces()->get()->map(function($piece) {
            return [
                'id' => $piece->id,
                'unique_code' => $piece->unique_code,
                'qr_svg' => $piece->getQrCodeSvg(),
                'status' => $piece->status,
            ];
        });

        return response()->json([
            'item_id' => $item->id,
            'item_name' => $item->name,
            'pieces' => $pieces,
        ]);
    }

    /**
     * Get QR code for an item.
     */
    public function itemQR(Item $item)
    {
        return response()->json([
            'item_id' => $item->id,
            'item_name' => $item->name,
            'qr_svg' => $item->getQrCodeSvg(),
        ]);
    }

    /**
     * Log activity for an item.
     */
    protected function logActivity(Item $item, string $action, ?string $description = null): void
    {
        ActivityLog::create([
            'item_id' => $item->id,
            'action' => $action,
            'description' => $description,
            'user_id' => auth()->id(),
        ]);
    }
}
