@extends('layouts.app')

@section('title', 'Repairs Management')
@section('page-title', 'Repairs')

@section('content')
<div class="ev-list-header">
    <div>
        <h1 class="ev-list-title">Maintenance & Repairs</h1>
        <p class="ev-list-sub">Track and manage item damages, ongoing repairs, and maintenance costs.</p>
    </div>
    <div class="ev-list-header-right">
        <a href="{{ route('repairs.create') }}" class="ev-btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Log New Repair
        </a>
    </div>
</div>

@if(session('success'))
<div class="ev-flash ev-flash-success">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    {{ session('success') }}
</div>
@endif

<form method="GET" action="{{ route('repairs.index') }}" class="ev-filter-bar">
    <div class="ev-search-box">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#a09890" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by item name..." class="ev-search-input">
    </div>
    
    <select name="status" class="ev-filter-select" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
        <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
        <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
        <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
    </select>
    
    @if(request('search') || request('status'))
        <a href="{{ route('repairs.index') }}" class="ev-clear-btn">Clear Filters</a>
    @endif
</form>

<div class="ev-table-wrap">
    <table class="ev-table">
        <thead>
            <tr>
                <th width="35%">Repair Job & Item</th>
                <th width="15%">Status</th>
                <th width="15%">Type</th>
                <th width="20%">Technician & Date</th>
                <th width="15%">Cost (KES)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($repairs as $repair)
            <tr class="ev-row" onclick="window.location='{{ route('repairs.show', $repair) }}'">
                <td>
                    <div class="ev-td-inner">
                        @php
                            $item = $repair->item;
                            $primaryImage = $repair->damage_image_path ? asset('storage/' . $repair->damage_image_path) : ($item->images->firstWhere('is_primary', true)->image_path ?? $item->image_path ?? null);
                        @endphp
                        <div class="evsh-item-thumb">
                            @if($primaryImage)
                                <img src="{{ is_string($primaryImage) && !str_starts_with($primaryImage, 'http') ? asset('storage/' . $primaryImage) : $primaryImage }}">
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d0c8c0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            @endif
                        </div>
                        <div class="ev-name-col">
                            <span class="ev-event-name">#RPR-{{ str_pad($repair->id, 4, '0', STR_PAD_LEFT) }}</span>
                            <span class="ev-client-name">{{ $item->name }} (#ITM-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }})</span>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="evsh-cond-badge 
                        @if($repair->status == 'Completed') ev-cond-excellent
                        @elseif($repair->status == 'In Progress') ev-cond-good
                        @elseif($repair->status == 'Cancelled') ev-cond-poor
                        @else ev-cond-average
                        @endif
                    ">
                        {{ $repair->status }}
                    </span>
                </td>
                <td>
                    <span class="ev-client-name" style="color: #3a3530; font-weight: 500;">{{ $repair->repair_type }}</span>
                </td>
                <td>
                    <div class="ev-date-text">
                        <span class="ev-date-main">{{ $repair->technician_name ?? 'Unassigned' }}</span><br>
                        @if($repair->completed_at)
                            C: {{ $repair->completed_at->format('M d, Y') }}
                        @elseif($repair->started_at)
                            S: {{ $repair->started_at->format('M d, Y') }}
                        @else
                            Pending
                        @endif
                    </div>
                </td>
                <td>
                    <div class="ev-date-text">
                        <span class="ev-date-main" style="color: #CC0000;">
                            {{ number_format($repair->actual_cost ?? $repair->estimated_cost, 0) }}
                        </span><br>
                        <span style="font-size: 9px; color: #a09890;">{{ $repair->actual_cost ? 'Actual' : 'Estimated' }}</span>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="ev-empty-cell">
                    <div class="ev-empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#d0c8c0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                        <p>No repair records found.</p>
                        <span>Try adjusting your filters or log a new repair.</span>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($repairs->hasPages())
    <div class="ev-pagination">
        <div class="ev-pg-info">
            Showing {{ $repairs->firstItem() }} to {{ $repairs->lastItem() }} of {{ $repairs->total() }} repairs
        </div>
        <div class="ev-pg-links">
            {{ $repairs->links('vendor.pagination.custom') }}
        </div>
    </div>
    @endif
</div>
@endsection
