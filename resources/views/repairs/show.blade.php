@extends('layouts.app')

@section('title', 'Repair Details - #' . str_pad($repair->id, 4, '0', STR_PAD_LEFT))
@section('page-title', 'Repairs')

@section('content')
<div class="evsh-top-bar">
    <div class="evsh-breadcrumb">
        <a href="{{ route('repairs.index') }}" class="wiz-back-link" style="text-decoration: none; color: #a09890;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline; margin-bottom: 2px;"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Repairs List
        </a>
        <span class="evsh-bc-sep">/</span>
        <span class="evsh-bc-current">Job #RPR-{{ str_pad($repair->id, 4, '0', STR_PAD_LEFT) }}</span>
    </div>
    <div class="evsh-top-actions">
        <a href="{{ route('reports.repair.pdf', $repair->id) }}" target="_blank" class="evsh-btn-outline">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Print Report
        </a>
        <a href="{{ route('repairs.edit', $repair) }}" class="evsh-btn-status">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit Job
        </a>
    </div>
</div>

@if(session('success'))
<div class="ev-flash ev-flash-success">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    {{ session('success') }}
</div>
@endif

<!-- Hero Section -->
<div class="evsh-hero">
    <div class="evsh-hero-icon" style="background-color: #f5f1ed; border-color: #ece8e3;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#5c5550" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
    </div>
    <div class="evsh-hero-content">
        <div class="evsh-hero-name-row">
            <h1 class="evsh-hero-name">{{ $repair->item->name }}</h1>
            <span class="wiz-tag {{ $repair->status === 'Completed' ? 'wiz-tag-avail' : 'wiz-tag-unavail' }}" style="margin-left: 10px;">
                {{ $repair->status }}
            </span>
        </div>
        <p class="evsh-hero-client">Repair Type: <strong>{{ $repair->repair_type }}</strong></p>
        <div class="evsh-hero-meta">
            <span class="evsh-meta-tag">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Started: {{ $repair->started_at ? $repair->started_at->format('M d, Y') : 'N/A' }}
            </span>
            <span class="evsh-meta-tag">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Completed: {{ $repair->completed_at ? $repair->completed_at->format('M d, Y') : 'Pending' }}
            </span>
        </div>
    </div>
</div>

<!-- KPIs -->
<div class="evsh-kpi-row">
    <div class="evsh-kpi">
        <div class="evsh-kpi-icon evsh-icon-amber">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div>
            <span class="evsh-kpi-label">Estimated Cost</span>
            <span class="evsh-kpi-val" style="color: #854F0B;">KES {{ number_format($repair->estimated_cost ?? 0, 0) }}</span>
        </div>
    </div>
    <div class="evsh-kpi">
        <div class="evsh-kpi-icon evsh-icon-red">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div>
            <span class="evsh-kpi-label">Actual Cost</span>
            <span class="evsh-kpi-val" style="color: #CC0000;">
                @if($repair->status === 'Completed')
                    KES {{ number_format($repair->actual_cost ?? 0, 0) }}
                @else
                    <span style="font-size: 11px; color: #a09890; font-weight: normal;">Pending</span>
                @endif
            </span>
        </div>
    </div>
    <div class="evsh-kpi">
        <div class="evsh-kpi-icon evsh-icon-blue">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <div>
            <span class="evsh-kpi-label">Technician</span>
            <span class="evsh-kpi-val" style="font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100px; display: inline-block;">{{ $repair->technician_name ?? 'Unassigned' }}</span>
        </div>
    </div>
</div>

<div class="evsh-content-grid">
    <div class="evsh-main-col" style="gap: 12px; display: flex; flex-direction: column;">
        
        <!-- Damage Assessment -->
        <div class="evsh-section-card">
            <div class="evsh-section-head">
                <div class="evsh-section-title">Damage Assessment</div>
                <div class="evsh-section-sub">Description of the issue to be fixed.</div>
            </div>
            <div style="padding: 16px;">
                <p class="evsh-notes-text">{{ $repair->description }}</p>
            </div>
        </div>

        <!-- Materials Required -->
        <div class="evsh-section-card">
            <div class="evsh-section-head">
                <div class="evsh-section-title">Materials Required</div>
                <div class="evsh-section-sub">Components or items consumed during repair.</div>
            </div>
            <div style="padding: 16px;">
                @if($repair->materials_required)
                    <p class="evsh-notes-text">{!! nl2br(e($repair->materials_required)) !!}</p>
                @else
                    <p class="evsh-notes-text" style="color: #a09890; font-style: italic;">No specific materials logged.</p>
                @endif
            </div>
        </div>

        <!-- Notes -->
        @if($repair->notes)
        <div class="evsh-section-card">
            <div class="evsh-section-head">
                <div class="evsh-section-title">Internal Notes</div>
            </div>
            <div style="padding: 16px; background-color: #faf8f6;">
                <p class="evsh-notes-text" style="font-style: italic;">{{ $repair->notes }}</p>
            </div>
        </div>
        @endif

    </div>

    <div class="evsh-side-col">
        <!-- Damage Evidence Image -->
        <div class="evsh-side-card">
            <div class="evsh-side-card-title">Damage Evidence</div>
            @if($repair->damage_image_path)
                <div style="border-radius: 8px; overflow: hidden; border: 1px solid #ece8e3;">
                    <img src="{{ asset('storage/' . $repair->damage_image_path) }}" style="width: 100%; height: auto; display: block;" alt="Damage Photo">
                </div>
            @else
                <div style="aspect-ratio: 4/3; background-color: #f8f7f5; border: 1px dashed #ece8e3; border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; padding: 20px; text-align: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d0c8c0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    <span style="font-size: 10px; color: #a09890;">No damage photo provided.</span>
                </div>
            @endif
        </div>
        
        <!-- Asset Quick Info -->
        <div class="evsh-side-card">
            <div class="evsh-side-card-title">Asset Details</div>
            <div class="itd-side-row">
                <span class="itd-side-label">Name</span>
                <span class="itd-side-val"><a href="{{ route('inventory.show', $repair->item->id) }}" style="color: #CC0000; text-decoration: none;">{{ $repair->item->name }}</a></span>
            </div>
            <div class="itd-side-row">
                <span class="itd-side-label">ID</span>
                <span class="itd-side-val">#ITM-{{ str_pad($repair->item->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="itd-side-row">
                <span class="itd-side-label">Category</span>
                <span class="itd-side-val">{{ $repair->item->category }}</span>
            </div>
            <div class="itd-side-row">
                <span class="itd-side-label">Status</span>
                <span class="itd-side-val">{{ $repair->item->status }}</span>
            </div>
        </div>

    </div>
</div>
@endsection
