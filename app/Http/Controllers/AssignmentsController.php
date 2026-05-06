<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Item;
use Illuminate\Http\Request;

class AssignmentsController extends Controller
{
    public function index(Request $request)
    {
        $query = Assignment::with("item");

        if ($search = $request->get("search")) {
            $query->whereHas("item", function ($q) use ($search) {
                $q->where("name", "like", "%{$search}%");
            })->orWhere("assigned_to", "like", "%{$search}%");
        }

        if ($status = $request->get("status")) {
            $query->where("status", $status);
        }

        $assignments = $query->orderBy("created_at", "desc")->paginate(15);
        
        return view("assignments.index", compact("assignments"));
    }

    public function create()
    {
        $items = Item::where("status", "Available")->get();
        return view("assignments.create", compact("items"));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "item_id" => "required|exists:items,id",
            "assigned_to" => "required|string|max:255",
            "assigned_by" => "required|string|max:255",
            "due_date" => "nullable|date|after:today",
            "notes" => "nullable|string",
        ]);

        $item = Item::findOrFail($validated["item_id"]);
        
        $assignment = Assignment::create([
            "item_id" => $validated["item_id"],
            "assigned_to" => $validated["assigned_to"],
            "assigned_by" => $validated["assigned_by"],
            "due_date" => $validated["due_date"] ?? null,
            "notes" => $validated["notes"] ?? null,
            "status" => "Active",
        ]);

        $item->update([
            "status" => "Assigned",
            "assigned_to" => $validated["assigned_to"],
            "assigned_by" => $validated["assigned_by"],
            "last_updated_by" => session("current_user_role", "Admin"),
            "last_updated_at" => now(),
        ]);

        $item->activityLogs()->create([
            "action" => "assigned",
            "description" => "Assigned to " . $validated["assigned_to"],
            "user_id" => session("current_user_role", "Admin"),
        ]);

        return redirect()->route("assignments.index")->with("success", "Assignment created successfully.");
    }

    public function show(string $id)
    {
        $assignment = Assignment::with(["item", "checklists"])->findOrFail($id);
        return view("assignments.show", compact("assignment"));
    }

    public function edit(string $id)
    {
        $assignment = Assignment::findOrFail($id);
        $items = Item::all();
        return view("assignments.edit", compact("assignment", "items"));
    }

    public function update(Request $request, string $id)
    {
        $assignment = Assignment::findOrFail($id);

        $validated = $request->validate([
            "assigned_to" => "required|string|max:255",
            "assigned_by" => "required|string|max:255",
            "due_date" => "nullable|date",
            "notes" => "nullable|string",
        ]);

        $assignment->update($validated);

        return redirect()->route("assignments.index")->with("success", "Assignment updated successfully.");
    }

    public function destroy(string $id)
    {
        $assignment = Assignment::findOrFail($id);
        $assignment->delete();

        return redirect()->route("assignments.index")->with("success", "Assignment deleted successfully.");
    }

    public function return(string $id)
    {
        $assignment = Assignment::findOrFail($id);
        $item = $assignment->item;

        $assignment->update([
            "status" => "Returned",
            "returned_at" => now(),
        ]);

        $item->update([
            "status" => "Available",
            "assigned_to" => null,
            "assigned_by" => null,
            "last_updated_by" => session("current_user_role", "Admin"),
            "last_updated_at" => now(),
        ]);

        $item->activityLogs()->create([
            "action" => "returned",
            "description" => "Item returned by " . $assignment->assigned_to,
            "user_id" => session("current_user_role", "Admin"),
        ]);

        return redirect()->route("assignments.index")->with("success", "Item returned successfully.");
    }
}
