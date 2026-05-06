@extends('layouts.pdf')

@section('title', 'Dispatch Checklist - ' . $event->name)
@section('report_name', 'Warehouse Dispatch Checklist')

@section('content')
    @php
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

    <div class="section-header">Event & Logistics Details</div>
    <table>
        <tr>
            <th width="20%">Event Name</th>
            <td>{{ $event->name }}</td>
            <th width="20%">Loading Date</th>
            <td>{{ $event->loading_date->format('M d, Y') }}</td>
        </tr>
        <tr>
            <th>Venue</th>
            <td>{{ $event->venue }}</td>
            <th>Setup Date</th>
            <td>{{ $event->setup_date->format('M d, Y') }}</td>
        </tr>
        <tr>
            <th>Total Items</th>
            <td colspan="3">{{ $event->items->count() }} Assets to be dispatched</td>
        </tr>
    </table>

    <div class="section-header">Asset Dispatch Checklist</div>
    @if($event->eventItems->count() > 0)
        <table>
            <thead>
                <tr>
                    <th width="8%">Loaded</th>
                    <th width="12%">Image</th>
                    <th width="25%">Item Name</th>
                    <th width="15%">Condition (Out)</th>
                    <th width="40%">Dispatch Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($event->eventItems as $ei)
                    @php
                        $item = $ei->item;
                        $primaryImage = $item->images->firstWhere('is_primary', true) ?? $item->images->first();
                        $imagePath = null;
                        if ($primaryImage) {
                            $imagePath = public_path('storage/' . $primaryImage->image_path);
                        } elseif ($item->image_path) {
                            $imagePath = public_path('storage/' . $item->image_path);
                        }
                    @endphp
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">
                            <div style="width: 15px; height: 15px; border: 1px solid #000; margin: 0 auto;"></div>
                        </td>
                        <td style="text-align: center; padding: 5px;">
                            @if($imagePath && file_exists($imagePath))
                                <img src="{{ $imagePath }}" style="width: 45px; height: 45px; object-fit: cover; border-radius: 4px;">
                            @else
                                <div style="width: 45px; height: 45px; background-color: #f8f7f5; border: 1px dashed #ece8e3; border-radius: 4px; line-height: 45px; font-size: 8px; color: #a09890;">N/A</div>
                            @endif
                        </td>
                        <td style="vertical-align: middle;">
                            <strong>{{ $item->name }}</strong><br>
                            <span style="font-size: 9px; color: #a09890;">#ITM-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td style="vertical-align: middle; text-align: center;">
                            @if($ei->condition_on_dispatch)
                                <span style="font-weight: bold; color: {{ $getConditionColor($ei->condition_on_dispatch) }};">
                                    {{ $getConditionText($ei->condition_on_dispatch) }}
                                </span>
                            @else
                                <span style="color: #a09890; font-size: 10px;">Pending</span>
                            @endif
                        </td>
                        <td style="vertical-align: middle; font-size: 10px; font-style: italic; border-bottom: 1px dashed #ece8e3;">
                            {{ $ei->dispatch_notes ?? '________________________________________' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="font-size: 11px; color: #a09890; font-style: italic; margin-bottom: 20px;">No items have been attached to this event manifest.</p>
    @endif
    
    <div style="margin-top: 30px; font-size: 11px;">
        <p><strong>Loading Supervisor:</strong> ___________________________________</p>
        <p><strong>Driver / Transporter:</strong> ___________________________________</p>
        <p><strong>Dispatch Time:</strong> ___________________________________</p>
    </div>
@endsection
