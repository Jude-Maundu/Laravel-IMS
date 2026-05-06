<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\Item;
use App\Models\Assignment;
use Illuminate\Http\Request;

class ChecklistsController extends Controller
{
    public function index(Request $request)
    {
        $query = Checklist::with(["item", "assignment"]);

        if ($search = $request->get("search")) {
            $query->whereHas("item", function ($q) use ($search) {
                $q->where("name", "like", "%{$search}%");
            })->orWhere("action", "like", "%{$search}%");
        }

        if ($action = $request->get("action")) {
            $query->where("action", $action);
        }

        $checklists = $query->orderBy("created_at", "desc")->paginate(15);
        $items = Item::all();
        $assignments = Assignment::where("status", "Active")->get();
        $actions = ["Verified", "Loaded", "Offloaded", "Handed Over"];
        
        return view("checklist.index", compact("checklists", "items", "assignments", "actions"));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "item_id" => "required|exists:items,id",
            "assignment_id" => "nullable|exists:assignments,id",
            "action" => "required|string|in:Verified,Loaded,Offloaded,Handed Over",
            "condition" => "nullable|string|in:Good,Damaged,Missing Parts",
            "notes" => "nullable|string",
        ]);

        $item = Item::findOrFail($validated["item_id"]);

        // Check if user has permission to assign items (for checklist actions)
        if (!auth()->user()->hasPermissionTo('assign items')) {
            return back()->with("error", "You do not have permission to perform this action.");
        }

        Checklist::create([
            "item_id" => $validated["item_id"],
            "assignment_id" => $validated["assignment_id"] ?? null,
            "action" => $validated["action"],
            "performed_by" => session("current_user_role", "Admin"),
            "condition" => $validated["condition"] ?? null,
            "notes" => $validated["notes"] ?? null,
        ]);

        $item->activityLogs()->create([
            "action" => "checklist_" . strtolower($validated["action"]),
            "description" => "Checklist action: {$validated["action"]} performed",
            "user_id" => session("current_user_role", "Admin"),
        ]);

        return back()->with("success", "Checklist action recorded successfully.");
    }

    public function destroy(string $id)
    {
        $checklist = Checklist::findOrFail($id);
        $checklist->delete();

        return redirect()->route("checklist.index")->with("success", "Checklist record deleted.");
    }
}
