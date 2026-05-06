@extends('layouts.pdf')

@section('title', 'Comprehensive Event & Dispatch Log')
@section('report_name', 'Event Dispatch & Return Manifest Log')

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

    <div style="margin-bottom: 20px; font-size: 11px; color: #5c5550; font-style: italic;">
        This report contains a chronological log of all events, their assigned logistical teams, and the complete manifest of items dispatched and returned.
    </div>

    @forelse($events as $event)
        <div style="margin-bottom: 30px; border: 1px solid #ece8e3; border-radius: 8px; padding: 15px; background-color: #faf8f6; page-break-inside: avoid;">
            
            <!-- Event Header -->
            <table style="width: 100%; margin-bottom: 0; background: transparent;">
                <tr>
                    <td style="border: none; padding: 0; width: 70%;">
                        <h3 style="margin: 0 0 5px 0; color: #CC0000; font-size: 16px;">
                            {{ $event->name }}
                        </h3>
                        <div style="font-size: 10px; color: #5c5550;">
                            <strong>Date:</strong> {{ $event->event_date->format('M d, Y') }} | 
                            <strong>Client:</strong> {{ $event->client_name }} | 
                            <strong>Venue:</strong> {{ $event->venue }}
                        </div>
                    </td>
                    <td style="border: none; padding: 0; text-align: right; width: 30%;">
                        <span class="status-badge status-{{ strtolower(str_replace(' ', '', $event->status)) }}">
                            {{ $event->status }}
                        </span>
                    </td>
                </tr>
            </table>

            <!-- Team Assignment -->
            <div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #ece8e3; font-size: 10px;">
                <strong style="color: #0f0f0f;">Assigned Logistics Team:</strong> 
                @if($event->staff && $event->staff->count() > 0)
                    @php
                        $staffMembers = $event->staff;
                    @endphp
                    @foreach($staffMembers as $member)
                        <span style="color: #185FA5;">{{ $member->name }}</span>
                        @if($member->pivot->role === 'leader')
                            <span style="color: #CC0000; font-size: 8px;">(L)</span>
                        @endif
                        @if(!$loop->last), @endif
                    @endforeach
                @else
                    <span style="color: #a09890; font-style: italic;">No team assigned.</span>
                @endif
            </div>

            <!-- Items Checklist Table -->
            <h4 style="margin: 15px 0 5px 0; font-size: 11px; color: #0f0f0f; text-transform: uppercase;">Asset Manifest</h4>
            @if($event->eventItems && $event->eventItems->count() > 0)
                <table style="width: 100%; font-size: 9px; margin-bottom: 0; background: #fff;">
                    <thead>
                        <tr>
                            <th width="10%" style="padding: 5px;">Image</th>
                            <th width="30%" style="padding: 5px;">Asset ID & Name</th>
                            <th width="15%" style="padding: 5px; text-align: center;">Condition (Out)</th>
                            <th width="15%" style="padding: 5px; text-align: center;">Condition (In)</th>
                            <th width="30%" style="padding: 5px;">Post-Event Triage</th>
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
                                <td style="text-align: center; padding: 3px; border-bottom: 1px solid #f8f7f5;">
                                    @if($imagePath && file_exists($imagePath))
                                        <img src="{{ $imagePath }}" style="width: 30px; height: 30px; object-fit: cover; border-radius: 4px;">
                                    @else
                                        <div style="width: 30px; height: 30px; background-color: #f8f7f5; border: 1px dashed #ece8e3; border-radius: 4px; line-height: 30px; font-size: 6px; color: #a09890; margin: 0 auto;">N/A</div>
                                    @endif
                                </td>
                                <td style="vertical-align: middle; padding: 3px; border-bottom: 1px solid #f8f7f5;">
                                    <span style="font-weight: bold; color: #0f0f0f;">{{ $item->name }}</span><br>
                                    <span style="color: #a09890;">#ITM-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td style="vertical-align: middle; text-align: center; padding: 3px; border-bottom: 1px solid #f8f7f5;">
                                    @if($ei->condition_on_dispatch)
                                        <span style="font-weight: bold; color: {{ $getConditionColor($ei->condition_on_dispatch) }};">
                                            {{ $getConditionText($ei->condition_on_dispatch) }}
                                        </span>
                                    @else
                                        <span style="color: #a09890;">-</span>
                                    @endif
                                </td>
                                <td style="vertical-align: middle; text-align: center; padding: 3px; border-bottom: 1px solid #f8f7f5;">
                                    @if($ei->condition_on_return)
                                        <span style="font-weight: bold; color: {{ $getConditionColor($ei->condition_on_return) }};">
                                            {{ $getConditionText($ei->condition_on_return) }}
                                        </span>
                                    @else
                                        <span style="color: #a09890;">Pending</span>
                                    @endif
                                </td>
                                <td style="vertical-align: middle; padding: 3px; border-bottom: 1px solid #f8f7f5;">
                                    @if($ei->return_destination)
                                        @if($ei->return_destination === 'warehouse')
                                            <span style="color: #3B6D11; font-weight: bold;">Returned to Warehouse</span>
                                        @elseif($ei->return_destination === 'cleaning')
                                            <span style="color: #0F6E56; font-weight: bold;">Sent for Cleaning</span>
                                        @elseif($ei->return_destination === 'repair')
                                            <span style="color: #CC0000; font-weight: bold;">Sent for Repair</span>
                                        @endif
                                    @else
                                        @if($event->status === 'Active')
                                            <span style="color: #185FA5; font-style: italic;">Currently on site</span>
                                        @else
                                            <span style="color: #a09890; font-style: italic;">Awaiting Return</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="padding: 10px; text-align: center; font-size: 10px; color: #a09890; font-style: italic; background: #fff; border: 1px solid #ece8e3; border-radius: 4px;">
                    No assets were dispatched for this event.
                </div>
            @endif

        </div>
    @empty
        <p style="font-size: 12px; color: #a09890; font-style: italic; margin-bottom: 20px;">No events are currently logged in the system.</p>
    @endforelse
@endsection
