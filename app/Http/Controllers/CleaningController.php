<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class CleaningController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::where('status', 'Cleaning');

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('last_updated_at', 'desc')->paginate(15)->withQueryString();

        return view('cleaning.index', compact('items', 'search'));
    }

    public function complete(Request $request, Item $item)
    {
        $item->update([
            'status' => 'Available',
            'location' => 'Warehouse',
            'last_updated_by' => auth()->user()->name,
            'last_updated_at' => now(),
        ]);

        ActivityLog::create([
            'item_id' => $item->id,
            'action' => 'cleaned',
            'description' => "Item marked as cleaned and moved back to Warehouse.",
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', '"{' . $item->name . '}" has been cleaned and is now available in warehouse.');
    }

    public function bulkComplete(Request $request)
    {
        $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:items,id'
        ]);

        $items = Item::whereIn('id', $request->item_ids)->where('status', 'Cleaning')->get();
        
        $count = 0;
        foreach ($items as $item) {
            $item->update([
                'status' => 'Available',
                'location' => 'Warehouse',
                'last_updated_by' => auth()->user()->name,
                'last_updated_at' => now(),
            ]);

            ActivityLog::create([
                'item_id' => $item->id,
                'action' => 'cleaned',
                'description' => "Item marked as cleaned and moved back to Warehouse.",
                'user_id' => auth()->id(),
            ]);
            $count++;
        }

        return back()
            ->with('success', "{$count} item" . ($count === 1 ? '' : 's') . " cleaned successfully and returned to warehouse.")
            ->with('toast_sound', 'true');
    }
}
