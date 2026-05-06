@extends('layouts.pdf')

@section('title', 'Inventory Report')
@section('report_name', 'Full Inventory Status & Asset Audit')

@section('content')
    <table>
        <thead>
            <tr>
                <th width="10%">Image</th>
                <th width="12%">ID</th>
                <th width="20%">Item Name</th>
                <th width="15%">Category</th>
                <th width="12%">Status</th>
                <th width="13%">Location</th>
                <th width="18%">Last Activity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                @php
                    $primaryImage = $item->images->firstWhere('is_primary', true) ?? $item->images->first();
                    $imagePath = null;
                    if ($primaryImage) {
                        $imagePath = public_path('storage/' . $primaryImage->image_path);
                    } elseif ($item->image_path) {
                        $imagePath = public_path('storage/' . $item->image_path);
                    }
                @endphp
                <tr>
                    <td style="padding: 5px; text-align: center;">
                        @if($imagePath && file_exists($imagePath))
                            <img src="{{ $imagePath }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                        @else
                            <div style="width: 40px; height: 40px; background-color: #f8f7f5; border: 1px dashed #ece8e3; border-radius: 4px; line-height: 40px; font-size: 7px; color: #a09890;">N/A</div>
                        @endif
                    </td>
                    <td style="font-size: 10px; vertical-align: middle;">#ITM-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td style="font-weight: bold; vertical-align: middle;">{{ $item->name }}</td>
                    <td style="vertical-align: middle;">{{ $item->category }}</td>
                    <td style="vertical-align: middle;">
                        <span class="status-badge status-{{ strtolower(str_replace(' ', '', $item->status)) }}">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td style="vertical-align: middle;">{{ $item->location }}</td>
                    <td style="font-size: 10px; color: #5c5550; vertical-align: middle;">
                        {{ $item->updated_at->format('M d, Y') }}<br>
                        <small style="color: #a09890;">by {{ $item->last_updated_by ?? 'System' }}</small>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
