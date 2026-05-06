@extends('layouts.pdf')

@section('title', 'Activity Log Report')
@section('report_name', 'System Operational Log')

@section('content')
    <div class="section-header">Recent Activity Audit (Last 100 entries)</div>
    @if($activities->count() > 0)
        <table>
            <thead>
                <tr>
                    <th width="15%">Date/Time</th>
                    <th width="15%">Action</th>
                    <th width="20%">Item Affected</th>
                    <th width="15%">User</th>
                    <th width="35%">Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activities as $log)
                    <tr>
                        <td style="font-size: 10px; color: #5c5550; vertical-align: middle;">
                            {{ $log->created_at->format('M d, Y H:i') }}
                        </td>
                        <td style="vertical-align: middle;">
                            <span style="font-weight: bold; color: #0f0f0f;">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span>
                        </td>
                        <td style="vertical-align: middle;">
                            @if($log->item)
                                <strong>{{ $log->item->name }}</strong><br>
                                <span style="font-size: 9px; color: #a09890;">#ITM-{{ str_pad($log->item->id, 4, '0', STR_PAD_LEFT) }}</span>
                            @else
                                <span style="color: #a09890;">N/A</span>
                            @endif
                        </td>
                        <td style="vertical-align: middle;">{{ $log->user->name ?? 'System' }}</td>
                        <td style="font-size: 10px; font-style: italic; vertical-align: middle;">
                            {{ $log->description ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="font-size: 11px; color: #a09890; font-style: italic; margin-bottom: 20px;">No recent activities found in the log.</p>
    @endif
@endsection
