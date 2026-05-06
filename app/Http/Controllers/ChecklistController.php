<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ChecklistController extends Controller
{
    public function index()
    {
        $items = Item::all();
        $actions = ['Verified', 'Loaded', 'Offloaded', 'Handed Over'];

        return view('checklist.index', compact('items', 'actions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'action' => 'required|in:Verified,Loaded,Offloaded,Handed Over',
        ]);

        $item = Item::findOrFail($validated['item_id']);

        // Only Offloader can mark Offloaded
        if ($validated['action'] == 'Offloaded' && session('current_user_role') !== 'Offloader') {
            return back()->with('error', 'Only Offloader role can mark items as Offloaded.');
        }

        // Log the checklist action
        $item->activityLogs()->create([
            'action' => 'checklist_' . strtolower($validated['action']),
            'description' => "Checklist action: {$validated['action']} performed",
            'user_id' => session('current_user_role', 'Admin')
        ]);

        return back()->with('success', 'Checklist action "' . $validated['action'] . '" recorded for ' . $item->name . '.');
    }
}
