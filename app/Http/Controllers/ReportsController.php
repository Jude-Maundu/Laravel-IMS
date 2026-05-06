<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Assignment;
use App\Models\Repair;
use App\Models\ActivityLog;
use App\Models\Checklist;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

use App\Models\Event;
use App\Models\EventItem;

class ReportsController extends Controller
{
    public function index()
    {
        $statuses = [
            "Available", "Assigned", "In Use", "Under Inspection",
            "Cleaning", "Cleaned", "Under Repair", "Repaired", "Damaged", "Irreparable"
        ];

        $itemsByStatus = [];
        foreach ($statuses as $status) {
            $itemsByStatus[$status] = Item::where("status", $status)->count();
        }

        $damagedItems = Item::where("status", "Damaged")->get();
        $underRepairItems = Item::where("status", "Under Repair")->get();
        $assignedItems = Item::whereNotNull("assigned_to")->get();
        
        // For report generator dropdowns
        $items = Item::orderBy('name')->get();
        $events = Event::orderBy('loading_date', 'desc')->get();
        $repairs = Repair::with('item')->orderBy('created_at', 'desc')->get();
        $eventsCount = $events->count();

        return view("reports.index", compact("itemsByStatus", "damagedItems", "underRepairItems", "assignedItems", "items", "events", "repairs", "eventsCount"));
    }

    public function inventoryReport(Request $request)
    {
        $items = Item::all();
        return view("reports.inventory", compact("items"));
    }

    public function singleRepairReportPdf($id)
    {
        $repair = Repair::with(['item.images'])->findOrFail($id);
        $ref_no = 'GA-RPR-' . str_pad($repair->id, 5, '0', STR_PAD_LEFT) . '-' . date('Ymd');
        
        $pdf = Pdf::loadView('reports.repair_single_pdf', compact('repair', 'ref_no'));
        return $pdf->download('GreyApple_Repair_Job_RPR' . $repair->id . '.pdf');
    }

    public function assignmentsReport(Request $request)
    {
        $assignments = Assignment::with("item")->get();
        return view("reports.assignments", compact("assignments"));
    }

    public function repairsReport(Request $request)
    {
        $repairs = Repair::with("item")->get();
        $totalEstimated = $repairs->sum("estimated_cost");
        $totalActual = $repairs->where("status", "Completed")->sum("actual_cost");
        return view("reports.repairs", compact("repairs", "totalEstimated", "totalActual"));
    }

    public function activityReport(Request $request)
    {
        $activities = ActivityLog::with("item")->orderBy("created_at", "desc")->limit(100)->get();
        return view("reports.activity", compact("activities"));
    }

    public function itemReportPdf($id)
    {
        $item = Item::with(['repairs', 'activityLogs.user', 'events', 'images'])->findOrFail($id);
        $ref_no = 'GA-ITM-' . str_pad($item->id, 5, '0', STR_PAD_LEFT) . '-' . date('Ymd');
        
        // Calculate health score
        $healthScore = 100;
        
        // Deduct for repairs
        $repairCount = $item->repairs->count();
        $healthScore -= ($repairCount * 5); // 5% off per repair
        
        // Deduct for condition on return
        $avgCondition = $item->events()->wherePivotNotNull('condition_on_return')->avg('condition_on_return');
        if ($avgCondition) {
            // If avg is 8/10, that's 80%. We can factor this in.
            // Let's say if it's less than 9, we start deducting.
            if ($avgCondition < 9) {
                $healthScore -= (9 - $avgCondition) * 10;
            }
        }
        
        // Final bounds
        $healthScore = max(min($healthScore, 100), 0);
        
        $totalMaintenanceCost = $item->repairs->sum('actual_cost') ?: $item->repairs->sum('estimated_cost');
        
        $pdf = Pdf::loadView('reports.item_pdf', compact('item', 'ref_no', 'healthScore', 'totalMaintenanceCost'));
        return $pdf->download('GreyApple_Item_Audit_ITM' . $item->id . '_' . date('Ymd') . '.pdf');
    }

