@extends('layouts.pdf')

@section('title', 'Return & Triage - ' . $event->name)
@section('report_name', 'Event Return & Triage Report')

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

    <div class="section-header">Event Details</div>
    <table>
        <tr>
            <th width="20%">Event Name</th>
            <td>{{ $event->name }}</td>
            <th width="20%">Client Name</th>
            <td>{{ $event->client_name }}</td>
        </tr>
        <tr>
            <th>Setdown Date</th>
            <td>{{ $event->setdown_date ? $event->setdown_date->format('M d, Y') : 'N/A' }}</td>
            <th>Total Returned</th>
            <td>{{ $event->eventItems->where('return_processed', true)->count() }} / {{ $event->items->count() }}</td>
        </tr>
    </table>

    <div class="section-header">Triage Breakdown</div>
    @php
        $warehouse = $event->eventItems->where('return_destination', 'warehouse')->count();
        $cleaning = $event->eventItems->where('return_destination', 'cleaning')->count();
        $repair = $event->eventItems->where('return_destination', 'repair')->count();
    @endphp
    <table>
        <tr>
            <th width="33%">Returned to Warehouse</th>
            <th width="33%">Sent for Cleaning</th>
            <th width="34%">Sent for Repair</th>
        </tr>
        <tr>
            <td style="font-size: 16px; font-weight: bold; color: #3B6D11; text-align: center;">{{ $warehouse }} Items</td>
            <td style="font-size: 16px; font-weight: bold; color: #0F6E56; text-align: center;">{{ $cleaning }} Items</td>
            <td style="font-size: 16px; font-weight: bold; color: #CC0000; text-align: center;">{{ $repair }} Items</td>
        </tr>
    </table>

    <div class="section-header">Item Return Audit</div>
    @if($event->eventItems->count() > 0)
        <table>
            <thead>
                <tr>
                    <th width="12%">Image</th>
                    <th width="28%">Item Name</th>
                    <th width="15%">Return Condition</th>
                    <th width="15%">Destination</th>
                    <th width="30%">Notes</th>
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
                            @if($ei->condition_on_return)
                                <span style="font-weight: bold; color: {{ $getConditionColor($ei->condition_on_return) }};">
                                    {{ $getConditionText($ei->condition_on_return) }}
                                </span>
                            @else
                                <span style="color: #a09890; font-size: 10px;">Pending</span>
                            @endif
                        </td>
                        <td style="vertical-align: middle; text-align: center;">
                            @if($ei->return_destination)
                                @if($ei->return_destination === 'warehouse')
                                    <span style="color: #3B6D11; font-weight: bold;">Warehouse</span>
                                @elseif($ei->return_destination === 'cleaning')
                                    <span style="color: #0F6E56; font-weight: bold;">Cleaning</span>
                                @elseif($ei->return_destination === 'repair')
                                    <span style="color: #CC0000; font-weight: bold;">Repair</span>
                                @endif
                            @else
                                <span style="color: #a09890; font-size: 10px;">Pending</span>
                            @endif
                        </td>
                        <td style="vertical-align: middle; font-size: 10px; font-style: italic;">
                            {{ $ei->return_notes ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="font-size: 11px; color: #a09890; font-style: italic; margin-bottom: 20px;">No items have been attached to this event manifest.</p>
    @endif
@endsection
