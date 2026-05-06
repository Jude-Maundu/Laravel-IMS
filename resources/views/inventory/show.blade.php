@extends('layouts.app')
@section('title', $item->name)
@section('page-title', 'Inventory')

@section('content')

@php
  $conditionLabels = [5=>'Excellent',4=>'Good',3=>'Fair',2=>'Average',1=>'Poor'];
  $conditionClasses = [
    5=>'itd-cond-excellent',4=>'itd-cond-good',
    3=>'itd-cond-fair',2=>'itd-cond-average',1=>'itd-cond-poor'
  ];
  $totalRepairCost = $item->repairs->sum('actual_cost') ?: $item->repairs->sum('estimated_cost');
  $activeRepair = $item->repairs->whereIn('status',['Pending','In Progress'])->first();
  $totalEvents = $item->events->count();
  $lastEvent = $item->events->sortByDesc(fn($e)=>$e->pivot->dispatched_at)->first();
@endphp

{{-- BREADCRUMB --}}
<div class="itd-breadcrumb">
  <a href="{{ route('inventory.index') }}" class="itd-bc-link">
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M10 3L6 8l4 5"/></svg>
    All Items
  </a>
  <span class="itd-bc-sep">/</span>
  <span class="itd-bc-cur">{{ $item->name }}</span>
</div>

{{-- FLASH --}}
@if(session('success'))
  <div class="inv-flash inv-flash-success">
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 8l2 2 5-5"/><circle cx="8" cy="8" r="6.5"/></svg>
    {{ session('success') }}
  </div>
@endif
@if(session('error'))
  <div class="inv-flash inv-flash-error">
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M8 6v3M8 11v1"/><path d="M3 13L8 3l5 10H3z"/></svg>
    {{ session('error') }}
  </div>
@endif

{{-- HERO --}}
<div class="itd-hero">
  <div class="itd-hero-img">
    @if($item->image_path)
      <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
    @else
      <svg width="48" height="48" viewBox="0 0 16 16" fill="none" stroke="#d0c8c0" stroke-width="0.8" stroke-linecap="round"><rect x="2" y="2" width="12" height="12" rx="2"/><circle cx="8" cy="7" r="2"/><path d="M2 12c1-2 3-3 6-3s5 1 6 3"/></svg>
    @endif
  </div>
  <div class="itd-hero-content">
    <div class="itd-hero-top">
      <div>
        <h1 class="itd-hero-name">{{ $item->name }}</h1>
        <p class="itd-hero-id">#ITM-{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }} &middot; {{ $item->category }}</p>
      </div>
      <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
        <x-ui.status-badge :status="$item->status" />
        @if(in_array($item->status,['Assigned','In Use']) && $item->currentEvent())
          <span class="itd-assigned-ctx">
            <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="3" width="12" height="11" rx="2"/><line x1="5" y1="1" x2="5" y2="5"/><line x1="11" y1="1" x2="11" y2="5"/><line x1="2" y1="7" x2="14" y2="7"/></svg>
            Currently at: {{ $item->currentEvent()->name }}
          </span>
        @endif
      </div>
    </div>
    <div class="itd-hero-meta">
      <span class="itd-meta-tag">
        <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M8 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path d="M8 14s5-3.5 5-7A5 5 0 0 0 3 7c0 3.5 5 7 5 7z"/></svg>
        {{ $item->location ?? 'Warehouse' }}
      </span>
      <span class="itd-meta-tag">
        <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><polyline points="8 4 8 8 11 10"/></svg>
        Last updated {{ $item->last_updated_at ? $item->last_updated_at->diffForHumans() : 'never' }}
      </span>
      @if($item->last_updated_by)
      <span class="itd-meta-tag">
        <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="5" r="3"/><path d="M2 14c0-3 2.5-5 6-5s6 2 6 5"/></svg>
        {{ $item->last_updated_by }}
      </span>
      @endif
    </div>
    <div class="itd-hero-actions">
      <a href="javascript:void(0)" onclick="document.getElementById('tab-btn-edit').click(); document.querySelector('.itd-tabs-card').scrollIntoView({behavior:'smooth'});" class="itd-btn-red">
        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M1 12l3-3 8-8 3 3-8 8-3 1z"/></svg>
        Edit Item
      </a>
      <button type="button" onclick="confirmDelete()" class="itd-btn-danger-outline">
        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M3 4h10M5 4V3h6v1M6 7v5M10 7v5"/><rect x="2" y="4" width="12" height="10" rx="1.5"/></svg>
        Delete Item
      </button>
      <form method="POST" action="{{ route('inventory.changeStatus', $item) }}" style="display:inline">
        @csrf
        <select name="status" class="itd-status-select" onchange="this.form.submit()">
          <option value="">Change Status...</option>
          @foreach(['Available','Assigned','In Use','Under Inspection','Cleaning','Cleaned','Under Repair','Repaired','Damaged','Irreparable'] as $s)
            @if($s !== $item->status)
              <option value="{{ $s }}">→ {{ $s }}</option>
            @endif
          @endforeach
        </select>
      </form>
      <a href="{{ route('inventory.index') }}?category={{ urlencode($item->category) }}" class="itd-btn-outline">
        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="2" width="12" height="12" rx="2"/><line x1="5" y1="6" x2="11" y2="6"/><line x1="5" y1="9" x2="9" y2="9"/></svg>
        Same Category
      </a>
      <button type="button" onclick="previewItemQR({{ $item->id }}, '{{ addslashes($item->name) }}')" class="itd-btn-outline">
        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="2" width="12" height="12" rx="2"/><path d="M2 6h12M6 2v12"/></svg>
        Item QR Code
      </button>
      <a href="{{ route('reports.item.pdf', $item) }}" target="_blank" class="itd-btn-outline" style="border-color: #CC0000; color: #CC0000; background: #fff8f8;">
        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 6V2h8v4M4 12H3a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1h-1M4 10h8v4H4z"/></svg>
        Download Item Audit Report
      </a>
    </div>
  </div>
</div>

{{-- KPI ROW --}}
<div class="itd-kpi-row">
  <div class="itd-kpi">
    <div class="itd-kpi-icon itd-ki-green">
      <svg width="15" height="15" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="3" width="12" height="11" rx="2"/><line x1="5" y1="1" x2="5" y2="5"/><line x1="11" y1="1" x2="11" y2="5"/></svg>
    </div>
    <div>
      <span class="itd-kpi-label">Total Events</span>
      <span class="itd-kpi-val">{{ $totalEvents }}</span>
      <span class="itd-kpi-sub">dispatches</span>
    </div>
  </div>
  <div class="itd-kpi">
    <div class="itd-kpi-icon itd-ki-amber">
      <svg width="15" height="15" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="3"/><path d="M8 1v2M8 13v2M1 8h2M13 8h2"/></svg>
    </div>
    <div>
      <span class="itd-kpi-label">Repairs</span>
      <span class="itd-kpi-val">{{ $item->repairs->count() }}</span>
      <span class="itd-kpi-sub">total repairs</span>
    </div>
  </div>
  <div class="itd-kpi">
    <div class="itd-kpi-icon itd-ki-red">
      <svg width="15" height="15" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="1" y="4" width="14" height="9" rx="2"/><path d="M1 7h14"/></svg>
    </div>
    <div>
      <span class="itd-kpi-label">Repair Cost</span>
      <span class="itd-kpi-val">KES {{ number_format($totalRepairCost, 0) }}</span>
      <span class="itd-kpi-sub">total spent</span>
    </div>
  </div>
  <div class="itd-kpi">
    <div class="itd-kpi-icon itd-ki-blue">
      <svg width="15" height="15" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M2 13V5l6-3 6 3v8"/></svg>
    </div>
    <div>
      <span class="itd-kpi-label">Location</span>
      <span class="itd-kpi-val" style="font-size:13px">{{ $item->location ?? 'Warehouse' }}</span>
      <span class="itd-kpi-sub">current site</span>
    </div>
  </div>
  <div class="itd-kpi">
    <div class="itd-kpi-icon itd-ki-purple">
      <svg width="15" height="15" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><polyline points="8 4 8 8 11 10"/></svg>
    </div>
    <div>
      <span class="itd-kpi-label">Activity Logs</span>
      <span class="itd-kpi-val">{{ $activityLogs->count() }}</span>
      <span class="itd-kpi-sub">recorded actions</span>
    </div>
  </div>