    public function eventReportPdf($id, $type)
    {
        // Load event with all necessary relationships including dispatch/return images
        $event = Event::with([
            'staff',
            'eventItems.item.images',  // Item's primary images
            'eventItems.images'         // Dispatch/return photos
        ])->findOrFail($id);

        $ref_no = 'GA-EVT-' . str_pad($event->id, 5, '0', STR_PAD_LEFT) . '-' . date('Ymd');

        $view = 'reports.event_general_pdf';
        $typeLabel = 'GENERAL SUMMARY FOR';
        if ($type === 'checklist') {
            $view = 'reports.event_checklist_pdf';
            $typeLabel = 'DISPATCH CHECKLIST FOR';
        }
        if ($type === 'receive') {
            $view = 'reports.event_receive_pdf';
            $typeLabel = 'RECEIPT NOTE FOR';
        }

        $pdf = Pdf::loadView($view, compact('event', 'ref_no'));
        $filename = $typeLabel . ' ' . strtoupper($event->name) . '.pdf';
        return $pdf->download($filename);
    }

    public function inventoryReportPdf(Request $request)
    {
        $items = Item::with('images')->get();
        $ref_no = 'GA-INV-' . date('YmdHis');
        $pdf = Pdf::loadView('reports.inventory_pdf', compact('items', 'ref_no'));
        return $pdf->download('GreyApple_Global_Inventory_Audit_' . date('Ymd') . '.pdf');
    }

    public function assignmentsReportPdf(Request $request)
    {
        $events = Event::with(['staff', 'eventItems.item.images'])->orderBy('event_date', 'desc')->get();
        $ref_no = 'GA-EVTLOG-' . date('YmdHis');
        $pdf = Pdf::loadView('reports.assignments_pdf', compact('events', 'ref_no'));
        return $pdf->download('GreyApple_Comprehensive_Event_Log_' . date('Ymd') . '.pdf');
    }

    public function repairsReportPdf(Request $request)
    {
        $repairs = Repair::with("item")->get();
        $totalEstimated = $repairs->sum("estimated_cost");
        $totalActual = $repairs->where("status", "Completed")->sum("actual_cost");
        $ref_no = 'GA-RPR-' . date('YmdHis');
        $pdf = Pdf::loadView('reports.repairs_pdf', compact('repairs', 'totalEstimated', 'totalActual', 'ref_no'));
        return $pdf->download('GreyApple_Maintenance_Repairs_Log_' . date('Ymd') . '.pdf');
    }

    public function activityReportPdf(Request $request)
    {
        $activities = ActivityLog::with("item")->orderBy("created_at", "desc")->limit(100)->get();
        $ref_no = 'GA-ACT-' . date('YmdHis');
        $pdf = Pdf::loadView('reports.activity_pdf', compact('activities', 'ref_no'));
        return $pdf->download('GreyApple_System_Operational_Log_' . date('Ymd') . '.pdf');
    }

    public function cleaningReportPdf(Request $request)
    {
        $items = Item::with('images')
            ->where('status', 'Cleaning')
            ->orWhere('location', 'Cleaning Bay')
            ->orderBy('updated_at', 'desc')
            ->get();
            
        $ref_no = 'GA-CLN-' . date('YmdHis');
        $pdf = Pdf::loadView('reports.cleaning_pdf', compact('items', 'ref_no'));
        return $pdf->download('GreyApple_Cleaning_Bay_Report_' . date('Ymd') . '.pdf');
    }

    public function generateReceiptPdf(Event $event)
    {
        $ref_no = 'GA-RCPT-' . str_pad($event->id, 5, '0', STR_PAD_LEFT) . '-' . date('Ymd');
        $pdf = Pdf::loadView('reports.payment_receipt_pdf', compact('event', 'ref_no'));
        return $pdf->download('GreyApple_Payment_Receipt_' . $event->plan_ref . '.pdf');
    }
}
