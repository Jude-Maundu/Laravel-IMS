@extends('layouts.pdf')

@section('title', 'Cleaning Bay Report')
@section('report_name', 'Cleaning Bay Activity Manifest')

@section('content')
    <div class="section-header">Cleaning Bay Summary</div>
    <table>
        <tr>
            <th width="30%">Total Items Under Cleaning</th>
            <td width="70%" style="font-size: 16px; font-weight: bold; color: #0F6E56;">{{ $items->count() }} Assets</td>
        </tr>
    </table>

    <div class="section-header">Items Manifest</div>
    @if($items->count() > 0)
        <table>
            <thead>
                <tr>
                    <th width="12%">Image</th>
                    <th width="15%">ID</th>
                    <th width="28%">Item Name</th>
                    <th width="15%">Category</th>
                    <th width="30%">Date Sent to Cleaning</th>
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
                        <td style="text-align: center; padding: 5px;">
                            @if($imagePath && file_exists($imagePath))
                                <img src="{{ $imagePath }}" style="width: 45px; height: 45px; object-fit: cover; border-radius: 4px;">
                            @else
                                <div style="width: 45px; height: 45px; background-color: #f8f7f5; border: 1px dashed #ece8e3; border-radius: 4px; line-height: 45px; font-size: 8px; color: #a09890;">N/A</div>
                            @endif
                        </td>
                        <td style="font-size: 10px; font-weight: bold; vertical-align: middle;">
                            #ITM-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}
                        </td>
                        <td style="font-weight: bold; vertical-align: middle;">{{ $item->name }}</td>
                        <td style="vertical-align: middle;">{{ $item->category }}</td>
                        <td style="vertical-align: middle; font-size: 11px; color: #5c5550;">
                            {{ $item->updated_at->format('M d, Y, h:i A') }}<br>
                            <span style="font-size: 9px; color: #a09890;">by {{ $item->last_updated_by ?? 'System' }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="font-size: 11px; color: #a09890; font-style: italic; margin-bottom: 20px;">There are no items currently logged in the cleaning bay.</p>
    @endif
@endsection
