@extends('layouts.pdf')

@section('title', 'Maintenance & Repairs Report')
@section('report_name', 'System Maintenance & Repairs Audit')

@section('content')
    <div class="section-header">Financial Summary</div>
    <table>
        <tr>
            <th width="50%" style="background-color: #fcebeb; color: #A32D2D;">Total Actual Cost (Completed Repairs)</th>
            <td width="50%" style="font-size: 18px; font-weight: bold; color: #CC0000; text-align: center;">KES {{ number_format($totalActual, 0) }}</td>
        </tr>
        <tr>
            <th width="50%" style="background-color: #faeeda; color: #854F0B;">Total Estimated Cost (Pending/In Progress)</th>
            <td width="50%" style="font-size: 14px; font-weight: bold; color: #854F0B; text-align: center;">KES {{ number_format($totalEstimated, 0) }}</td>
        </tr>
    </table>

    <div class="section-header">Repair Log</div>
    @if($repairs->count() > 0)
        <table>
            <thead>
                <tr>
                    <th width="10%">Image</th>
                    <th width="20%">Item Name</th>
                    <th width="20%">Repair Type</th>
                    <th width="15%">Status</th>
                    <th width="20%">Date (Started/Completed)</th>
                    <th width="15%">Cost (KES)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($repairs as $repair)
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
                    <tr>
                        <td style="text-align: center; padding: 5px;">
                            @if($imagePath && file_exists($imagePath))
                                <img src="{{ $imagePath }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                            @else
                                <div style="width: 40px; height: 40px; background-color: #f8f7f5; border: 1px dashed #ece8e3; border-radius: 4px; line-height: 40px; font-size: 8px; color: #a09890;">N/A</div>
                            @endif
                        </td>
                        <td style="vertical-align: middle;">
                            <strong>{{ $item->name }}</strong><br>
                            <span style="font-size: 9px; color: #a09890;">#ITM-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td style="vertical-align: middle;">{{ $repair->repair_type ?? 'General Repair' }}</td>
                        <td style="vertical-align: middle;">
                            @if($repair->status === 'Completed')
                                <span style="font-weight: bold; color: #3B6D11;">Completed</span>
                            @elseif($repair->status === 'In Progress')
                                <span style="font-weight: bold; color: #185FA5;">In Progress</span>
                            @else
                                <span style="font-weight: bold; color: #854F0B;">{{ $repair->status }}</span>
                            @endif
                        </td>
                        <td style="vertical-align: middle; font-size: 10px; color: #5c5550;">
                            @if($repair->completed_at)
                                C: {{ $repair->completed_at->format('M d, Y') }}
                            @elseif($repair->started_at)
                                S: {{ $repair->started_at->format('M d, Y') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td style="vertical-align: middle; text-align: right; font-weight: bold;">
                            {{ number_format($repair->actual_cost ?? $repair->estimated_cost, 0) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="font-size: 11px; color: #a09890; font-style: italic; margin-bottom: 20px;">No repair logs found.</p>
    @endif
@endsection