</div>

{{-- MAIN CONTENT GRID --}}
<div class="itd-content-grid">

  {{-- TABS PANEL --}}
  <div class="itd-main-col">
    <div class="itd-tabs-card">

      <div class="itd-tabs-nav">
        <div class="itd-tab active" onclick="showTab('media',this)">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="1" y="3" width="14" height="10" rx="2"/><circle cx="6" cy="8" r="1.5"/><path d="M1 11l3.5-3.5 2.5 2.5 2-2 4 4"/></svg>
          Media
        </div>
        <div class="itd-tab" onclick="showTab('pieces',this)">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="1" y="1" width="6" height="6" rx="1"/><rect x="9" y="1" width="6" height="6" rx="1"/><rect x="1" y="9" width="6" height="6" rx="1"/><rect x="9" y="9" width="6" height="6" rx="1"/></svg>
          Pieces
          @if($item->pieces->count() > 0)<span class="itd-tab-count">{{ $item->pieces->count() }}</span>@endif
        </div>
        <div class="itd-tab" onclick="showTab('events',this)">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="3" width="12" height="11" rx="2"/><line x1="5" y1="1" x2="5" y2="5"/><line x1="11" y1="1" x2="11" y2="5"/><line x1="2" y1="7" x2="14" y2="7"/></svg>
          Event History
          @if($totalEvents > 0)<span class="itd-tab-count">{{ $totalEvents }}</span>@endif
        </div>
        <div class="itd-tab" onclick="showTab('repairs',this)">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="3"/><path d="M8 1v2M8 13v2M1 8h2M13 8h2"/></svg>
          Repairs
          @if($item->repairs->count() > 0)<span class="itd-tab-count">{{ $item->repairs->count() }}</span>@endif
        </div>
        <div class="itd-tab" onclick="showTab('specs',this)">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M2 4h12M2 8h12M2 12h12"/></svg>
          Specifications
        </div>
        <div class="itd-tab" onclick="showTab('health',this)">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M2 8h2l2-4 2 8 2-4 2 2h2"/></svg>
          Health
        </div>
        <div class="itd-tab" onclick="showTab('activity',this)">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><polyline points="8 4 8 8 11 10"/></svg>
          Activity Log
          @if($activityLogs->count() > 0)<span class="itd-tab-count">{{ $activityLogs->count() }}</span>@endif
        </div>
        <div id="tab-btn-edit" class="itd-tab" onclick="showTab('edit',this)">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M1 12l3-3 8-8 3 3-8 8-3 1z"/></svg>
          Edit Item
        </div>
      </div>

      {{-- MEDIA TAB --}}
      <div id="tab-media" class="itd-tab-body">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
          <div>
            <p style="font-size:13px;font-weight:600;color:#0f0f0f;margin:0">Item Media</p>
            <p style="font-size:11px;color:#a09890;margin:2px 0 0">
              {{ $item->images->count() }} photo{{ $item->images->count() !== 1 ? 's' : '' }} &middot; Click the star to set as primary card image
            </p>
          </div>
          <label class="itd-btn-red" style="cursor:pointer" id="upload-label">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><line x1="8" y1="5" x2="8" y2="11"/><line x1="5" y1="8" x2="11" y2="8"/></svg>
            Upload Photo
            <input type="file" id="photo-upload-input" style="display:none"
                   accept="image/jpeg,image/png,image/webp" multiple
                   data-upload-url="{{ route('inventory.image.upload', $item) }}"
                   data-csrf="{{ csrf_token() }}"
                   onchange="handleItemImageUpload(this)">
          </label>
        </div>

        <div class="itd-gallery" id="image-gallery">
          @forelse($item->images as $img)
          <div class="itd-gimg-wrap" id="img-wrap-{{ $img->id }}">
            <div class="itd-gimg">
              <img src="{{ $img->url }}" alt="{{ $item->name }}">
              <div class="itd-gimg-ov">
                <a href="{{ $img->url }}" target="_blank">
                  <svg width="20" height="20" viewBox="0 0 16 16" fill="none" stroke="#fff" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="3"/><path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z"/></svg>
                </a>
              </div>
              <button class="itd-star-overlay {{ $img->is_primary ? 'itd-star-on' : '' }}"
                      title="{{ $img->is_primary ? 'Primary photo' : 'Set as primary' }}"
                      onclick="setPrimary({{ $img->id }}, this, '{{ route('inventory.image.primary', [$item, $img]) }}')">
                <svg width="13" height="13" viewBox="0 0 16 16" fill="{{ $img->is_primary ? '#fff' : 'none' }}" stroke="#fff" stroke-width="1.5" stroke-linecap="round"><polygon points="8 2 10 6 14 6.5 11 9.5 11.5 14 8 12 4.5 14 5 9.5 2 6.5 6 6"/></svg>
              </button>
              <button class="itd-del-overlay"
                      title="Delete photo"
                      onclick="deleteImage({{ $img->id }}, this, '{{ route('inventory.image.delete', [$item, $img]) }}')">
                <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="#fff" stroke-width="1.5" stroke-linecap="round"><path d="M3 4h10M5 4V3h6v1M6 7v5M10 7v5"/><rect x="2" y="4" width="12" height="10" rx="1.5"/></svg>
              </button>
              @if($img->is_primary)
              <div class="itd-primary-badge">
                <svg width="9" height="9" viewBox="0 0 16 16" fill="#fff" stroke="#fff" stroke-width="1" stroke-linecap="round"><polygon points="8 2 10 6 14 6.5 11 9.5 11.5 14 8 12 4.5 14 5 9.5 2 6.5 6 6"/></svg>
                Primary
              </div>
              @endif
            </div>
          </div>
          @empty
          <div class="itd-media-empty" style="grid-column:1/-1" id="media-empty-state">
            <svg width="36" height="36" viewBox="0 0 16 16" fill="none" stroke="#d0c8c0" stroke-width="0.8" stroke-linecap="round"><rect x="1" y="3" width="14" height="10" rx="2"/><circle cx="6" cy="8" r="1.5"/><path d="M1 11l3.5-3.5 2.5 2.5 2-2 4 4"/></svg>
            <p>No photos yet</p>
            <span>Upload the first photo of this item</span>
          </div>
          @endforelse

          <label class="itd-add-photo" style="cursor:pointer">
            <svg width="20" height="20" viewBox="0 0 16 16" fill="none" stroke="#c0b8b0" stroke-width="1.5" stroke-linecap="round"><rect x="1" y="3" width="14" height="10" rx="2"/><circle cx="6" cy="8" r="1.5"/><path d="M1 11l3.5-3.5 2.5 2.5 2-2 4 4"/></svg>
            <span>Add photo</span>
            <input type="file" style="display:none" accept="image/jpeg,image/png,image/webp" multiple
                   data-upload-url="{{ route('inventory.image.upload', $item) }}"
                   data-csrf="{{ csrf_token() }}"
                   onchange="handleItemImageUpload(this)">
          </label>
        </div>

        <div id="upload-progress" style="display:none;margin-top:12px">
          <div style="font-size:11px;color:#a09890;margin-bottom:6px" id="upload-status">Uploading...</div>
          <div style="height:3px;background:#f0ece8;border-radius:2px;overflow:hidden">
            <div id="upload-bar" style="height:100%;background:#CC0000;width:0%;transition:width 0.3s;border-radius:2px"></div>
          </div>
        </div>
      </div>

      {{-- PIECES TAB --}}
      <div id="tab-pieces" class="itd-tab-body" style="display:none">
        @php
          $totalPieces = $item->pieces->count();
          $availablePieces = $item->pieces->where('status', 'Available')->count();
          $assignedPieces = $item->pieces->where('status', 'Assigned')->count();
          $inServicePieces = $item->pieces->whereIn('status', ['Cleaning', 'Under Repair', 'Damaged'])->count();
        @endphp

        {{-- Tab Header with Print Button --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
          <div>
            <p style="font-size:13px;font-weight:600;color:#0f0f0f;margin:0">Item Pieces</p>
            <p style="font-size:11px;color:#a09890;margin:2px 0 0">Individual serialized pieces for this item</p>
          </div>
          <div style="display:flex;gap:8px">
            <button type="button" onclick="previewAllQR({{ $item->id }}, '{{ addslashes($item->name) }}')" class="itd-btn-outline">
              <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                <circle cx="8" cy="8" r="2"/><path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z"/>
              </svg>
              Preview QR Codes
            </button>
            <a href="{{ route('labels.byItem', $item) }}" target="_blank" class="itd-btn-outline" style="text-decoration:none">
              <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                <path d="M4 5V2h8v3M4 11H2V6h12v5h-2M5 9h6v5H5z"/>
              </svg>
              Print All Labels
            </a>
          </div>
        </div>

        {{-- Summary Stats --}}
        <div class="itd-pieces-stats">
          <div class="itd-pieces-stat">
            <div class="itd-pieces-stat-label">Total Pieces</div>
            <div class="itd-pieces-stat-value">{{ $totalPieces }}</div>
          </div>
          <div class="itd-pieces-stat">
            <div class="itd-pieces-stat-label">Available</div>
            <div class="itd-pieces-stat-value" style="color:#3B6D11">{{ $availablePieces }}</div>
          </div>
          <div class="itd-pieces-stat">
            <div class="itd-pieces-stat-label">Assigned</div>
            <div class="itd-pieces-stat-value" style="color:#185FA5">{{ $assignedPieces }}</div>
          </div>
          <div class="itd-pieces-stat">
            <div class="itd-pieces-stat-label">In Service</div>
            <div class="itd-pieces-stat-value" style="color:#854F0B">{{ $inServicePieces }}</div>
          </div>
        </div>

        {{-- Filter Tabs --}}
        <div class="itd-pieces-filter-tabs">
          <div class="itd-pieces-filter-tab active" onclick="filterPieces('all',this)">All</div>
          <div class="itd-pieces-filter-tab" onclick="filterPieces('Available',this)">Available</div>
          <div class="itd-pieces-filter-tab" onclick="filterPieces('Assigned',this)">Assigned</div>
          <div class="itd-pieces-filter-tab" onclick="filterPieces('in-service',this)">In Service</div>
        </div>

        {{-- Search --}}
        <input type="text" id="pieces-search" placeholder="Search by unique code..." class="itd-pieces-search" onkeyup="searchPieces()">

        {{-- Pieces Table --}}
        <div class="itd-pieces-table-wrap">
          <table class="itd-pieces-table" id="pieces-table">
            <thead>
              <tr>
                <th>Unique Code</th>
                <th>Status</th>
                <th>Condition</th>
                <th>Current Event</th>
                <th>Last Updated</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($item->pieces()->orderBy('unique_code')->get() as $piece)
                <tr class="piece-row" data-status="{{ $piece->status }}" data-code="{{ strtolower($piece->unique_code) }}">
                  <td><strong>{{ $piece->unique_code }}</strong></td>
                  <td><x-ui.status-badge :status="$piece->status" /></td>
                  <td>
                    @if($piece->condition_score)
                      <div class="itd-pieces-stars">
                        @for($i = 1; $i <= 5; $i++)
                          <svg width="12" height="12" viewBox="0 0 16 16" fill="{{ $i <= $piece->condition_score ? '#FFC107' : '#e0e0e0' }}" stroke="none">
                            <path d="M8 1l2 5h5l-4 3 2 5-5-3-5 3 2-5-4-3h5z"/>
                          </svg>
                        @endfor
                      </div>
                    @else
                      <span style="color:#b0a8a0">—</span>
                    @endif
                  </td>
                  <td>
                    @if($piece->current_event_id)
                      <a href="{{ route('events.show', $piece->current_event_id) }}" style="color:#CC0000;font-size:11px;font-weight:600">
                        {{ $piece->currentEvent->name ?? 'Event #'.$piece->current_event_id }}
                      </a>
                    @else
                      <span style="color:#b0a8a0">—</span>
                    @endif
                  </td>
                  <td style="font-size:11px;color:#a09890">{{ $piece->updated_at->diffForHumans() }}</td>
                  <td>
                    <div style="display:flex;gap:4px">
                      <button type="button" onclick="previewSingleQR({{ $piece->id }}, '{{ addslashes($piece->unique_code) }}', '{{ addslashes($item->name) }}')" class="inv-pc-action-btn" title="Preview QR">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                          <circle cx="8" cy="8" r="2"/><path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z"/>
                        </svg>
                      </button>
                      <a href="{{ route('labels.single', $piece) }}" target="_blank" class="inv-pc-action-btn" title="Print Label" style="text-decoration:none">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                          <path d="M4 5V2h8v3M4 11H2V6h12v5h-2M5 9h6v5H5z"/>
                        </svg>
                      </a>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" style="text-align:center;padding:40px;color:#a09890">No pieces found for this item.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- EVENTS TAB --}}
      <div id="tab-events" class="itd-tab-body" style="display:none">
        <div style="margin-bottom:14px">
          <p style="font-size:13px;font-weight:600;color:#0f0f0f;margin:0">Event History</p>
          <p style="font-size:11px;color:#a09890;margin:2px 0 0">All events this item has been dispatched to</p>
        </div>
        @forelse($item->events->sortByDesc(fn($e)=>$e->pivot->dispatched_at) as $event)
        @php $pivot = $event->pivot; @endphp
        <div class="itd-ev-item">
          <div class="itd-ev-dot-col">
            <div class="itd-ev-dot" style="background:{{ in_array($event->status,['Active','Scheduled']) ? '#CC0000' : '#d0c8c0' }}"></div>
            <div class="itd-ev-line"></div>
          </div>
          <div class="itd-ev-content">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:5px">
              <a href="{{ route('events.show', $event->id) }}" class="itd-ev-name">{{ $event->name }}</a>
              @php
                $evStatusClass = match($event->status) {
                  'Completed' => 'ev-s-completed',
                  'Active'    => 'ev-s-active',
                  'Scheduled' => 'ev-s-scheduled',
                  'Cancelled' => 'ev-s-cancelled',
                  'Set Down'  => 'ev-s-setdown',
                  default     => 'ev-s-draft',
                };
              @endphp
              <span class="ev-status-pill {{ $evStatusClass }}" style="font-size:9px">{{ $event->status }}</span>
            </div>
            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:5px">
              <span class="itd-ev-tag">
                <svg width="9" height="9" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M8 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path d="M8 14s5-3.5 5-7A5 5 0 0 0 3 7c0 3.5 5 7 5 7z"/></svg>
                {{ $event->venue }}
              </span>
              <span class="itd-ev-tag">Event: {{ $event->event_date->format('d M Y') }}</span>
              @if($pivot->dispatched_at)
                <span class="itd-ev-tag">Dispatched: {{ \Carbon\Carbon::parse($pivot->dispatched_at)->format('d M Y') }}</span>
              @endif
              @if($pivot->returned_at)
                <span class="itd-ev-tag" style="background:#eaf3de;color:#3B6D11">Returned: {{ \Carbon\Carbon::parse($pivot->returned_at)->format('d M Y') }}</span>
              @endif
            </div>
            <div style="display:flex;gap:8px">
              @if($pivot->condition_on_dispatch)
                <span class="itd-cond-badge {{ $conditionClasses[$pivot->condition_on_dispatch] ?? '' }}">
                  Dispatch: {{ $conditionLabels[$pivot->condition_on_dispatch] ?? '' }}
                </span>
              @endif
              @if($pivot->condition_on_return)
                <span class="itd-cond-badge {{ $conditionClasses[$pivot->condition_on_return] ?? '' }}">
                  Return: {{ $conditionLabels[$pivot->condition_on_return] ?? '' }}
                </span>
              @endif
            </div>
          </div>
        </div>
        @empty
        <div class="itd-tab-empty">
          <svg width="28" height="28" viewBox="0 0 16 16" fill="none" stroke="#d0c8c0" stroke-width="0.8" stroke-linecap="round"><rect x="2" y="3" width="12" height="11" rx="2"/><line x1="5" y1="1" x2="5" y2="5"/><line x1="11" y1="1" x2="11" y2="5"/></svg>
          <p>No events yet</p>
          <span>This item has not been dispatched to any event</span>
        </div>
        @endforelse
      </div>

      {{-- REPAIRS TAB --}}
      <div id="tab-repairs" class="itd-tab-body" style="display:none">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
          <div>
            <p style="font-size:13px;font-weight:600;color:#0f0f0f;margin:0">Repair History</p>
            <p style="font-size:11px;color:#a09890;margin:2px 0 0">All repairs recorded for this item</p>
          </div>
          <a href="{{ route('repairs.index') }}" class="itd-btn-outline" style="font-size:11px">View all repairs</a>
        </div>
        @forelse($item->repairs->sortByDesc('created_at') as $repair)
        <div class="itd-repair-item">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:8px">
            <div>
              <p style="font-size:13px;font-weight:600;color:#0f0f0f;margin:0">{{ $repair->repair_type ?? 'General Repair' }}</p>
              <p style="font-size:11px;color:#a09890;margin:2px 0 0">{{ $repair->technician_name ?? 'Technician not specified' }}</p>
            </div>
            <span class="itd-repair-status {{ match($repair->status) { 'Completed','Repaired' => 'itd-rs-done', 'In Progress' => 'itd-rs-prog', default => 'itd-rs-pend' } }}">
              {{ $repair->status }}
            </span>
          </div>
          @if($repair->description)
            <p style="font-size:12px;color:#5c5550;margin:0 0 8px;line-height:1.5">{{ $repair->description }}</p>
          @endif
          <div style="display:flex;gap:10px;flex-wrap:wrap">
            @if($repair->estimated_cost)
              <span class="itd-cost-tag">Est. KES {{ number_format($repair->estimated_cost, 0) }}</span>
            @endif
            @if($repair->actual_cost)
              <span class="itd-cost-tag itd-cost-actual">Actual KES {{ number_format($repair->actual_cost, 0) }}</span>
            @endif
            @if($repair->started_at)
              <span class="itd-date-tag">Started {{ \Carbon\Carbon::parse($repair->started_at)->format('d M Y') }}</span>
            @endif
            @if($repair->completed_at)
              <span class="itd-date-tag" style="color:#3B6D11">Completed {{ \Carbon\Carbon::parse($repair->completed_at)->format('d M Y') }}</span>
            @endif
          </div>
        </div>
        @empty
        <div class="itd-tab-empty">
          <svg width="28" height="28" viewBox="0 0 16 16" fill="none" stroke="#d0c8c0" stroke-width="0.8" stroke-linecap="round"><circle cx="8" cy="8" r="3"/><path d="M8 1v2M8 13v2M1 8h2M13 8h2"/></svg>
          <p>No repairs recorded</p>
          <span>This item has a clean repair history</span>
        </div>
        @endforelse
      </div>

      {{-- SPECS TAB --}}
      <div id="tab-specs" class="itd-tab-body" style="display:none">
        <div style="margin-bottom:20px">
          <p style="font-size:13px;font-weight:600;color:#0f0f0f;margin:0">Technical Specifications</p>
          <p style="font-size:11px;color:#a09890;margin:2px 0 0">Detailed build and logistics information</p>
        </div>
        
        <div class="itd-specs-grid">
          <div class="itd-spec-item">
            <span class="itd-spec-label">Brand</span>
            <span class="itd-spec-val">{{ $item->brand ?? '—' }}</span>
          </div>
          <div class="itd-spec-item">
            <span class="itd-spec-label">Model Number</span>
            <span class="itd-spec-val">{{ $item->model_number ?? '—' }}</span>
          </div>
          <div class="itd-spec-item">
            <span class="itd-spec-label">Serial Number</span>
            <span class="itd-spec-val">{{ $item->serial_number ?? '—' }}</span>
          </div>
          <div class="itd-spec-item">
            <span class="itd-spec-label">Dimensions</span>
            <span class="itd-spec-val">{{ $item->dimensions ?? '—' }}</span>
          </div>
          <div class="itd-spec-item">
            <span class="itd-spec-label">Weight</span>
            <span class="itd-spec-val">{{ $item->weight ?? '—' }}</span>
          </div>
          <div class="itd-spec-item">
            <span class="itd-spec-label">Purchase Date</span>
            <span class="itd-spec-val">{{ $item->purchase_date ? \Carbon\Carbon::parse($item->purchase_date)->format('d M Y') : '—' }}</span>
          </div>
          <div class="itd-spec-item">
            <span class="itd-spec-label">Purchase Cost</span>
            <span class="itd-spec-val">{{ $item->purchase_cost ? 'KES ' . number_format($item->purchase_cost, 2) : '—' }}</span>
          </div>
        </div>

        @if($item->specifications)
        <div style="margin-top:24px">
          <p style="font-size:12px;font-weight:700;color:#5c5550;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.05em">Detailed Features</p>
          <div style="font-size:13px;color:#3a3530;line-height:1.6;background:#fcfcfc;border:1px solid #f0ece8;padding:16px;border-radius:8px">
            {!! nl2br(e($item->specifications)) !!}
          </div>
        </div>
        @endif
      </div>

      {{-- HEALTH TAB --}}
      <div id="tab-health" class="itd-tab-body" style="display:none">
        <div style="margin-bottom:14px">
          <p style="font-size:13px;font-weight:600;color:#0f0f0f;margin:0">Item Health Overview</p>
          <p style="font-size:11px;color:#a09890;margin:2px 0 0">Condition tracking and lifecycle analysis</p>
        </div>
        <div class="itd-health-grid">
          <div class="itd-health-card">
            <div class="itd-hc-label">Current Status</div>
            <div style="margin-top:4px"><x-ui.status-badge :status="$item->status" /></div>
          </div>
          <div class="itd-health-card">
            <div class="itd-hc-label">Total Dispatches</div>
            <div class="itd-hc-val">{{ $totalEvents }}</div>
            <div class="itd-hc-sub">times deployed</div>
          </div>
          <div class="itd-health-card">
            <div class="itd-hc-label">Total Repairs</div>
            <div class="itd-hc-val" style="{{ $item->repairs->count() > 2 ? 'color:#CC0000' : 'color:#0f0f0f' }}">
              {{ $item->repairs->count() }}
            </div>
            <div class="itd-hc-sub">repair records</div>
          </div>
          <div class="itd-health-card">
            <div class="itd-hc-label">Repair Cost Total</div>
            <div class="itd-hc-val" style="font-size:14px">KES {{ number_format($totalRepairCost, 0) }}</div>
            <div class="itd-hc-sub">cumulative spend</div>
          </div>
        </div>
        @php
          $completedRepairs = $item->repairs->whereIn('status',['Completed','Repaired'])->count();
          $totalRep = max($item->repairs->count(), 1);
          $repairSuccessRate = round(($completedRepairs / $totalRep) * 100);
          $usageRate = $totalEvents > 0 ? min(100, round(($totalEvents / 20) * 100)) : 0;
        @endphp
        <div style="display:flex;flex-direction:column;gap:10px;margin-top:4px">
          <div class="itd-health-bar-wrap">
            <div class="itd-health-bar-label">
              <span>Usage frequency</span>
              <span style="font-weight:600">{{ $totalEvents }} events</span>
            </div>
            <div class="itd-health-bar-track">
              <div class="itd-health-bar-fill" style="width:{{ $usageRate }}%;background:#CC0000"></div>
            </div>
          </div>
          <div class="itd-health-bar-wrap">
            <div class="itd-health-bar-label">
              <span>Repair completion rate</span>
              <span style="font-weight:600">{{ $repairSuccessRate }}%</span>
            </div>
            <div class="itd-health-bar-track">
              <div class="itd-health-bar-fill" style="width:{{ $repairSuccessRate }}%;background:#3B6D11"></div>
            </div>
          </div>
        </div>
      </div>

      {{-- ACTIVITY LOG TAB --}}
      <div id="tab-activity" class="itd-tab-body" style="display:none">
        <div style="margin-bottom:14px">
          <p style="font-size:13px;font-weight:600;color:#0f0f0f;margin:0">Activity Log</p>
          <p style="font-size:11px;color:#a09890;margin:2px 0 0">Full audit trail for this item</p>
        </div>
        @forelse($activityLogs as $log)
        @php
          $iconClass = match(true) {
            str_contains($log->action,'dispatch') => 'itd-ai-blue',
            str_contains($log->action,'return')   => 'itd-ai-green',
            str_contains($log->action,'damage') || str_contains($log->action,'repair') => 'itd-ai-red',
            str_contains($log->action,'assign') => 'itd-ai-blue',
            default => 'itd-ai-green',
          };
        @endphp
        <div class="itd-act-item">
          <div class="itd-act-icon {{ $iconClass }}">
            <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><line x1="8" y1="5" x2="8" y2="8.5"/><circle cx="8" cy="11" r="0.5" fill="currentColor"/></svg>
          </div>
          <div style="flex:1;min-width:0">
            <div class="itd-act-main">
              <strong>{{ ucfirst($log->action) }}</strong>
              @if($log->description) — {{ $log->description }}@endif
            </div>
            <div class="itd-act-time">
              {{ $log->created_at->format('d M Y H:i') }}
              @if($log->user_id) &middot; User #{{ $log->user_id }}@endif
            </div>
          </div>
        </div>
        @empty
        <div class="itd-tab-empty">
          <svg width="28" height="28" viewBox="0 0 16 16" fill="none" stroke="#d0c8c0" stroke-width="0.8" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/></svg>
          <p>No activity recorded</p>
          <span>Actions on this item will appear here</span>
        </div>
        @endforelse
      </div>

      {{-- EDIT TAB --}}
      <div id="tab-edit" class="itd-tab-body" style="display:none">

        <form method="POST" action="{{ route('inventory.update', $item) }}" enctype="multipart/form-data" id="edit-item-form">
          @csrf @method('PUT')

          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
            <div>
              <p style="font-size:13px;font-weight:600;color:#0f0f0f;margin:0">Edit Item Details</p>
              <p style="font-size:11px;color:#a09890;margin:2px 0 0">Update item information and primary image</p>
            </div>
            <button type="submit" class="itd-btn-red" id="edit-save-btn">
              <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M2 10V13h3l8-8-3-3-8 8zM12 4l-1-1"/></svg>
              Save Changes
            </button>
          </div>

          @if($errors->any())
          <div class="wiz-error-box" style="margin-bottom:16px">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M8 6v3M8 11v1"/><path d="M3 13L8 3l5 10H3z"/></svg>
            <div>
              @foreach($errors->all() as $error)
                <p style="font-size:11px;color:#A32D2D;margin:0">{{ $error }}</p>
              @endforeach
            </div>
          </div>
          @endif

          {{-- PRIMARY IMAGE --}}
          <div class="itd-edit-section-title">Primary Image</div>
          <div class="itd-edit-image-row">
            <div class="itd-edit-current-img">
              @if($item->primaryImageUrl)
                <img src="{{ $item->primaryImageUrl }}" alt="{{ $item->name }}" id="edit-img-preview">
              @else
                <div class="itd-edit-img-placeholder" id="edit-img-preview">
                  <svg width="32" height="32" viewBox="0 0 16 16" fill="none" stroke="#d0c8c0" stroke-width="0.8" stroke-linecap="round"><rect x="2" y="2" width="12" height="12" rx="2"/><circle cx="8" cy="7" r="2"/><path d="M2 12c1-2 3-3 6-3s5 1 6 3"/></svg>
                </div>
              @endif
            </div>
            <div class="itd-edit-img-info">
              <p style="font-size:12px;font-weight:600;color:#0f0f0f;margin:0 0 4px">Replace primary image</p>
              <p style="font-size:11px;color:#a09890;margin:0 0 10px;line-height:1.5">Upload a new image to replace the current primary photo. JPEG, PNG or WEBP, max 4MB.</p>
              <label class="itd-btn-outline" style="cursor:pointer;display:inline-flex">
                <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="1" y="3" width="14" height="10" rx="2"/><circle cx="6" cy="8" r="1.5"/><path d="M1 11l3.5-3.5 2.5 2.5 2-2 4 4"/></svg>
                Choose Image
                <input type="file" name="image" style="display:none" accept="image/jpeg,image/png,image/webp"
                       onchange="previewEditImage(this)">
              </label>
              <p style="font-size:10px;color:#b0a8a0;margin:8px 0 0" id="edit-img-filename">No new image selected</p>
            </div>
          </div>

          {{-- BASIC INFO --}}
          <div class="itd-edit-section-title" style="margin-top:20px">Basic Information</div>
          <div class="wiz-form-grid">
            <div class="wiz-form-group">
              <label class="wiz-label">Item Name <span class="wiz-req">*</span></label>
              <input type="text" name="name" value="{{ old('name', $item->name) }}" class="wiz-input" required>
            </div>
            <div class="wiz-form-group">
              <label class="wiz-label">Category <span class="wiz-req">*</span></label>
              <select name="category" class="wiz-input" style="cursor:pointer">
                @foreach($categories as $cat)
                  <option value="{{ $cat }}" {{ old('category', $item->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
              </select>
            </div>
          </div>

          {{-- STATUS + LOCATION --}}
          <div class="itd-edit-section-title" style="margin-top:18px">Status & Location</div>
          <div class="wiz-form-grid">
            <div class="wiz-form-group">
              <label class="wiz-label">Status <span class="wiz-req">*</span></label>
              <select name="status" class="wiz-input" style="cursor:pointer">
                @foreach(['Available','Assigned','In Use','Under Inspection','Cleaning','Cleaned','Under Repair','Repaired','Damaged','Irreparable'] as $s)
                  <option value="{{ $s }}" {{ old('status', $item->status) === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
              </select>
            </div>
            <div class="wiz-form-group">
              <label class="wiz-label">Location</label>
              <select name="location" class="wiz-input" style="cursor:pointer">
                @foreach(['Warehouse','Site A','Site B'] as $loc)
                  <option value="{{ $loc }}" {{ old('location', $item->location) === $loc ? 'selected' : '' }}>{{ $loc }}</option>
                @endforeach
              </select>
            </div>
            <div class="wiz-form-group">
              <label class="wiz-label">Assigned To</label>
              <input type="text" name="assigned_to" value="{{ old('assigned_to', $item->assigned_to) }}" class="wiz-input" placeholder="Person or event name">
            </div>
            <div class="wiz-form-group">
              <label class="wiz-label">Assigned By</label>
              <input type="text" name="assigned_by" value="{{ old('assigned_by', $item->assigned_by) }}" class="wiz-input" placeholder="Your name">
            </div>
          </div>

          {{-- NOTES --}}
          <div class="itd-edit-section-title" style="margin-top:18px">Notes</div>
          <div class="wiz-form-group">
            <label class="wiz-label">Notes <span class="wiz-optional">(optional)</span></label>
            <textarea name="notes" class="wiz-textarea" rows="4" placeholder="Any additional notes about this item...">{{ old('notes', $item->notes) }}</textarea>
          </div>

          <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:20px;padding-top:16px;border-top:1px solid #f5f1ed">
            <button type="button" class="itd-btn-outline" onclick="showTab('media', document.querySelector('.itd-tab'))">Cancel</button>
            <button type="submit" class="itd-btn-red">
              <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M2 10V13h3l8-8-3-3-8 8zM12 4l-1-1"/></svg>
              Save Changes
            </button>
          </div>

        </form>
      </div>

    </div>
  </div>

  {{-- SIDE PANEL --}}
  <div class="itd-side-col">

    {{-- ITEM DETAILS --}}
    <div class="itd-side-card">
      <div class="itd-side-title">Item Details</div>
      <div class="itd-side-row"><span class="itd-side-label">ID</span><span class="itd-side-val">#ITM-{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</span></div>
      <div class="itd-side-row"><span class="itd-side-label">Category</span><span class="itd-side-val">{{ $item->category }}</span></div>
      @if($item->brand)
        <div class="itd-side-row"><span class="itd-side-label">Brand</span><span class="itd-side-val">{{ $item->brand }}</span></div>
      @endif
      @if($item->model_number)
        <div class="itd-side-row"><span class="itd-side-label">Model</span><span class="itd-side-val">{{ $item->model_number }}</span></div>
      @endif
      <div class="itd-side-row"><span class="itd-side-label">Status</span><span class="itd-side-val">{{ $item->status }}</span></div>
      <div class="itd-side-row"><span class="itd-side-label">Location</span><span class="itd-side-val">{{ $item->location ?? '—' }}</span></div>
      @if($item->assigned_to)
      <div class="itd-side-row"><span class="itd-side-label">Assigned to</span><span class="itd-side-val">{{ $item->assigned_to }}</span></div>
      @endif
      @if($item->notes)
      <div class="itd-side-row" style="flex-direction:column;gap:4px;align-items:flex-start">
        <span class="itd-side-label">Notes</span>
        <span style="font-size:11px;color:#5c5550;line-height:1.5">{{ $item->notes }}</span>
      </div>
      @endif
    </div>

    {{-- LIFECYCLE --}}
    <div class="itd-side-card">
      <div class="itd-side-title">Lifecycle Stage</div>
      @php
        $stages = ['Available','Assigned','In Use','Cleaning','Under Inspection','Under Repair','Repaired'];
        $currentIndex = array_search($item->status, $stages);
      @endphp
      <div class="itd-lifecycle">
        @foreach($stages as $i => $stage)
        <div class="itd-lc-step">
          <div class="itd-lc-dot" style="background:{{ $stage === $item->status ? '#CC0000' : ($currentIndex !== false && $i < $currentIndex ? '#3B6D11' : '#ece8e3') }}"></div>
          <span class="itd-lc-label {{ $stage === $item->status ? 'itd-lc-active' : ($currentIndex !== false && $i < $currentIndex ? 'itd-lc-done' : 'itd-lc-future') }}">
            {{ $stage }}
          </span>
        </div>
        @endforeach
      </div>
    </div>

    {{-- LAST ACTIVE REPAIR --}}
    @if($activeRepair)
    <div class="itd-side-card" style="border-color:#f5c0c0;background:#fff8f8">
      <div class="itd-side-title" style="color:#CC0000">Active Repair</div>
      <p style="font-size:12px;font-weight:600;color:#0f0f0f;margin:0 0 4px">{{ $activeRepair->repair_type ?? 'General Repair' }}</p>
      <p style="font-size:11px;color:#a09890;margin:0 0 8px">{{ $activeRepair->technician_name ?? 'No technician assigned' }}</p>
      @if($activeRepair->estimated_cost)
        <p style="font-size:13px;font-weight:700;color:#CC0000;margin:0">KES {{ number_format($activeRepair->estimated_cost, 0) }}</p>
        <p style="font-size:10px;color:#a09890;margin:2px 0 0">Estimated cost</p>
      @endif
    </div>
    @endif

    {{-- CURRENT EVENT --}}
    @if($item->currentEvent())
    @php $ce = $item->currentEvent(); @endphp
    <div class="itd-side-card" style="border-color:#b5d4f4;background:#f0f7ff">
      <div class="itd-side-title" style="color:#185FA5">Current Event</div>
      <p style="font-size:12px;font-weight:600;color:#0f0f0f;margin:0 0 3px">{{ $ce->name }}</p>
      <p style="font-size:11px;color:#185FA5;margin:0 0 8px">{{ $ce->client_name }}</p>
      <p style="font-size:11px;color:#5c5550;margin:0 0 8px">{{ $ce->venue }} &middot; {{ $ce->event_date->format('d M Y') }}</p>
      <a href="{{ route('events.show', $ce->id) }}" class="itd-btn-outline" style="font-size:11px;display:inline-flex">
        View event →
      </a>
    </div>
    @endif

  </div>

</div>

<script>
function showTab(name, el) {
  document.querySelectorAll('.itd-tab-body').forEach(function(t){ t.style.display='none'; });
  document.querySelectorAll('.itd-tab').forEach(function(t){ t.classList.remove('active'); });
  document.getElementById('tab-'+name).style.display='block';
  el.classList.add('active');
}

function previewEditImage(input) {
  if (!input.files || !input.files[0]) return;
  var file = input.files[0];
  var reader = new FileReader();
  reader.onload = function(e) {
    var preview = document.getElementById('edit-img-preview');
    if (!preview) return;
    if (preview.tagName === 'IMG') {
      preview.src = e.target.result;
    } else {
      var img = document.createElement('img');
      img.src = e.target.result;
      img.id = 'edit-img-preview';
      img.style.cssText = 'width:100%;height:100%;object-fit:cover';
      preview.replaceWith(img);
    }
  };
  reader.readAsDataURL(file);
  var filename = document.getElementById('edit-img-filename');
  if (filename) filename.textContent = file.name;
}

function handleItemImageUpload(input) {
  var files = Array.from(input.files);
  if (!files.length) return;
  var url    = input.getAttribute('data-upload-url');
  var csrf   = input.getAttribute('data-csrf');
  var gallery = document.getElementById('image-gallery');
  var progress = document.getElementById('upload-progress');
  var bar      = document.getElementById('upload-bar');
  var status   = document.getElementById('upload-status');
  var empty    = document.getElementById('media-empty-state');

  if (progress) { progress.style.display='block'; bar.style.width='0%'; }

  var completed = 0;
  files.forEach(function(file) {
    var formData = new FormData();
    formData.append('image', file);
    formData.append('_token', csrf);

    fetch(url, { method:'POST', body:formData })
      .then(function(r){ return r.json(); })
      .then(function(data) {
        if (!data.success) return;

        if (empty) empty.style.display = 'none';

        var addBtn = gallery.querySelector('.itd-add-photo');

        var wrap = document.createElement('div');
        wrap.className = 'itd-gimg-wrap';
        wrap.id = 'img-wrap-' + data.id;

        wrap.innerHTML =
          '<div class="itd-gimg">' +
            '<img src="' + data.url + '" alt="Item photo">' +
            '<div class="itd-gimg-ov"><svg width="18" height="18" viewBox="0 0 16 16" fill="none" stroke="#fff" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="3"/><path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z"/></svg></div>' +
          '</div>' +
          '<div class="itd-img-actions">' +
            '<button class="itd-img-star' + (data.is_primary ? ' itd-img-star-on' : '') + '" ' +
              'onclick="setPrimary(' + data.id + ', this, \'' + url.replace('/images', '/images/' + data.id + '/primary') + '\')">' +
              '<svg width="13" height="13" viewBox="0 0 16 16" fill="' + (data.is_primary ? '#CC0000' : 'none') + '" stroke="' + (data.is_primary ? '#CC0000' : '#b0a8a0') + '" stroke-width="1.5" stroke-linecap="round"><polygon points="8 2 10 6 14 6.5 11 9.5 11.5 14 8 12 4.5 14 5 9.5 2 6.5 6 6"/></svg>' +
              (data.is_primary ? 'Primary' : 'Set primary') +
            '</button>' +
            '<button class="itd-img-del" onclick="deleteImage(' + data.id + ', this, \'' + url + '/' + data.id + '\')">' +
              '<svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M3 4h10M5 4V3h6v1M6 7v5M10 7v5"/><rect x="2" y="4" width="12" height="10" rx="1.5"/></svg>' +
            '</button>' +
          '</div>';

        gallery.insertBefore(wrap, addBtn);

        completed++;
        var pct = Math.round((completed / files.length) * 100);
        if (bar) bar.style.width = pct + '%';
        if (completed === files.length) {
          if (status) status.textContent = completed + ' photo' + (completed > 1 ? 's' : '') + ' uploaded successfully';
          setTimeout(function(){ if (progress) progress.style.display='none'; }, 2000);

          // Show toast notification
          if (typeof window.gaShowToast === 'function') {
            window.gaShowToast({
              type: 'success',
              title: 'Upload Complete',
              message: completed + ' photo' + (completed > 1 ? 's' : '') + ' uploaded successfully',
              duration: 4000,
              sound: false
            });
          }
        }
      })
      .catch(function(err){ console.error('Upload failed', err); });
  });
}

function setPrimary(imageId, btn, url) {
  var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  fetch(url, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' }
  })
  .then(function(r){ return r.json(); })
  .then(function(data) {
    if (!data.success) return;
    document.querySelectorAll('.itd-star-overlay').forEach(function(b) {
      b.classList.remove('itd-star-on');
    });
    document.querySelectorAll('.itd-primary-badge').forEach(function(b) {
      b.remove();
    });
    btn.classList.add('itd-star-on');
    var badge = document.createElement('div');
    badge.className = 'itd-primary-badge';
    badge.innerHTML = '<svg width="9" height="9" viewBox="0 0 16 16" fill="#fff" stroke="#fff" stroke-width="1" stroke-linecap="round"><polygon points="8 2 10 6 14 6.5 11 9.5 11.5 14 8 12 4.5 14 5 9.5 2 6.5 6 6"/></svg> Primary';
    btn.closest('.itd-gimg').appendChild(badge);

    var heroImg = document.querySelector('.itd-hero-img img');
    var editPreview = document.getElementById('edit-img-preview');
    var imgEl = btn.closest('.itd-gimg').querySelector('img');
    if (imgEl) {
      if (heroImg) heroImg.src = imgEl.src;
      if (editPreview && editPreview.tagName === 'IMG') editPreview.src = imgEl.src;
    }

    // Show toast notification
    if (typeof window.gaShowToast === 'function') {
      window.gaShowToast({
        type: 'success',
        title: 'Primary Image Set',
        message: 'This image is now the primary photo for this item',
        duration: 3500,
        sound: false
      });
    }
  });
}

function deleteImage(imageId, btn, url) {
  if (!confirm('Delete this photo?')) return;
  var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  fetch(url, {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' }
  })
  .then(function(r){ return r.json(); })
  .then(function(data) {
    if (!data.success) return;
    var wrap = document.getElementById('img-wrap-' + imageId);
    if (wrap) wrap.remove();

    // Show toast notification
    if (typeof window.gaShowToast === 'function') {
      window.gaShowToast({
        type: 'success',
        title: 'Photo Deleted',
        message: 'Photo has been removed successfully',
        duration: 3500,
        sound: false
      });
    }
  })
  .catch(function(err) {
    console.error('Delete failed', err);
    if (typeof window.gaShowToast === 'function') {
      window.gaShowToast({
        type: 'error',
        title: 'Delete Failed',
        message: 'Failed to delete photo. Please try again.',
        duration: 4000,
        sound: false
      });
    }
  });
}

// Pieces tab filtering
function filterPieces(status, el) {
  document.querySelectorAll('.itd-pieces-filter-tab').forEach(t => t.classList.remove('active'));
  el.classList.add('active');

  const rows = document.querySelectorAll('.piece-row');
  rows.forEach(row => {
    const rowStatus = row.getAttribute('data-status');
    if (status === 'all') {
      row.style.display = '';
    } else if (status === 'in-service') {
      row.style.display = ['Cleaning', 'Under Repair', 'Damaged'].includes(rowStatus) ? '' : 'none';
    } else {
      row.style.display = rowStatus === status ? '' : 'none';
    }
  });
}

function searchPieces() {
  const search = document.getElementById('pieces-search').value.toLowerCase();
  const rows = document.querySelectorAll('.piece-row');

  rows.forEach(row => {
    const code = row.getAttribute('data-code');
    if (code.includes(search)) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

// QR Preview Functions
function previewSingleQR(pieceId, code, itemName) {
  fetch(`/api/pieces/${pieceId}/qr`)
    .then(res => res.json())
    .then(data => {
      const modal = document.getElementById('qrPreviewModal');
      document.getElementById('qrModalTitle').textContent = 'QR Code Preview';
      document.getElementById('qrModalSubtitle').textContent = `${itemName} - ${code}`;
      document.getElementById('qrModalGrid').innerHTML = `
        <div class="qr-preview-single">
          ${data.qr_svg}
          <div class="qr-preview-code">${code}</div>
          <div class="qr-preview-item">${itemName}</div>
        </div>
      `;
      modal.style.display = 'flex';
    });
}

function previewAllQR(itemId, itemName) {
  fetch(`/api/items/${itemId}/pieces-qr`)
    .then(res => res.json())
    .then(data => {
      const modal = document.getElementById('qrPreviewModal');
      document.getElementById('qrModalTitle').textContent = 'QR Codes Preview';
      document.getElementById('qrModalSubtitle').textContent = `${itemName} - ${data.pieces.length} piece${data.pieces.length === 1 ? '' : 's'}`;

      const grid = data.pieces.map(piece => `
        <div class="qr-preview-item-card">
          ${piece.qr_svg}
          <div class="qr-preview-code">${piece.unique_code}</div>
        </div>
      `).join('');

      document.getElementById('qrModalGrid').innerHTML = grid;
      modal.style.display = 'flex';
    });
}

function previewItemQR(itemId, itemName) {
  fetch(`/api/items/${itemId}/qr`)
    .then(res => res.json())
    .then(data => {
      const modal = document.getElementById('qrPreviewModal');
      document.getElementById('qrModalTitle').textContent = 'Item QR Code';
      document.getElementById('qrModalSubtitle').textContent = itemName;

      document.getElementById('qrModalGrid').innerHTML = `
        <div class="qr-preview-single">
          ${data.qr_svg}
          <div class="qr-preview-code">#ITM-${String(itemId).padStart(3, '0')}</div>
          <div class="qr-preview-item">${itemName}</div>
        </div>
      `;
      modal.style.display = 'flex';
    });
}

function closeQRModal() {
  document.getElementById('qrPreviewModal').style.display = 'none';
}

function confirmDelete() {
  document.getElementById('deleteConfirmModal').style.display = 'flex';
}

function closeDeleteModal() {
  document.getElementById('deleteConfirmModal').style.display = 'none';
}
</script>

{{-- QR PREVIEW MODAL --}}
<div id="qrPreviewModal" class="itd-modal-backdrop" style="display:none;">
  <div class="itd-modal-card itd-modal-card-large">
    {{-- HEADER --}}
    <div class="itd-modal-header">
      <div>
        <h3 id="qrModalTitle"></h3>
        <p id="qrModalSubtitle" class="itd-modal-subtitle"></p>
      </div>
      <button type="button" onclick="closeQRModal()" class="itd-modal-close">&times;</button>
    </div>

    {{-- BODY --}}
    <div class="itd-modal-body" style="overflow-y:auto;flex:1;">
      <div id="qrModalGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px"></div>
    </div>

    {{-- FOOTER --}}
    <div class="itd-modal-footer" style="text-align:right;">
      <button type="button" onclick="closeQRModal()" class="itd-btn-outline">Close</button>
    </div>
  </div>
</div>

{{-- DELETE CONFIRMATION MODAL --}}
<div id="deleteConfirmModal" class="itd-modal-backdrop" style="display:none;">
  <div class="itd-modal-card">
    {{-- HEADER --}}
    <div class="itd-modal-header">
      <div>
        <h3>Delete Item</h3>
        <p class="itd-modal-subtitle">This action cannot be undone.</p>
      </div>
      <button type="button" onclick="closeDeleteModal()" class="itd-modal-close">&times;</button>
    </div>

    {{-- BODY --}}
    <div class="itd-modal-body">
      <p>Are you sure you want to permanently delete <strong>{{ $item->name }}</strong>?</p>
      <p class="itd-modal-note">This will remove all associated data including images, event history, and activity logs.</p>
    </div>

    {{-- FOOTER --}}
    <div class="itd-modal-footer">
      <button type="button" onclick="closeDeleteModal()" class="itd-btn-outline">Cancel</button>
      <form method="POST" action="{{ route('inventory.destroy', $item) }}" style="display:inline">
        @csrf @method('DELETE')
        <button type="submit" class="itd-btn-red itd-btn-modal-danger">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M3 4h10M5 4V3h6v1M6 7v5M10 7v5"/><rect x="2" y="4" width="12" height="10" rx="1.5"/></svg>
          Delete Item
        </button>
      </form>
    </div>
  </div>
</div>

<style>
.qr-preview-single {
  text-align:center;
  padding:20px;
}
.qr-preview-single svg {
  width:300px;
  height:300px;
  margin:0 auto 16px;
  display:block;
}
.qr-preview-item-card {
  background:#fff;
  border:1px solid #ece8e3;
  border-radius:8px;
  padding:16px;
  text-align:center;
  transition:all 0.2s;
}
.qr-preview-item-card:hover {
  border-color:#CC0000;
  box-shadow:0 2px 8px rgba(204,0,0,0.1);
}
.qr-preview-item-card svg {
  width:160px;
  height:160px;
  margin:0 auto 12px;
  display:block;
}
.qr-preview-code {
  font-size:13px;
  font-weight:700;
  color:#0f0f0f;
  margin-bottom:4px;
  font-family:'Courier New',monospace;
}
.qr-preview-item {
  font-size:11px;
  color:#a09890;
}
</style>

@endsection
