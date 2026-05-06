<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use App\Models\Item;
use Illuminate\Http\Request;

class RepairsController extends Controller
{
    public function index(Request $request)
    {
        $query = Repair::with("item");

        if ($search = $request->get("search")) {
            $query->whereHas("item", function ($q) use ($search) {
                $q->where("name", "like", "%{$search}%");
            });
        }

        if ($status = $request->get("status")) {
            $query->where("status", $status);
        }

        $repairs = $query->orderBy("created_at", "desc")->paginate(15);
        
        return view("repairs.index", compact("repairs"));
    }

    public function create()
    {
        $items = Item::all();
        return view("repairs.create", compact("items"));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "item_id" => "required|exists:items,id",
            "repair_type" => "required|string",
            "description" => "required|string",
            "materials_required" => "nullable|string",
            "damage_image" => "nullable|image|max:5120",
            "estimated_cost" => "required|numeric|min:0",
            "technician_name" => "nullable|string|max:255",
            "notes" => "nullable|string",
        ]);

        $item = Item::findOrFail($validated["item_id"]);
        
        $damage_image_path = null;
        if ($request->hasFile("damage_image")) {
            $damage_image_path = $request->file("damage_image")->store("repairs/damage_images", "public");
        }

        $repair = Repair::create([
            "item_id" => $validated["item_id"],
            "repair_type" => $validated["repair_type"],
            "description" => $validated["description"],
            "materials_required" => $validated["materials_required"] ?? null,
            "damage_image_path" => $damage_image_path,
            "estimated_cost" => $validated["estimated_cost"] ?? null,
            "technician_name" => $validated["technician_name"] ?? null,
            "notes" => $validated["notes"] ?? null,
            "status" => "Pending",
            "started_at" => now(),
        ]);

        $item->update([
            "status" => "Under Repair",
            "location" => "Repair Workshop",
            "last_updated_at" => now(),
        ]);

        $item->activityLogs()->create([
            "action" => "sent_for_repair",
            "description" => "Item sent for repair: " . $validated["description"],
            "user_id" => auth()->id() ?? null,
        ]);

        return redirect()->route("repairs.index")
            ->with("success", "Repair record created for " . $item->name . ". Item moved to Repair Workshop.")
            ->with("toast_sound", "true");
    }

    public function show(string $id)
    {
        $repair = Repair::with("item")->findOrFail($id);
        return view("repairs.show", compact("repair"));
    }

    public function edit(string $id)
    {
        $repair = Repair::findOrFail($id);
        $items = Item::all();
        return view("repairs.edit", compact("repair", "items"));
    }

    public function update(Request $request, string $id)
    {
        $repair = Repair::findOrFail($id);

        $validated = $request->validate([
            "repair_type" => "required|string",
            "description" => "required|string",
            "materials_required" => "nullable|string",
            "damage_image" => "nullable|image|max:5120",
            "estimated_cost" => "required|numeric|min:0",
            "actual_cost" => "nullable|numeric|min:0",
            "status" => "required|string|in:Pending,In Progress,Completed,Cancelled",
            "technician_name" => "nullable|string|max:255",
            "completed_at" => "nullable|date",
            "notes" => "nullable|string",
        ]);
        
        if ($request->hasFile("damage_image")) {
            $validated["damage_image_path"] = $request->file("damage_image")->store("repairs/damage_images", "public");
        }

        $repair->update($validated);

        if ($validated["status"] === "Completed") {
            if (!$repair->completed_at) {
                $repair->update(["completed_at" => now()]);
            }
            $repair->item->update([
                "status" => "Available",
                "location" => "Warehouse",
                "last_updated_at" => now(),
            ]);
            
            $repair->item->activityLogs()->create([
                "action" => "repaired",
                "description" => "Item repair completed and returned to Warehouse.",
                "user_id" => auth()->id() ?? null,
            ]);
        }

        $message = "Repair record updated successfully.";
        $soundEnabled = false;

        if ($validated["status"] === "Completed") {
            $message = "Repair completed successfully. Item returned to warehouse.";
            $soundEnabled = true;
        }

        return redirect()->route("repairs.index")
            ->with("success", $message)
            ->with("toast_sound", $soundEnabled ? "true" : "false");
    }

    public function destroy(string $id)
    {
        $repair = Repair::findOrFail($id);
        $repair->delete();

        return redirect()->route("repairs.index")
            ->with("warning", "Repair record deleted permanently.");
    }
}
