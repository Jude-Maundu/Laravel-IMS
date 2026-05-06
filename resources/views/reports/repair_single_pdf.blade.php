@extends('layouts.pdf')

@section('title', 'Repair Job Report - ' . $repair->item->name)
@section('report_name', 'Comprehensive Repair & Damage Audit')

@section('content')
    <div style="display: table; width: 100%; margin-bottom: 20px;">
        <div style="display: table-row;">
            <div style="display: table-cell; width: 180px; vertical-align: top; padding-right: 20px;">
                @if($repair->damage_image_path && file_exists(public_path('storage/' . $repair->damage_image_path)))
                    <div style="font-size: 10px; font-weight: bold; color: #a09890; margin-bottom: 5px; text-transform: uppercase;">Damage Evidence</div>
                    <img src="{{ public_path('storage/' . $repair->damage_image_path) }}" style="width: 180px; height: 180px; object-fit: cover; border-radius: 8px; border: 1px solid #CC0000;">
                @else
                    @php
                        $item = $repair->item;
                        $primaryImage = $item->images->firstWhere('is_primary', true) ?? $item->images->first();
                        $imagePath = null;
                        if ($primaryImage) {
                            $imagePath = public_path('storage/' . $primaryImage->image_path);
                        } elseif ($item->image_path) {
                            $imagePath = public_path('storage/' . $item->image_path);
                        }
                    @endphp
                    <div style="font-size: 10px; font-weight: bold; color: #a09890; margin-bottom: 5px; text-transform: uppercase;">Asset Image</div>
                    @if($imagePath && file_exists($imagePath))
                        <img src="{{ $imagePath }}" style="width: 180px; height: 180px; object-fit: cover; border-radius: 8px; border: 1px solid #ece8e3;">
                    @else
                        <div style="width: 180px; height: 180px; background-color: #f8f7f5; border: 1px dashed #ece8e3; border-radius: 8px; text-align: center; line-height: 180px; color: #a09890; font-size: 10px;">
                            No Image Available
                        </div>
                    @endif
                @endif
            </div>
            <div style="display: table-cell; vertical-align: top;">
                <h2 style="margin: 0 0 10px 0; color: #0f0f0f; font-size: 18px;">{{ $repair->item->name }}</h2>
                <p style="margin: 0 0 15px 0; color: #5c5550; font-size: 12px;">ID Number: <strong>#ITM-{{ str_pad($repair->item->id, 4, '0', STR_PAD_LEFT) }}</strong></p>
                
                <table style="margin-bottom: 0;">
                    <tr>
                        <th style="padding: 5px; background: transparent; border: none; width: 120px;">Repair Status:</th>
                        <td style="padding: 5px; border: none;">
                            <span style="font-weight: bold; color: {{ $repair->status === 'Completed' ? '#3B6D11' : ($repair->status === 'In Progress' ? '#185FA5' : '#854F0B') }};">
                                {{ $repair->status }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th style="padding: 5px; background: transparent; border: none;">Repair Type:</th>
                        <td style="padding: 5px; border: none;">{{ $repair->repair_type ?? 'General Repair' }}</td>
                    </tr>
                    <tr>
                        <th style="padding: 5px; background: transparent; border: none;">Technician:</th>
                        <td style="padding: 5px; border: none;">{{ $repair->technician_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th style="padding: 5px; background: transparent; border: none;">Date Started:</th>
                        <td style="padding: 5px; border: none;">{{ $repair->started_at ? $repair->started_at->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th style="padding: 5px; background: transparent; border: none;">Date Completed:</th>
                        <td style="padding: 5px; border: none;">{{ $repair->completed_at ? $repair->completed_at->format('M d, Y') : 'Pending' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="section-header">Damage & Repair Financials</div>
    <table>
        <tr>
            <th width="50%">Estimated Cost (Before Repair)</th>
            <th width="50%">Actual Final Cost (Materials & Labor)</th>
        </tr>
        <tr>
            <td style="font-size: 16px; font-weight: bold; color: #854F0B; text-align: center;">KES {{ number_format($repair->estimated_cost ?? 0, 0) }}</td>
            <td style="font-size: 18px; font-weight: bold; color: #CC0000; text-align: center;">
                @if($repair->status === 'Completed')
                    KES {{ number_format($repair->actual_cost ?? 0, 0) }}
                @else
                    <span style="font-size: 12px; font-weight: normal; color: #a09890;">Pending Completion</span>
                @endif
            </td>
        </tr>
    </table>

    <div class="section-header">Damage Assessment & Scope of Work</div>
    <div style="background-color: #faf8f6; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ece8e3;">
        @if($repair->description)
            <div style="font-size: 11px; color: #3a3530; line-height: 1.6;">{{ $repair->description }}</div>
        @else
            <div style="font-size: 11px; color: #a09890; font-style: italic;">No detailed damage description was provided.</div>
        @endif
    </div>

    <div class="section-header">Materials Required & Consumed</div>
    <div style="background-color: #faf8f6; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ece8e3;">
        @if($repair->materials_required)
            <div style="font-size: 11px; color: #3a3530; line-height: 1.6;">{!! nl2br(e($repair->materials_required)) !!}</div>
        @else
            <div style="font-size: 11px; color: #a09890; font-style: italic;">No specific materials were listed for this repair job.</div>
        @endif
    </div>

    @if($repair->notes)
        <div class="section-header">Technician Notes</div>
        <div style="background-color: #fff8f8; padding: 15px; border-radius: 8px; border: 1px solid #f5c0c0;">
            <div style="font-size: 11px; color: #CC0000; font-style: italic; line-height: 1.6;">
                "{{ $repair->notes }}"
            </div>
        </div>
    @endif
@endsection
