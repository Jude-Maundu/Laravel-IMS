@extends('layouts.pdf')

@section('title', 'Event Report - ' . $event->name)
@section('report_name', 'Comprehensive Event Summary')

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

    <div class="section-header">Event Profile</div>
    <table>
        <tr>
            <th width="20%">Event Name</th>
            <td width="30%" style="font-weight: bold; color: #CC0000;">{{ $event->name }}</td>
            <th width="20%">Status</th>
            <td width="30%">
                <span class="status-badge status-{{ strtolower(str_replace(' ', '', $event->status)) }}">
                    {{ $event->status }}
                </span>
            </td>
        </tr>
        <tr>
            <th>Client Name</th>
            <td>{{ $event->client_name }}</td>
            <th>Venue</th>
            <td>{{ $event->venue }}</td>
        </tr>
        <tr>
            <th>Event Date</th>
            <td>{{ $event->event_date->format('M d, Y') }}</td>
            <th>Setup Date</th>
            <td>{{ $event->setup_date->format('M d, Y') }}</td>
        </tr>
        <tr>
            <th>Loading Date</th>
            <td>{{ $event->loading_date->format('M d, Y') }}</td>
            <th>Setdown Date</th>
            <td>{{ $event->setdown_date ? $event->setdown_date->format('M d, Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Financials</th>
            <td>Cost: KES {{ number_format($event->cost ?? 0, 0) }}</td>
            <th>Total Items</th>
            <td>{{ $event->items->count() }} Assets Dispatched</td>
        </tr>
    </table>

    @if($event->notes)
        <div style="background-color: #faf8f6; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ece8e3;">
            <div style="font-size: 10px; font-weight: bold; color: #a09890; text-transform: uppercase; margin-bottom: 5px;">General Event Notes</div>
            <div style="font-size: 11px; color: #5c5550; font-style: italic;">{{ $event->notes }}</div>
        </div>
    @endif

    <div class="section-header">Assigned Event Team</div>
    @if($event->staff && $event->staff->count() > 0)
        <table>
            <thead>
                <tr>
                    <th width="30%">Name</th>
                    <th width="30%">Role</th>
                    <th width="40%">Contact</th>
                </tr>
            </thead>
            <tbody>
                @foreach($event->staff as $staff)
                    <tr>
                        <td style="font-weight: bold;">
                            {{ $staff->name }}
                            @if($staff->pivot->role === 'leader')
                                <span style="font-size: 9px; color: #CC0000; margin-left: 5px;">(Leader)</span>
                            @endif
                        </td>
                        <td>{{ ucfirst($staff->pivot->role) }}</td>
                        <td>{{ $staff->email }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="font-size: 11px; color: #a09890; font-style: italic; margin-bottom: 20px;">No team members have been assigned to this event yet.</p>
    @endif

    <div class="section-header">Dispatched Asset Manifest</div>
    @if($event->eventItems->count() > 0)
        <table>
            <thead>
                <tr>
                    <th width="12%">Image</th>
                    <th width="28%">Item Name</th>
                    <th width="15%">Dispatch Condition</th>
                    <th width="15%">Return Condition</th>
                    <th width="30%">Post-Event Status</th>
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
                            @if($ei->condition_on_dispatch)
                                <span style="font-weight: bold; color: {{ $getConditionColor($ei->condition_on_dispatch) }};">
                                    {{ $getConditionText($ei->condition_on_dispatch) }}
                                </span>
                            @else
                                <span style="color: #a09890;">-</span>
                            @endif
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
                        <td style="vertical-align: middle;">
                            @if($ei->return_destination)
                                @if($ei->return_destination === 'warehouse')
                                    <span style="color: #3B6D11; font-size: 10px; font-weight: bold;">Returned to Warehouse</span>
                                @elseif($ei->return_destination === 'cleaning')
                                    <span style="color: #0F6E56; font-size: 10px; font-weight: bold;">Sent for Cleaning</span>
                                @elseif($ei->return_destination === 'repair')
                                    <span style="color: #CC0000; font-size: 10px; font-weight: bold;">Sent for Repair</span>
                                @endif
                                @if($ei->return_notes)
                                    <div style="font-size: 9px; color: #5c5550; font-style: italic; margin-top: 2px;">"{{ $ei->return_notes }}"</div>
                                @endif
                            @else
                                <span style="color: #a09890; font-size: 10px;">Not Returned</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="font-size: 11px; color: #a09890; font-style: italic; margin-bottom: 20px;">No items have been attached to this event manifest.</p>
    @endif
@endsection
