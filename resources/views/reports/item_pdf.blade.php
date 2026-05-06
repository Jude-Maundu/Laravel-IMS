@extends('layouts.pdf')

@section('title', 'Item Report - ' . $item->name)
@section('report_name', 'Comprehensive Item Audit Report')

@section('content')
    @php
        $primaryImage = $item->images->firstWhere('is_primary', true) ?? $item->images->first();
        $imagePath = null;
        if ($primaryImage) {
            $imagePath = public_path('storage/' . $primaryImage->image_path);
        } elseif ($item->image_path) {
            $imagePath = public_path('storage/' . $item->image_path);
        }

        // Condition mapping: 1-5 scale
        $getConditionText = function($val) {
            if (!$val) return 'N/A';
            return match((int) $val) {
                5 => 'Excellent',
                4 => 'Good',
                3 => 'Fair',
                2 => 'Average',
                1 => 'Poor',
                default => 'N/A',
            };
        };

        // Color for condition (green for 4-5, amber for 3, red for 1-2)
        $getConditionColor = function($val) {
            if (!$val) return '#a09890';
            if ($val >= 4) return '#3B6D11'; // Green for Excellent & Good
            if ($val == 3) return '#854F0B';  // Amber for Fair
            return '#CC0000';                  // Red for Average & Poor
        };
    @endphp

    <div style="display: table; width: 100%; margin-bottom: 20px;">
        <div style="display: table-row;">
            <div style="display: table-cell; width: 180px; vertical-align: top; padding-right: 20px;">
                @if($imagePath && file_exists($imagePath))
                    <img src="{{ $imagePath }}" style="width: 180px; height: 180px; object-fit: cover; border-radius: 8px; border: 1px solid #ece8e3;">
                @else
                    <div style="width: 180px; height: 180px; background-color: #f8f7f5; border: 1px dashed #ece8e3; border-radius: 8px; text-align: center; line-height: 180px; color: #a09890; font-size: 10px;">
                        No Image Available
                    </div>
                @endif
            </div>
            <div style="display: table-cell; vertical-align: top;">
                <h2 style="margin: 0 0 10px 0; color: #0f0f0f; font-size: 18px;">{{ $item->name }}</h2>
                <p style="margin: 0 0 15px 0; color: #5c5550; font-size: 12px;">ID Number: <strong>#ITM-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</strong></p>
                
                <table style="margin-bottom: 0;">
                    <tr>
                        <th style="padding: 5px; background: transparent; border: none; width: 100px;">Status:</th>
                        <td style="padding: 5px; border: none;">
                            <span class="status-badge status-{{ strtolower(str_replace(' ', '', $item->status)) }}">
                                {{ $item->status }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th style="padding: 5px; background: transparent; border: none;">Category:</th>
                        <td style="padding: 5px; border: none;">{{ $item->category }}</td>
                    </tr>
                    <tr>
                        <th style="padding: 5px; background: transparent; border: none;">Location:</th>
                        <td style="padding: 5px; border: none;">{{ $item->location }}</td>
                    </tr>
                    <tr>
                        <th style="padding: 5px; background: transparent; border: none;">Condition:</th>
                        <td style="padding: 5px; border: none;">
                            @php
                                $avgCond = $item->events()->wherePivotNotNull('condition_on_return')->avg('condition_on_return');
                            @endphp
                            @if($avgCond)
                                <strong style="color: {{ $getConditionColor(round($avgCond)) }};">{{ $getConditionText(round($avgCond)) }}</strong>
                                <span style="color: #a09890; font-size: 10px;">({{ number_format($avgCond, 1) }}/5 Avg)</span>
                            @else
                                <span style="color: #3B6D11;">Excellent (Default)</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="section-header">Item Health & Lifecycle</div>
    <div style="display: table; width: 100%; margin-bottom: 20px;">
        <div style="display: table-row;">
            <div style="display: table-cell; width: 50%; padding-right: 10px;">
                <div style="background-color: #f8f7f5; padding: 15px; border-radius: 8px; border: 1px solid #ece8e3;">
                    <div style="font-size: 9px; color: #a09890; text-transform: uppercase; font-weight: bold; margin-bottom: 5px;">Overall Health Score</div>
                    <div style="font-size: 24px; font-weight: bold; color: {{ $healthScore > 80 ? '#3B6D11' : ($healthScore > 50 ? '#854F0B' : '#CC0000') }};">
                        {{ $healthScore }}%
                    </div>
                    <div style="height: 8px; background-color: #e0e0e0; border-radius: 4px; margin-top: 10px; overflow: hidden;">
                        <div style="height: 100%; width: {{ $healthScore }}%; background-color: {{ $healthScore > 80 ? '#3B6D11' : ($healthScore > 50 ? '#854F0B' : '#CC0000') }}; border-radius: 4px;"></div>
                    </div>
                </div>
            </div>
            <div style="display: table-cell; width: 50%; padding-left: 10px;">
                <div style="background-color: #f8f7f5; padding: 15px; border-radius: 8px; border: 1px solid #ece8e3;">
                    <div style="font-size: 9px; color: #a09890; text-transform: uppercase; font-weight: bold; margin-bottom: 5px;">Maintenance Cost</div>
                    <div style="font-size: 24px; font-weight: bold; color: #0f0f0f;">
                        KES {{ number_format($totalMaintenanceCost, 0) }}
                    </div>
                    <div style="font-size: 10px; color: #5c5550; margin-top: 10px;">
                        Total repairs performed: <strong>{{ $item->repairs->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section-header">Technical Specifications</div>
    <table>
        <tr>
            <th width="20%">Brand</th>
            <td width="30%">{{ $item->brand ?? 'N/A' }}</td>
            <th width="20%">Model Number</th>
            <td width="30%">{{ $item->model_number ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Serial Number</th>
            <td>{{ $item->serial_number ?? 'N/A' }}</td>
            <th>Purchase Date</th>
            <td>{{ $item->purchase_date ? \Carbon\Carbon::parse($item->purchase_date)->format('M d, Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Purchase Cost</th>
            <td>KES {{ number_format($item->purchase_cost, 0) }}</td>
            <th>Weight</th>
            <td>{{ $item->weight ? $item->weight . ' kg' : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Dimensions</th>
            <td colspan="3">{{ $item->dimensions ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Specifications</th>
            <td colspan="3">{{ $item->specifications ?? 'N/A' }}</td>
        </tr>
    </table>

    @if($item->events->count() > 0)
        <div class="section-header">Event Utilization History</div>
        <table>
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Client</th>
                    <th style="text-align: center;">Condition</th>
                </tr>
            </thead>
            <tbody>
                @foreach($item->events as $event)
                    @php
                        $pivot = $event->pivot ?? $item->eventItems->where('event_id', $event->id)->first();
                    @endphp
                    <tr>
                        <td>{{ $event->name }}</td>
                        <td>{{ $event->event_date->format('M d, Y') }}</td>
                        <td>{{ $event->client_name }}</td>
                        <td style="text-align: center;">
                            @if($pivot && $pivot->condition_on_return)
                                <span style="font-weight: bold; color: {{ $getConditionColor($pivot->condition_on_return) }};">
                                    {{ $getConditionText($pivot->condition_on_return) }}
                                </span>
                            @else
                                <span style="color: #a09890;">N/A</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($item->repairs->count() > 0)
        <div class="section-header">Maintenance & Repair Audit</div>
        <table>
            <thead>
                <tr>
                    <th>Repair Type</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th style="text-align: right;">Cost (KES)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($item->repairs as $repair)
                    <tr>
                        <td>{{ $repair->repair_type ?? 'General Repair' }}</td>
                        <td>{{ $repair->status }}</td>
                        <td>{{ $repair->completed_at ? $repair->completed_at->format('M d, Y') : ($repair->started_at ? $repair->started_at->format('M d, Y') : 'N/A') }}</td>
                        <td style="text-align: right;">{{ number_format($repair->actual_cost ?? $repair->estimated_cost, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($item->notes)
        <div class="section-header">Audit Notes</div>
        <div style="background-color: #faf8f6; padding: 15px; border-radius: 8px; font-style: italic; color: #5c5550; font-size: 11px;">
            "{{ $item->notes }}"
        </div>
    @endif
@endsection
