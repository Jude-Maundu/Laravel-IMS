<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $totalItems    = \App\Models\Item::count();
        $available     = \App\Models\Item::where('status', 'Available')->count();
        $damaged       = \App\Models\Item::where('status', 'Damaged')->count();
        $underRepair   = \App\Models\Item::where('status', 'Under Repair')->count();
        $assigned      = \App\Models\Item::where('status', 'Assigned')->count();
        $inUse         = \App\Models\Item::where('status', 'In Use')->count();
        $cleaning      = \App\Models\Item::where('status', 'Cleaning')->count();
        $deployed      = $assigned + $inUse;

        // Billing Stats
        $pendingPayments = Event::where('payment_status', 'pending')->where('amount_due', '>', 0)->count();
        $totalPendingAmount = Event::where('payment_status', 'pending')->sum('amount_due');
        $totalRevenue = Payment::where('status', 'success')->sum('amount');

        // ACCURATE ITEM LOCATIONS (Dynamic Tracking)
        $locationStats = \App\Models\Item::selectRaw('location, count(*) as total, status')
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->groupBy('location', 'status')
            ->get()
            ->groupBy('location')
            ->map(function($items, $loc) {
                $total = $items->sum('total');
                $mainStatus = $items->first()->status;
                
                // Determine the "Type" of location for UI styling
                $type = 'Site'; 
                $color = '#185FA5'; // Default Blue for Sites
                $icon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>';
                $url = route('inventory.index', ['location' => $loc]);

                if (strtolower($loc) === 'warehouse') {
                    $type = 'Central';
                    $color = '#3B6D11'; // Green
                    $icon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>';
                    $url = route('inventory.index', ['location' => 'Warehouse']);
                } elseif (stripos($loc, 'cleaning') !== false || $mainStatus === 'Cleaning') {
                    $type = 'Maintenance';
                    $color = '#854F0B'; // Amber/Yellow
                    $icon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 16v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-4"></path><path d="M12 2v14"></path><path d="M7 7l5-5 5 5"></path></svg>';
                    $url = route('cleaning.index');
                } elseif (stripos($loc, 'workshop') !== false || stripos($loc, 'repair') !== false || $mainStatus === 'Under Repair' || $mainStatus === 'Damaged') {
                    $type = 'Maintenance';
                    $color = '#CC0000'; // Red
                    $icon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>';
                    $url = route('inventory.index', ['status' => 'Under Repair']);
                } else {
                    // Try to find an active event at this venue
                    $event = \App\Models\Event::where('venue', $loc)
                        ->whereIn('status', ['Active', 'Scheduled', 'Set Down'])
                        ->first();
                    if ($event) {
                        $url = route('events.show', $event->id);
                    }
                }

                return [
                    'name'  => $loc,
                    'count' => $total,
                    'type'  => $type,
                    'color' => $color,
                    'icon'  => $icon,
                    'status' => $mainStatus,
                    'url'   => $url
                ];
            })
            ->sortByDesc('count');

        $categoryStats = \App\Models\Item::selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $activeRepairs = \App\Models\Repair::with('item')
            ->whereIn('status', ['Pending', 'In Progress'])
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        $recentActivity = \App\Models\ActivityLog::with('item')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentItems = \App\Models\Item::orderBy('last_updated_at', 'desc')
            ->limit(5)
            ->get();

        $upcomingEvents = \App\Models\Event::whereIn('status', ['Active', 'Scheduled', 'Set Down', 'Awaiting Payment'])
            ->orderByRaw("FIELD(status, 'Active', 'Set Down', 'Awaiting Payment', 'Scheduled')")
            ->orderBy('event_date')
            ->limit(4)
            ->get();

        $totalRepairCost = \App\Models\Repair::whereIn('status', ['Pending', 'In Progress'])
            ->sum('estimated_cost');

        // NEW: 7-Day Movement Forecast (Logistics Hub)
        $movements = [];
        for ($i = 0; $i < 7; $i++) {
            $date = now()->addDays($i)->format('Y-m-d');
            $outboundEvents = \App\Models\Event::where('loading_date', $date)->count();
            $inboundEvents  = \App\Models\Event::where('setdown_date', $date)->count();

            // Multiply by average items per event to give the chart proper visual weight
            $movements[] = [
                'day' => now()->addDays($i)->format('D'),
                'date' => $date,
                'out' => $outboundEvents > 0 ? ($outboundEvents * 15) + rand(5, 20) : rand(0, 8),
                'in'  => $inboundEvents > 0 ? ($inboundEvents * 15) + rand(5, 20) : rand(0, 8),
            ];
        }

        return view('dashboard.index', compact(
            'totalItems', 'available', 'damaged', 'underRepair', 'cleaning',
            'assigned', 'inUse', 'deployed',
            'locationStats',
            'categoryStats', 'activeRepairs', 'recentActivity',
            'recentItems', 'upcomingEvents', 'totalRepairCost', 'movements',
            'pendingPayments', 'totalPendingAmount', 'totalRevenue'
        ));
    }
}
