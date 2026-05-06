@extends('layouts.app')
@section('title', $event->name)
@section('page-title', 'Events')

@section('content')

@php
  $statusColors = [
    'Draft'     => 'ev-s-draft',
    'Awaiting Payment' => 'ev-s-draft',
    'Scheduled' => 'ev-s-scheduled',
    'Active'    => 'ev-s-active',
    'Set Down'  => 'ev-s-setdown',
    'Completed' => 'ev-s-completed',
    'Cancelled' => 'ev-s-cancelled',
  ];
  $colorClass = $statusColors[$event->status] ?? 'ev-s-draft';
  $conditionLabels = [5=>'Excellent',4=>'Good',3=>'Fair',2=>'Average',1=>'Poor'];
  $conditionColors = [
    5=>'ev-cond-excellent',
    4=>'ev-cond-good',
    3=>'ev-cond-fair',
    2=>'ev-cond-average',
    1=>'ev-cond-poor',
  ];
@endphp

{{-- BREADCRUMB + ACTIONS --}}
<div class="evsh-top-bar">
  <div class="evsh-breadcrumb">
    <a href="{{ route('events.index') }}" class="wiz-back-link">
      <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M10 3L6 8l4 5"/></svg>
      Events
    </a>
    <span class="evsh-bc-sep">/</span>
    <span class="evsh-bc-current">{{ $event->name }}</span>
  </div>
  <div class="evsh-top-actions">
    {{-- ORIGINAL ACTIONS --}}
    @if($event->status === 'Draft')
      <a href="{{ route('events.checklist', $event) }}" class="evsh-btn-outline">
        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 8l2 2 5-5"/><rect x="2" y="2" width="12" height="12" rx="2"/></svg>
        Edit Checklist
      </a>
    @endif

    {{-- DISPATCH BUTTON SMART VISIBILITY --}}
    @if(in_array($event->status, ['Scheduled', 'Awaiting Payment']) && $event->hasActiveSession())
      <a href="{{ route('events.scan.monitor', [$event, $event->getActiveScanSession()]) }}"
         class="ev-btn-dispatch ev-btn-dispatch-resume">
        Resume Scan Session
      </a>
    @elseif(in_array($event->status, ['Scheduled', 'Awaiting Payment']))
      <button onclick="openDispatchModal()" class="ev-btn-dispatch"
              id="dispatch-btn"
              @if($event->status === 'Awaiting Payment' && $event->payment_status !== 'paid')
                disabled title="Cannot dispatch until payment is received"
                style="opacity:0.6;cursor:not-allowed;background:#a09890"
              @endif
              @if($event->loading_date > now()->toDateString())
                data-future-date="{{ $event->loading_date->format('d M Y') }}"
              @endif>
        Dispatch Items
      </button>
    @elseif($event->status === 'Active')
      <a href="{{ route('events.dispatch.manual', $event) }}"
         class="ev-btn-dispatch ev-btn-dispatch-view">
        View / Edit Dispatch
      </a>
      @php
        $undispatchedItems = $event->eventItems->whereNull('dispatched_at')->count();
      @endphp
      @if($undispatchedItems > 0)
        <button onclick="openAdditionalDispatchModal()" class="evsh-btn-outline" style="border-color:#854F0B;color:#854F0B;background:#faeeda">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
            <path d="M8 3v10M3 8h10"/>
          </svg>
          Dispatch Additional Items
          <span style="background:#854F0B;color:#fff;font-size:9px;font-weight:700;padding:2px 6px;border-radius:4px;margin-left:4px">{{ $undispatchedItems }}</span>
        </button>
      @endif
    @endif
    @php
      $pendingReturn = $event->eventItems->where('return_processed', false)->whereNotNull('dispatched_at')->count();
      $itemsInCleaning = $event->eventItems->filter(fn($ei) => $ei->item && $ei->item->status === 'Cleaning')->count();
      $allItemsReceived = $event->status === 'Completed' && $pendingReturn === 0 && $itemsInCleaning === 0;
    @endphp
    @if(in_array($event->status, ['Set Down', 'Completed']))
      @if($event->status === 'Set Down')
        @if($pendingReturn > 0)
          <a href="{{ route('events.site-to-site.wizard', $event) }}" class="evsh-btn-outline" style="border-color: #185FA5; color: #185FA5; background: #f0f7ff;" title="Link items directly to another site">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M2 8h5M9 8h5M7 3l-5 5 5 5M9 3l5 5-5 5"/></svg>
            Site-to-Site Link
          </a>

          {{-- RECEIVE BUTTON --}}
          @if($event->hasActiveReceiveSession())
            <a href="{{ route('events.receive.monitor', [$event, $event->getActiveReceiveSession()]) }}"
               class="ev-btn-receive ev-btn-receive-resume">
              Resume Receive Session
            </a>
          @else
            <button onclick="openReceiveModal()" class="ev-btn-receive">
              Receive Items
              <span class="evsh-receive-badge">{{ $pendingReturn }}</span>
            </button>
          @endif
        @endif
      @elseif($event->status === 'Completed')
        @if($itemsInCleaning > 0)
          <a href="{{ route('cleaning.index') }}" class="evsh-btn-outline" style="border-color: #0F6E56; color: #0F6E56; background: #E1F5EE;">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M13 10V4h-3M3 10V4h3M8 1v14"/></svg>
            View {{ $itemsInCleaning }} Items in Cleaning
          </a>
        @elseif($allItemsReceived)
          <div class="evsh-btn-outline" style="opacity: 0.7; cursor: default; border-color: #3B6D11; color: #3B6D11; background: #eaf3de;">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 8l2 2 5-5"/><circle cx="8" cy="8" r="6.5"/></svg>
            All Items Received
          </div>
        @endif
        <a href="{{ route('events.receiving-report', $event) }}"
           target="_blank"
           class="ev-btn-receive ev-btn-receive-report">
          View Receiving Report
        </a>
      @endif
    @endif
    <button onclick="openEditModal()" class="evsh-edit-btn">
      <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
        <path d="M1 12l3-3 8-8 3 3-8 8-3 1z"/>
      </svg>
      Edit Event
    </button>
    {{-- STATUS CHANGE --}}
    @php
      $nextStatus = match($event->status) {
        'Scheduled' => 'Active',
        'Active'    => 'Set Down',
        'Set Down'  => 'Completed',
        default     => null,
      };
      $allowedStatuses = match($event->status) {
        'Draft'     => ['Scheduled', 'Cancelled'],
        'Scheduled' => ['Active', 'Cancelled'],
        'Active'    => ['Set Down', 'Scheduled'],
        'Set Down'  => ['Completed', 'Active'],
        'Completed' => ['Set Down'],
        'Cancelled' => ['Draft'],
        default     => [],
      };
    @endphp
    @if($nextStatus)
      <form method="POST" action="{{ route('events.update', $event) }}" style="display:inline">
        @csrf @method('PUT')
        <input type="hidden" name="name"          value="{{ $event->name }}">
        <input type="hidden" name="client_name"   value="{{ $event->client_name }}">
        <input type="hidden" name="venue"         value="{{ $event->venue }}">
        <input type="hidden" name="loading_date"  value="{{ $event->loading_date->format('Y-m-d') }}">
        <input type="hidden" name="setup_date"    value="{{ $event->setup_date->format('Y-m-d') }}">
        <input type="hidden" name="event_date"    value="{{ $event->event_date->format('Y-m-d') }}">
        <input type="hidden" name="setdown_date"  value="{{ $event->setdown_date->format('Y-m-d') }}">
        <input type="hidden" name="status"        value="{{ $nextStatus }}">
        <button type="submit" class="evsh-btn-status"
                onclick="return confirm('Move event status to {{ $nextStatus }}?')">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
          Mark as {{ $nextStatus }}
        </button>
      </form>
    @endif
    {{-- STATUS CHANGE DROPDOWN (for reverting/changing) --}}
    @if(count($allowedStatuses) > 0)
    <div class="evsh-status-dropdown" style="position: relative; display: inline-block;">
      <button type="button" class="evsh-btn-outline" onclick="toggleStatusDropdown(event)" style="padding: 8px 10px;">
        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M2 5h12M2 8h12M2 11h12"/></svg>
        Change Status
      </button>
      <div id="status-dropdown-menu" class="evsh-status-menu" style="display: none;">
        <div class="evsh-status-menu-header">Change Event Status</div>
        @foreach($allowedStatuses as $statusOption)
        <form method="POST" action="{{ route('events.update', $event) }}" style="display:inline; width: 100%;" onsubmit="return confirmStatusChange('{{ $statusOption }}', '{{ $event->status }}')">
          @csrf @method('PUT')
          <input type="hidden" name="name"          value="{{ $event->name }}">
          <input type="hidden" name="client_name"   value="{{ $event->client_name }}">
          <input type="hidden" name="venue"         value="{{ $event->venue }}">
          <input type="hidden" name="loading_date"  value="{{ $event->loading_date->format('Y-m-d') }}">
          <input type="hidden" name="setup_date"    value="{{ $event->setup_date->format('Y-m-d') }}">
          <input type="hidden" name="event_date"    value="{{ $event->event_date->format('Y-m-d') }}">
          <input type="hidden" name="setdown_date"  value="{{ $event->setdown_date->format('Y-m-d') }}">
          <input type="hidden" name="status"        value="{{ $statusOption }}">
          <button type="submit" class="evsh-status-menu-item {{ $statusColors[$statusOption] ?? '' }}">
            <span class="evsh-status-badge {{ $statusColors[$statusOption] ?? '' }}">{{ $statusOption }}</span>
            @if($statusOption === 'Set Down' && $event->status === 'Completed')
              <span class="evsh-status-hint">Revert to enable site-to-site</span>
            @endif
          </button>
        </form>
        @endforeach
      </div>
    </div>
    @endif
  </div>
</div>

{{-- FLASH --}}
@if(session('success'))
  <div class="ev-flash ev-flash-success">
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 8l2 2 5-5"/><circle cx="8" cy="8" r="6.5"/></svg>
    {{ session('success') }}
  </div>
@endif
@if(session('error'))
  <div class="ev-flash ev-flash-error">
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M8 6v3M8 11v1"/><path d="M3 13L8 3l5 10H3z"/></svg>
    {{ session('error') }}
  </div>
@endif

{{-- PREMIUM HERO SECTION --}}
<div class="evsh-hero-premium">
  <div class="evsh-hero-header">
    <div class="evsh-hero-title-section">
      <div class="evsh-hero-label">Event Details</div>
      <h1 class="evsh-hero-title">{{ $event->name }}</h1>
      <div class="evsh-hero-badges">
        <span class="evsh-status-badge {{ $colorClass }}">{{ $event->status }}</span>
        @if($event->link_type === 'site-to-site')
          <span class="evsh-status-badge evsh-badge-link">
            <svg width="10" height="10" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
              <path d="M2 8h5M9 8h5M7 3l-5 5 5 5M9 3l5 5-5 5"/>
            </svg>
            Site-to-Site
          </span>
        @endif
        @if($event->plan_ref)
          <span class="evsh-status-badge evsh-badge-ref">{{ $event->plan_ref }}</span>
        @endif
      </div>
    </div>
  </div>

  <div class="evsh-hero-details">
    <div class="evsh-detail-item">
      <div class="evsh-detail-icon">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
          <circle cx="8" cy="6" r="2.5"/>
          <path d="M2 13c0-3 2.5-5 6-5s6 2 6 5"/>
        </svg>
      </div>
      <div class="evsh-detail-content">
        <div class="evsh-detail-label">Client</div>
        <div class="evsh-detail-value">{{ $event->client_name }}</div>
      </div>
    </div>

    <div class="evsh-detail-item">
      <div class="evsh-detail-icon">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
          <path d="M8 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
          <path d="M8 14s5-3.5 5-7A5 5 0 0 0 3 7c0 3.5 5 7 5 7z"/>
        </svg>
      </div>
      <div class="evsh-detail-content">
        <div class="evsh-detail-label">Venue</div>
        <div class="evsh-detail-value">{{ $event->venue }}</div>
      </div>
    </div>

    @if($event->location_name)
    <div class="evsh-detail-item">
      <div class="evsh-detail-icon">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
          <circle cx="8" cy="8" r="6.5"/>
          <line x1="8" y1="5" x2="8" y2="11"/>
          <line x1="5" y1="8" x2="11" y2="8"/>
        </svg>
      </div>
      <div class="evsh-detail-content">
        <div class="evsh-detail-label">Location</div>
        <div class="evsh-detail-value">{{ $event->location_name }}</div>
      </div>
    </div>
    @endif

    <div class="evsh-detail-item">
      <div class="evsh-detail-icon">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
          <circle cx="8" cy="8" r="6.5"/>
          <polyline points="8 4 8 8 11 10"/>
        </svg>
      </div>
      <div class="evsh-detail-content">
        <div class="evsh-detail-label">Created</div>
        <div class="evsh-detail-value">{{ $event->created_at->format('d M Y') }}@if($event->creator) • {{ $event->creator->name }}@endif</div>
      </div>
    </div>

    @if($event->linked_from_event_id && $event->linkedFromEvent)
    <div class="evsh-detail-item evsh-detail-linked">
      <div class="evsh-detail-icon">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
          <path d="M6 8h4M7 4l-4 4 4 4"/>
        </svg>
      </div>
      <div class="evsh-detail-content">
        <div class="evsh-detail-label">Linked From</div>
        <div class="evsh-detail-value">
          <a href="{{ route('events.show', $event->linkedFromEvent) }}" class="evsh-link">{{ $event->linkedFromEvent->name }}</a>
        </div>
      </div>
    </div>
    @endif
  </div>

  {{-- COMPACT KPI ROW --}}
  <div class="evsh-kpi-compact">
    <div class="evsh-kpi-card">
      <div class="evsh-kpi-header">
        <svg width="15" height="15" viewBox="0 0 16 16" fill="none" stroke="#185FA5" stroke-width="1.5" stroke-linecap="round">
          <rect x="2" y="3" width="12" height="11" rx="2"/>
          <line x1="5" y1="1" x2="5" y2="5"/>
          <line x1="11" y1="1" x2="11" y2="5"/>
        </svg>
        <span class="evsh-kpi-title">Loading</span>
      </div>
      <div class="evsh-kpi-value">{{ $event->loading_date->format('d M Y') }}</div>
      <div class="evsh-kpi-subtitle">{{ $event->loading_date->format('l') }}</div>
    </div>

    <div class="evsh-kpi-card">
      <div class="evsh-kpi-header">
        <svg width="15" height="15" viewBox="0 0 16 16" fill="none" stroke="#854F0B" stroke-width="1.5" stroke-linecap="round">
          <rect x="2" y="3" width="12" height="11" rx="2"/>
          <line x1="2" y1="7" x2="14" y2="7"/>
        </svg>
        <span class="evsh-kpi-title">Setup</span>
      </div>
      <div class="evsh-kpi-value">{{ $event->setup_date->format('d M Y') }}</div>
      <div class="evsh-kpi-subtitle">{{ $event->setup_date->format('l') }}</div>
    </div>

    <div class="evsh-kpi-card evsh-kpi-highlight">
      <div class="evsh-kpi-header">
        <svg width="15" height="15" viewBox="0 0 16 16" fill="none" stroke="#fff" stroke-width="1.5" stroke-linecap="round">
          <circle cx="8" cy="8" r="6.5"/>
          <polyline points="8 4 8 8 11 10"/>
        </svg>
        <span class="evsh-kpi-title">Event Day</span>
      </div>
      <div class="evsh-kpi-value">{{ $event->event_date->format('d M Y') }}</div>
      <div class="evsh-kpi-subtitle">{{ $event->event_date->format('l') }}</div>
    </div>

    <div class="evsh-kpi-card">
      <div class="evsh-kpi-header">
        <svg width="15" height="15" viewBox="0 0 16 16" fill="none" stroke="#534AB7" stroke-width="1.5" stroke-linecap="round">
          <path d="M2 13V5l6-3 6 3v8"/>
        </svg>
        <span class="evsh-kpi-title">Set-down</span>
      </div>
      <div class="evsh-kpi-value">{{ $event->setdown_date->format('d M Y') }}</div>
      <div class="evsh-kpi-subtitle">{{ $event->setdown_date->format('l') }}</div>
    </div>

    <div class="evsh-kpi-card evsh-kpi-accent">
      <div class="evsh-kpi-header">
        <svg width="15" height="15" viewBox="0 0 16 16" fill="none" stroke="#3B6D11" stroke-width="1.5" stroke-linecap="round">
          <rect x="2" y="2" width="12" height="12" rx="2"/>
          <line x1="5" y1="6" x2="11" y2="6"/>
          <line x1="5" y1="9" x2="9" y2="9"/>
        </svg>
        <span class="evsh-kpi-title">Items</span>
      </div>
      <div class="evsh-kpi-value">{{ $event->eventItems->count() }}</div>
      <div class="evsh-kpi-subtitle">Total Pieces</div>
    </div>

    @if($event->cost)
    <div class="evsh-kpi-card evsh-kpi-cost">
      <div class="evsh-kpi-header">
        <svg width="15" height="15" viewBox="0 0 16 16" fill="none" stroke="#0F6E56" stroke-width="1.5" stroke-linecap="round">
          <rect x="1" y="4" width="14" height="9" rx="2"/>
          <path d="M1 7h14"/>
        </svg>
        <span class="evsh-kpi-title">Budget</span>
      </div>
      <div class="evsh-kpi-value">{{ number_format($event->cost, 0) }}</div>
      <div class="evsh-kpi-subtitle">KES</div>
    </div>
    @endif
  </div>
</div>

{{-- TABBED CONTENT --}}
<div class="evsh-tabs-card">

  {{-- TAB NAVIGATION --}}
  <div class="evsh-tabs-nav">
    <div class="evsh-tab active" onclick="showTab('overview', this)">
      <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="2" width="12" height="12" rx="2"/><line x1="6" y1="6" x2="10" y2="6"/><line x1="6" y1="9" x2="10" y2="9"/></svg>
      Overview
    </div>
    <div class="evsh-tab" onclick="showTab('items', this)">
      <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="2" width="12" height="12" rx="2"/><line x1="5" y1="6" x2="11" y2="6"/><line x1="5" y1="9" x2="9" y2="9"/></svg>
      Items
      <span class="evsh-tab-count">{{ $event->eventItems->count() }}</span>
    </div>
    @if($event->staff()->count() > 0)
    <div class="evsh-tab" onclick="showTab('team', this)">
      <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="6" r="2.5"/><path d="M2 13c0-3 2.5-5 6-5s6 2 6 5"/></svg>
      Team
      <span class="evsh-tab-count">{{ $event->staff()->count() }}</span>
    </div>
    @endif
    <div class="evsh-tab" onclick="showTab('documents', this)">
      <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 2h8l3 3v9a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1z"/><path d="M9 2v4h4"/></svg>
      Documents
    </div>
    @php
      $dispatchedItems = $event->eventItems->whereNotNull('dispatched_at');
      $totalDispatched = $dispatchedItems->count();
    @endphp
    @if($totalDispatched > 0)
    <div class="evsh-tab" onclick="showTab('analytics', this)">
      <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><polyline points="1 12 4 9 8 11 15 4"/><line x1="10" y1="4" x2="15" y2="4"/><line x1="15" y1="4" x2="15" y2="9"/></svg>
      Analytics
    </div>
    @endif
    @php $allImages = $event->eventItems->flatMap(fn($ei) => $ei->images); @endphp
    @if($allImages->count() > 0)
    <div class="evsh-tab" onclick="showTab('gallery', this)">
      <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="1" y="3" width="14" height="10" rx="2"/><circle cx="6" cy="8" r="1.5"/><path d="M1 11l3.5-3.5 2.5 2.5 2-2 4 4"/></svg>
      Gallery
      <span class="evsh-tab-count">{{ $allImages->count() }}</span>
    </div>
    @endif
    @php
      $logs = \App\Models\ActivityLog::with(['item', 'user'])
        ->where('event_id', $event->id)
        ->orderByDesc('created_at')
        ->get();
    @endphp
    <div class="evsh-tab" onclick="showTab('activity', this)">
      <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><polyline points="8 4 8 8 11 10"/></svg>
      Activity
      @if($logs->count() > 0)<span class="evsh-tab-count">{{ $logs->count() }}</span>@endif
    </div>
    @if($event->notes)
    <div class="evsh-tab" onclick="showTab('notes', this)">
      <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="2" width="10" height="12" rx="1"/><line x1="6" y1="6" x2="10" y2="6"/><line x1="6" y1="9" x2="10" y2="9"/></svg>
      Notes
    </div>
    @endif
    <div class="evsh-tab" onclick="showTab('actions', this)">
      <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M8 1v14M1 8h14"/></svg>
      Actions
    </div>
  </div>

  {{-- TAB 1: OVERVIEW --}}
  <div id="tab-overview" class="evsh-tab-body">
    {{-- SITE-TO-SITE CHAIN VISUALIZATION --}}
    @php
      // Build the complete chain of linked events
      $eventChain = collect();

      // Find the root event (the one with no parent)
      $rootEvent = $event;
      while ($rootEvent->linked_from_event_id) {
        $rootEvent = $rootEvent->linkedFromEvent;
      }

      // Build chain from root
      if (!function_exists('buildChain')) {
        function buildChain($evt) {
          $chain = collect([$evt]);
          if ($evt->linkedEvents && $evt->linkedEvents->count() > 0) {
            foreach ($evt->linkedEvents as $linked) {
              $chain = $chain->concat(buildChain($linked));
            }
          }
          return $chain;
        }
      }

      $eventChain = buildChain($rootEvent);
    @endphp

    @if($eventChain->count() > 1)
    <div style="background:#f0f7ff;border:1px solid #dae8f7;border-radius:10px;padding:18px;margin-bottom:16px">
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#185FA5" stroke-width="1.5" stroke-linecap="round">
          <path d="M2 8h5M9 8h5M7 3l-5 5 5 5M9 3l5 5-5 5"/>
        </svg>
        <span style="font-size:12px;font-weight:700;color:#185FA5;letter-spacing:0.01em">Site-to-Site Event Chain</span>
        <span style="font-size:10px;color:#7c7470;margin-left:auto">{{ $eventChain->count() }} linked events</span>
      </div>

      <div style="display:flex;align-items:center;gap:12px;overflow-x:auto;padding:6px 0">
        @foreach($eventChain as $chainEvent)
          @php
            $isCurrentEvent = $chainEvent->id === $event->id;
            $chainColorClass = $statusColors[$chainEvent->status] ?? 'ev-s-draft';
          @endphp

          {{-- Event Card --}}
          <a href="{{ route('events.show', $chainEvent) }}"
             style="flex-shrink:0;width:200px;background:{{ $isCurrentEvent ? '#fff' : '#fafafa' }};border:2px solid {{ $isCurrentEvent ? '#185FA5' : '#e0e8f0' }};border-radius:8px;padding:12px;text-decoration:none;position:relative;transition:all 0.2s ease"
             onmouseover="this.style.borderColor='#185FA5';this.style.boxShadow='0 2px 8px rgba(24,95,165,0.12)'"
             onmouseout="this.style.borderColor='{{ $isCurrentEvent ? '#185FA5' : '#e0e8f0' }}';this.style.boxShadow='none'">

            @if($isCurrentEvent)
            <div style="position:absolute;top:-6px;right:8px;background:#185FA5;color:#fff;font-size:8px;font-weight:700;padding:2px 7px;border-radius:3px;text-transform:uppercase;letter-spacing:0.05em">
              Current
            </div>
            @endif

            <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px">
              <div style="width:24px;height:24px;background:#fff;border:1px solid #e0e8f0;border-radius:5px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="#7c7470" stroke-width="1.5" stroke-linecap="round">
                  <rect x="2" y="3" width="12" height="11" rx="2"/>
                  <line x1="5" y1="1" x2="5" y2="5"/>
                  <line x1="11" y1="1" x2="11" y2="5"/>
                </svg>
              </div>
              <span class="ev-status-pill {{ $chainColorClass }}" style="font-size:8px;padding:2px 6px">{{ $chainEvent->status }}</span>
            </div>

            <div style="font-size:11px;font-weight:700;color:#0f0f0f;margin-bottom:3px;line-height:1.3">
              {{ Str::limit($chainEvent->name, 30) }}
            </div>
            <div style="font-size:9px;color:#7c7470;margin-bottom:6px">
              {{ Str::limit($chainEvent->client_name, 25) }}
            </div>
            <div style="font-size:9px;color:#a09890;display:flex;align-items:center;gap:3px">
              <svg width="8" height="8" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                <rect x="2" y="3" width="12" height="11" rx="2"/>
              </svg>
              {{ $chainEvent->event_date->format('d M Y') }}
            </div>
          </a>

          {{-- Arrow --}}
          @if(!$loop->last)
          <div style="flex-shrink:0;color:#b0a8a0">
            <svg width="20" height="20" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
              <path d="M3 8h10M9 4l4 4-4 4"/>
            </svg>
          </div>
          @endif
        @endforeach
      </div>

      @if($eventChain->count() > 3)
      <div style="margin-top:10px;padding:8px;background:#fff;border-radius:5px;font-size:9px;color:#7c7470;text-align:center">
        💡 Items flow through this chain. Each event receives items from the previous one.
      </div>
      @endif
    </div>
    @endif

    <div style="font-size:13px;font-weight:600;color:#0f0f0f;margin-bottom:10px">Event Summary</div>
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px">
      <div style="background:#faf8f6;border:1px solid #f0ece8;border-radius:8px;padding:12px">
        <div style="font-size:9px;font-weight:700;color:#a09890;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:5px">Client</div>
        <div style="font-size:14px;font-weight:600;color:#0f0f0f">{{ $event->client_name }}</div>
      </div>
      <div style="background:#faf8f6;border:1px solid #f0ece8;border-radius:8px;padding:12px">
        <div style="font-size:9px;font-weight:700;color:#a09890;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:5px">Venue</div>
        <div style="font-size:14px;font-weight:600;color:#0f0f0f">{{ $event->venue }}</div>
      </div>
      <div style="background:#faf8f6;border:1px solid #f0ece8;border-radius:8px;padding:12px">
        <div style="font-size:9px;font-weight:700;color:#a09890;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:5px">Total Items</div>
        <div style="font-size:20px;font-weight:700;color:#CC0000">{{ $event->eventItems->count() }}</div>
      </div>
      <div style="background:#faf8f6;border:1px solid #f0ece8;border-radius:8px;padding:12px">
        <div style="font-size:9px;font-weight:700;color:#a09890;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:5px">Status</div>
        <span class="ev-status-pill {{ $colorClass }}">{{ $event->status }}</span>
      </div>
    </div>
  </div>

  {{-- TAB 2: ITEMS --}}
  <div id="tab-items" class="evsh-tab-body" style="display:none">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
      <div>
        <div style="font-size:13px;font-weight:600;color:#0f0f0f">Dispatched Items</div>
        <div style="font-size:11px;color:#a09890;margin-top:2px">{{ $event->eventItems->count() }} items assigned to this event</div>
      </div>
      @if(in_array($event->status, ['Draft','Scheduled']))
        <a href="{{ route('events.checklist', $event) }}" class="evsh-btn-sm">
          Edit checklist
        </a>
      @endif
    </div>

    @if($event->eventItems->count() > 0)
      @php
        // Group items by dispatch batch
        $itemsByBatch = $event->eventItems->groupBy('dispatch_batch')->sortKeys();
        $totalBatches = $itemsByBatch->count();
      @endphp

      @foreach($itemsByBatch as $batchNumber => $batchItems)
        @php
          $batchDate = $batchItems->whereNotNull('dispatched_at')->first()?->dispatched_at;
          $batchUser = $batchItems->whereNotNull('dispatched_by')->first();
        @endphp

        {{-- Batch Header --}}
        @if($totalBatches > 1 || $batchDate)
        <div class="dispatch-batch-header">
          <div style="display:flex;align-items:center;gap:10px">
            <div style="width:32px;height:32px;background:{{ $batchNumber == 1 ? '#e6f1fb' : '#faeeda' }};border-radius:8px;display:flex;align-items:center;justify-content:center">
              <span style="font-size:13px;font-weight:700;color:{{ $batchNumber == 1 ? '#185FA5' : '#854F0B' }}">{{ $batchNumber }}</span>
            </div>
            <div>
              <div style="font-size:12px;font-weight:700;color:#0f0f0f">
                {{ $batchNumber == 1 ? 'Initial Dispatch' : 'Additional Dispatch #' . ($batchNumber - 1) }}
              </div>
              @if($batchDate)
                <div style="font-size:10px;color:#7c7470">
                  {{ $batchDate->format('d M Y, h:i A') }}
                  @if($batchUser && $batchUser->dispatchedBy)
                    • by {{ $batchUser->dispatchedBy->name }}
                  @endif
                </div>
              @endif
            </div>
          </div>
          <div style="font-size:11px;color:#7c7470">{{ $batchItems->count() }} items</div>
        </div>
        @endif

      <table class="evsh-items-table" style="{{ $totalBatches > 1 ? 'margin-bottom:20px' : '' }}">
        <thead>
          <tr>
            <th>Item</th>
            <th>Category</th>
            <th>Dispatch Condition</th>
            <th>Return Condition</th>
            <th>Qty Dispatched</th>
            <th>Photos</th>
          </tr>
        </thead>
        <tbody>
          @foreach($batchItems as $eventItem)
          @php
            $dispatchedPieces = \App\Models\EventPieceDispatch::where('event_id', $event->id)
              ->whereHas('itemPiece', function($q) use ($eventItem) {
                $q->where('item_id', $eventItem->item_id);
              })
              ->with('itemPiece')
              ->get();
            $pieceCount = $dispatchedPieces->count();
          @endphp
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:8px">
                <div class="evsh-item-thumb">
                  @if($eventItem->item && $eventItem->item->image_path)
                    <img src="{{ asset('storage/' . $eventItem->item->image_path) }}" alt="">
                  @else
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#c0b8b0" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="2" width="12" height="12" rx="2"/><circle cx="8" cy="7" r="2"/><path d="M2 12c1-2 3-3 6-3s5 1 6 3"/></svg>
                  @endif
                </div>
                <div>
                  <a href="{{ route('inventory.show', $eventItem->item_id) }}"
                     class="evsh-item-name">
                    {{ $eventItem->item->name ?? 'Unknown item' }}
                  </a>
                  <span class="evsh-item-id">#ITM-{{ str_pad($eventItem->item_id, 3, '0', STR_PAD_LEFT) }}</span>
                  @if($pieceCount > 0)
                    <button onclick="togglePieces('pieces-{{ $eventItem->id }}')"
                            style="margin-left:8px;background:#f8f7f5;border:1px solid #ece8e3;border-radius:5px;padding:2px 8px;font-size:9px;font-weight:600;color:#5c5550;cursor:pointer;transition:all 0.2s"
                            onmouseover="this.style.borderColor='#CC0000';this.style.color='#CC0000'"
                            onmouseout="this.style.borderColor='#ece8e3';this.style.color='#5c5550'">
                      <svg width="8" height="8" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="vertical-align:middle;margin-right:3px">
                        <rect x="2" y="2" width="12" height="12" rx="2"/>
                      </svg>
                      {{ $pieceCount }} piece{{ $pieceCount > 1 ? 's' : '' }}
                    </button>
                  @endif
                </div>
              </div>
            </td>
            <td>
              <span class="ev-cat-badge">{{ $eventItem->item->category ?? '—' }}</span>
            </td>
            <td>
              @if($eventItem->condition_on_dispatch)
                <span class="evsh-cond-badge {{ $conditionColors[$eventItem->condition_on_dispatch] ?? '' }}">
                  {{ $eventItem->condition_on_dispatch }} — {{ $conditionLabels[$eventItem->condition_on_dispatch] ?? '' }}
                </span>
              @else
                <span class="evsh-not-set">Not recorded</span>
              @endif
            </td>
            <td>
              @if($eventItem->condition_on_return)
                <span class="evsh-cond-badge {{ $conditionColors[$eventItem->condition_on_return] ?? '' }}">
                  {{ $eventItem->condition_on_return }} — {{ $conditionLabels[$eventItem->condition_on_return] ?? '' }}
                </span>
              @else
                <span class="evsh-not-set">Pending return</span>
              @endif
            </td>
            <td>
              <span class="evsh-date-cell">
                {{ $eventItem->quantity_dispatched ?? 0 }} pcs
              </span>
            </td>
            <td>
              <span class="evsh-photo-count">
                {{ $eventItem->images->count() }}
                <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="1" y="3" width="14" height="10" rx="2"/><circle cx="6" cy="8" r="1.5"/><path d="M1 11l3.5-3.5 2.5 2.5 2-2 4 4"/></svg>
              </span>
            </td>
          </tr>
          @if($pieceCount > 0)
          <tr id="pieces-{{ $eventItem->id }}" style="display:none">
            <td colspan="6" style="padding:0;background:#fafafa">
              <div style="padding:14px 18px;border-top:1px solid #f0ece8">
                <div style="font-size:10px;font-weight:700;color:#5c5550;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:10px;display:flex;align-items:center;gap:6px">
                  <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                    <rect x="2" y="2" width="12" height="12" rx="2"/>
                  </svg>
                  Dispatched Pieces for this Event
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:8px">
                  @foreach($dispatchedPieces as $dispatch)
                  <div style="background:#fff;border:1px solid #ece8e3;border-radius:6px;padding:10px;display:flex;align-items:center;justify-content:space-between">
                    <div style="flex:1">
                      <div style="font-family:'Courier New',monospace;font-size:11px;font-weight:700;color:#0f0f0f;margin-bottom:3px">
                        {{ $dispatch->itemPiece->unique_code }}
                      </div>
                      <div style="font-size:9px;color:#7c7470">
                        Condition: <span style="font-weight:600;color:#3B6D11">{{ $dispatch->condition_on_dispatch }} — {{ $conditionLabels[$dispatch->condition_on_dispatch] ?? 'N/A' }}</span>
                      </div>
                    </div>
                    <div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;padding:3px 8px;border-radius:4px;background:#eaf3de;color:#3B6D11;border:1px solid #d1e7b8">
                      Dispatched
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
            </td>
          </tr>
          @endif
          @endforeach
        </tbody>
      </table>
      @endforeach
    @else
      <div class="evsh-tab-empty">
        <svg width="28" height="28" viewBox="0 0 16 16" fill="none" stroke="#d0c8c0" stroke-width="0.8" stroke-linecap="round"><rect x="2" y="2" width="12" height="12" rx="2"/></svg>
        <p>No items dispatched yet</p>
        <span><a href="{{ route('events.checklist', $event) }}" style="color:#CC0000">Build the checklist</a></span>
      </div>
    @endif
  </div>

  {{-- TAB 3: TEAM --}}
  @php $teamStaff = $event->staff()->withPivot('role')->get(); @endphp
  @if($teamStaff->count() > 0)
  <div id="tab-team" class="evsh-tab-body" style="display:none">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
      <div>
        <div style="font-size:13px;font-weight:600;color:#0f0f0f">Assigned Team</div>
        <div style="font-size:11px;color:#a09890;margin-top:2px">{{ $teamStaff->count() }} staff members assigned to this event</div>
      </div>
      <a href="{{ route('events.team', $event) }}" class="evsh-btn-sm">
        Edit team
      </a>
    </div>
    <div class="evt-team-grid">
      @foreach($teamStaff as $staff)
      <div class="evt-team-card">
        <div class="evt-team-avatar">{{ strtoupper(substr($staff->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $staff->name)[1] ?? '', 0, 1)) }}</div>
        <div class="evt-team-info">
          <div class="evt-team-name">
            {{ $staff->name }}
            @if($staff->pivot->role === 'leader')
            <span class="evt-leader-badge">
              <svg width="10" height="10" viewBox="0 0 16 16" fill="currentColor" stroke="none"><path d="M8 1l2 5h5l-4 3.5 1.5 5L8 11l-4.5 3.5 1.5-5L1 6h5z"/></svg>
              Leader
            </span>
            @endif
          </div>
          <div class="evt-team-email">{{ $staff->email }}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- TAB 4: DOCUMENTS --}}
  <div id="tab-documents" class="evsh-tab-body" style="display:none">
    <div style="margin-bottom:14px">
      <div style="font-size:13px;font-weight:600;color:#0f0f0f">Event Documents</div>
      <div style="font-size:11px;color:#a09890;margin-top:2px">Download packing lists, dispatch notes, and receipts for this event</div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px">

      {{-- Planning Packing List FINAL --}}
      @php
        $completedSession = $event->scanSessions()->where('status', 'completed')->latest()->first();
      @endphp
      <div class="doc-card" style="background:#fff;border:1px solid {{ $completedSession ? '#e0e4e8' : '#f0ece8' }};border-radius:8px;padding:12px;transition:all 0.2s;{{ !$completedSession ? 'opacity:0.6;cursor:not-allowed' : 'cursor:pointer' }}"
           @if($completedSession)
           onmouseover="this.style.borderColor='#185FA5';this.style.boxShadow='0 2px 6px rgba(24,95,165,0.08)'"
           onmouseout="this.style.borderColor='#e0e4e8';this.style.boxShadow='none'"
           onclick="downloadDocument(this, '{{ route('events.packing-list.dispatch', [$event, $completedSession]) }}?type=full', 'PACKING LIST FINAL FOR {{ strtoupper($event->name) }}')"
           @endif>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
          <div style="width:38px;height:38px;background:#e6f1fb;border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg width="20" height="20" viewBox="0 0 16 16" fill="none" stroke="#185FA5" stroke-width="1.5" stroke-linecap="round">
              <path d="M4 2h8l3 3v9a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1z"/>
              <path d="M9 2v4h4"/>
            </svg>
          </div>
          <div style="flex:1;min-width:0">
            <div style="font-size:12px;font-weight:700;color:#0f0f0f;margin-bottom:2px">
              Planning Packing List
              <span style="background:#e6f1fb;color:#185FA5;font-size:8px;font-weight:700;padding:2px 6px;border-radius:4px;margin-left:4px">FINAL</span>
            </div>
            <div style="font-size:9px;color:#7c7470">
              @if($completedSession)
                With QR Codes
              @else
                After Dispatch
              @endif
            </div>
          </div>
          <div class="download-icon" style="width:28px;height:28px;background:#f0f7ff;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            @if($completedSession)
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#185FA5" stroke-width="1.8" stroke-linecap="round">
                <path d="M8 1v10M4 7l4 4 4-4"/>
              </svg>
            @else
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#a09890" stroke-width="1.8" stroke-linecap="round">
                <circle cx="8" cy="8" r="6.5"/><line x1="6" y1="8" x2="10" y2="8"/>
              </svg>
            @endif
          </div>
        </div>
      </div>

      {{-- General Summary Report --}}
      <div class="doc-card" style="background:#fff;border:1px solid #ece8e3;border-radius:8px;padding:12px;transition:all 0.2s;cursor:pointer" onmouseover="this.style.borderColor='#CC0000';this.style.boxShadow='0 2px 6px rgba(204,0,0,0.08)'" onmouseout="this.style.borderColor='#ece8e3';this.style.boxShadow='none'" onclick="downloadDocument(this, '{{ route('reports.event.pdf', ['event' => $event->id, 'type' => 'general']) }}', 'GENERAL SUMMARY FOR {{ strtoupper($event->name) }}')">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
          <div style="width:38px;height:38px;background:#fff0f0;border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg width="20" height="20" viewBox="0 0 16 16" fill="none" stroke="#CC0000" stroke-width="1.5" stroke-linecap="round">
              <rect x="2" y="2" width="12" height="12" rx="2"/>
              <line x1="5" y1="6" x2="11" y2="6"/>
              <line x1="5" y1="9" x2="9" y2="9"/>
            </svg>
          </div>
          <div style="flex:1;min-width:0">
            <div style="font-size:12px;font-weight:700;color:#0f0f0f;margin-bottom:2px">General Event Summary</div>
            <div style="font-size:9px;color:#7c7470">Comprehensive Summary</div>
          </div>
          <div class="download-icon" style="width:28px;height:28px;background:#fff8f8;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#CC0000" stroke-width="1.8" stroke-linecap="round">
              <path d="M8 1v10M4 7l4 4 4-4"/>
            </svg>
          </div>
        </div>
      </div>

      {{-- Dispatch Note --}}
      @php
        $completedSession = $event->scanSessions()->where('status', 'completed')->latest()->first();
        $isDispatched = in_array($event->status, ['Active','Set Down','Completed']);
      @endphp
      <div class="doc-card" style="background:#fff;border:1px solid {{ $isDispatched ? '#ece8e3' : '#f0ece8' }};border-radius:8px;padding:12px;transition:all 0.2s;{{ !$isDispatched ? 'opacity:0.6;cursor:not-allowed' : 'cursor:pointer' }}"
           @if($isDispatched)
           onmouseover="this.style.borderColor='#3B6D11';this.style.boxShadow='0 2px 6px rgba(59,109,17,0.08)'"
           onmouseout="this.style.borderColor='#ece8e3';this.style.boxShadow='none'"
           onclick="downloadDocument(this, '{{ route('events.dispatch-note', $event) }}', 'DISPATCH NOTE FOR {{ strtoupper($event->name) }}')"
           @endif>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
          <div style="width:38px;height:38px;background:#eaf3de;border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg width="20" height="20" viewBox="0 0 16 16" fill="none" stroke="#3B6D11" stroke-width="1.5" stroke-linecap="round">
              <rect x="2" y="2" width="12" height="12" rx="2"/>
              <line x1="4" y1="8" x2="8" y2="12"/>
              <line x1="8" y1="12" x2="13" y2="5"/>
            </svg>
          </div>
          <div style="flex:1;min-width:0">
            <div style="font-size:12px;font-weight:700;color:#0f0f0f;margin-bottom:2px">Dispatch Note</div>
            <div style="font-size:9px;color:#7c7470">
              @if($isDispatched)
                Items Dispatched
              @else
                Not dispatched yet
              @endif
            </div>
          </div>
          <div class="download-icon" style="width:28px;height:28px;background:#f0f7ff;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            @if($isDispatched)
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#3B6D11" stroke-width="1.8" stroke-linecap="round">
                <path d="M8 1v10M4 7l4 4 4-4"/>
              </svg>
            @else
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#a09890" stroke-width="1.8" stroke-linecap="round">
                <circle cx="8" cy="8" r="6.5"/><line x1="6" y1="8" x2="10" y2="8"/>
              </svg>
            @endif
          </div>
        </div>
      </div>

      {{-- Return/Receipt Note --}}
      <div class="doc-card" style="background:#fff;border:1px solid {{ in_array($event->status, ['Set Down','Completed']) ? '#ece8e3' : '#f0ece8' }};border-radius:8px;padding:12px;transition:all 0.2s;{{ !in_array($event->status, ['Set Down','Completed']) ? 'opacity:0.6;cursor:not-allowed' : 'cursor:pointer' }}"
           @if(in_array($event->status, ['Set Down','Completed']))
           onmouseover="this.style.borderColor='#854F0B';this.style.boxShadow='0 2px 6px rgba(133,79,11,0.08)'"
           onmouseout="this.style.borderColor='#ece8e3';this.style.boxShadow='none'"
           onclick="downloadDocument(this, '{{ route('reports.event.pdf', ['event' => $event->id, 'type' => 'receive']) }}', 'RECEIPT NOTE FOR {{ strtoupper($event->name) }}')"
           @endif>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
          <div style="width:38px;height:38px;background:#faeeda;border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg width="20" height="20" viewBox="0 0 16 16" fill="none" stroke="#854F0B" stroke-width="1.5" stroke-linecap="round">
              <path d="M8 1v10M4 7l4 4 4-4"/>
              <path d="M2 13h12"/>
            </svg>
          </div>
          <div style="flex:1;min-width:0">
            <div style="font-size:12px;font-weight:700;color:#0f0f0f;margin-bottom:2px">Return & Receipt Note</div>
            <div style="font-size:9px;color:#7c7470">
              @if(in_array($event->status, ['Set Down','Completed']))
                Ready to Download
              @else
                After Event Set Down
              @endif
            </div>
          </div>
          <div class="download-icon" style="width:28px;height:28px;background:#fdf9f0;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            @if(in_array($event->status, ['Set Down','Completed']))
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#854F0B" stroke-width="1.8" stroke-linecap="round">
                <path d="M8 1v10M4 7l4 4 4-4"/>
              </svg>
            @else
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#a09890" stroke-width="1.8" stroke-linecap="round">
                <circle cx="8" cy="8" r="6.5"/><line x1="6" y1="8" x2="10" y2="8"/>
              </svg>
            @endif
          </div>
        </div>
      </div>

    </div>

    {{-- Info Note --}}
    <div style="background:#faf8f6;border:1px solid #f0ece8;border-radius:8px;padding:12px;margin-top:16px;display:flex;gap:10px">
      <svg width="18" height="18" viewBox="0 0 16 16" fill="none" stroke="#5c5550" stroke-width="1.5" stroke-linecap="round" style="flex-shrink:0;margin-top:2px">
        <circle cx="8" cy="8" r="6.5"/>
        <line x1="8" y1="6" x2="8" y2="11"/>
        <circle cx="8" cy="4" r="0.5" fill="#5c5550"/>
      </svg>
      <div style="flex:1">
        <div style="font-size:11px;font-weight:600;color:#0f0f0f;margin-bottom:3px">Document Generation Info</div>
        <div style="font-size:10px;color:#7c7470;line-height:1.5">
          • <strong>Planning Packing List:</strong> Available after creating the event checklist<br>
          • <strong>General Summary:</strong> Available anytime for comprehensive event overview<br>
          • <strong>Dispatch Note:</strong> Available after dispatch completion with all dispatched items<br>
          • <strong>Receipt Note:</strong> Available after event set down for return processing
        </div>
      </div>
    </div>
  </div>

  {{-- TAB 5: ANALYTICS (renumbered from 4) --}}
  @php
    $dispatchedItems = $event->eventItems->whereNotNull('dispatched_at');
    $totalDispatched = $dispatchedItems->count();
  @endphp
  @if($totalDispatched > 0)
  <div id="tab-analytics" class="evsh-tab-body" style="display:none">
    @php
      $processedItems = $event->eventItems->where('return_processed', true);
      $processedCount = $processedItems->count();
      $pendingCount = $totalDispatched - $processedCount;
      $pctReturned = $totalDispatched > 0 ? round(($processedCount / $totalDispatched) * 100) : 0;

      $toWarehouse = $processedItems->where('return_destination', 'warehouse')->count();
      $toCleaning = $processedItems->where('return_destination', 'cleaning')->count();
      $toRepair = $processedItems->where('return_destination', 'repair')->count();

      $pctWarehouse = $processedCount > 0 ? round(($toWarehouse / $processedCount) * 100) : 0;
      $pctCleaning = $processedCount > 0 ? round(($toCleaning / $processedCount) * 100) : 0;
      $pctRepair = $processedCount > 0 ? round(($toRepair / $processedCount) * 100) : 0;

      $firstDispatch = $dispatchedItems->min('dispatched_at');
      $lastReturn = $processedItems->max('returned_at');
    @endphp

    <div style="margin-bottom:14px">
      <div style="font-size:13px;font-weight:600;color:#0f0f0f">Item Lifecycle Analytics</div>
      <div style="font-size:11px;color:#a09890;margin-top:2px">Comprehensive tracking, flow timeline, and return condition distribution</div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr; gap: 24px;">
        
        {{-- FLOW TIMELINE --}}
        <div style="background: #faf8f6; border: 1px solid #ece8e3; border-radius: 12px; padding: 24px; position: relative;">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; position: relative; z-index: 2;">
            
            {{-- NODE 1: DISPATCHED --}}
            <div style="text-align: center; width: 120px;">
              <div style="width: 48px; height: 48px; background: #fff; border: 2px solid #0f0f0f; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 700; color: #0f0f0f; margin: 0 auto 10px; box-shadow: 0 0 0 4px #faf8f6;">
                {{ $totalDispatched }}
              </div>
              <div style="font-size: 11px; font-weight: 700; color: #0f0f0f; text-transform: uppercase; letter-spacing: 0.05em;">Dispatched</div>
              <div style="font-size: 10px; color: #5c5550; margin-top: 4px; display: flex; align-items: center; justify-content: center; gap: 4px;">
                <svg width="10" height="10" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="3" width="12" height="11" rx="2"/><line x1="5" y1="1" x2="5" y2="5"/><line x1="11" y1="1" x2="11" y2="5"/></svg>
                {{ $firstDispatch ? \Carbon\Carbon::parse($firstDispatch)->format('d M, H:i') : '—' }}
              </div>
            </div>

            {{-- MAIN CONNECTION LINE --}}
            <div style="position: absolute; top: 24px; left: 80px; right: 80px; height: 3px; background: #ece8e3; z-index: -1;">
              <div style="height: 100%; background: #0f0f0f; width: {{ $pctReturned }}%; transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);"></div>
            </div>

            {{-- NODE 2: RETURNED --}}
            <div style="text-align: center; width: 120px;">
              <div style="width: 48px; height: 48px; background: {{ $pctReturned == 100 ? '#3B6D11' : '#fff' }}; border: 2px solid {{ $pctReturned == 100 ? '#3B6D11' : ($processedCount > 0 ? '#0f0f0f' : '#ece8e3') }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 700; color: {{ $pctReturned == 100 ? '#fff' : ($processedCount > 0 ? '#0f0f0f' : '#a09890') }}; margin: 0 auto 10px; box-shadow: 0 0 0 4px #faf8f6; transition: all 0.3s ease;">
                {{ $processedCount }}
              </div>
              <div style="font-size: 11px; font-weight: 700; color: {{ $processedCount > 0 ? '#0f0f0f' : '#a09890' }}; text-transform: uppercase; letter-spacing: 0.05em;">Received</div>
              <div style="font-size: 10px; color: {{ $pendingCount > 0 ? '#CC0000' : '#5c5550' }}; margin-top: 4px; display: flex; align-items: center; justify-content: center; gap: 4px; font-weight: {{ $pendingCount > 0 ? '600' : '400' }};">
                @if($pendingCount > 0)
                  {{ $pendingCount }} pending
                @else
                  <svg width="10" height="10" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M2 13V5l6-3 6 3v8"/><rect x="5" y="9" width="6" height="4"/></svg>
                  {{ $lastReturn ? \Carbon\Carbon::parse($lastReturn)->format('d M, H:i') : '—' }}
                @endif
              </div>
            </div>

          </div>
        </div>

        {{-- CHARTS & TRIAGE --}}
        @if($processedCount > 0)
        <div style="display: grid; grid-template-columns: 180px 1fr; gap: 40px; align-items: center; padding: 10px;">
          
          {{-- CSS DONUT CHART --}}
          @php
            $degW = ($pctWarehouse / 100) * 360;
            $degC = ($pctCleaning / 100) * 360;
            $degR = ($pctRepair / 100) * 360;
          @endphp
          <div style="display: flex; flex-direction: column; align-items: center; gap: 16px;">
            <div style="position: relative; width: 130px; height: 130px; border-radius: 50%; background: conic-gradient(
                #3B6D11 0deg {{ $degW }}deg, 
                #0F6E56 {{ $degW }}deg {{ $degW + $degC }}deg, 
                #CC0000 {{ $degW + $degC }}deg 360deg
              ); box-shadow: inset 0 2px 4px rgba(0,0,0,0.1), 0 4px 10px rgba(0,0,0,0.05);">
              
              {{-- Inner hole --}}
              <div style="position: absolute; top: 22px; left: 22px; right: 22px; bottom: 22px; background: #fff; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <span style="font-size: 20px; font-weight: 800; color: #0f0f0f; line-height: 1;">{{ $processedCount }}</span>
                <span style="font-size: 8px; font-weight: 700; color: #a09890; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 3px;">Returned</span>
              </div>
            </div>
            <div style="font-size: 10px; font-weight: 700; color: #a09890; text-transform: uppercase; letter-spacing: 0.1em; text-align: center;">Triage Distribution</div>
          </div>

          {{-- TRIAGE DESTINATIONS LIST (Progress Bars) --}}
          <div style="display: flex; flex-direction: column; gap: 16px;">
            
            {{-- Warehouse Bar --}}
            <div style="display: flex; align-items: center; gap: 16px;">
              <div style="width: 32px; font-size: 12px; font-weight: 700; color: #3B6D11; text-align: right;">{{ $pctWarehouse }}%</div>
              <div style="flex: 1; height: 32px; background: #f5f2ee; border-radius: 8px; overflow: hidden; position: relative; box-shadow: inset 0 1px 3px rgba(0,0,0,0.04);">
                <div style="height: 100%; background: #3B6D11; width: {{ $pctWarehouse }}%; transition: width 1s ease-out;"></div>
                <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: space-between; padding: 0 12px; font-size: 11px; font-weight: 600;">
                  <span style="color: {{ $pctWarehouse > 15 ? '#fff' : '#3a3530' }}; text-shadow: {{ $pctWarehouse > 15 ? '0 1px 2px rgba(0,0,0,0.2)' : 'none' }}; letter-spacing: 0.02em;">To Warehouse</span>
                  <span style="color: {{ $pctWarehouse > 85 ? '#fff' : '#5c5550' }};">{{ $toWarehouse }} {{ Str::plural('item', $toWarehouse) }}</span>
                </div>
              </div>
            </div>

            {{-- Cleaning Bar --}}
            <div style="display: flex; align-items: center; gap: 16px;">
              <div style="width: 32px; font-size: 12px; font-weight: 700; color: #0F6E56; text-align: right;">{{ $pctCleaning }}%</div>
              <div style="flex: 1; height: 32px; background: #f5f2ee; border-radius: 8px; overflow: hidden; position: relative; box-shadow: inset 0 1px 3px rgba(0,0,0,0.04);">
                <div style="height: 100%; background: #0F6E56; width: {{ $pctCleaning }}%; transition: width 1s ease-out 0.2s;"></div>
                <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: space-between; padding: 0 12px; font-size: 11px; font-weight: 600;">
                  <span style="color: {{ $pctCleaning > 15 ? '#fff' : '#3a3530' }}; text-shadow: {{ $pctCleaning > 15 ? '0 1px 2px rgba(0,0,0,0.2)' : 'none' }}; letter-spacing: 0.02em;">To Cleaning</span>
                  <span style="color: {{ $pctCleaning > 85 ? '#fff' : '#5c5550' }};">{{ $toCleaning }} {{ Str::plural('item', $toCleaning) }}</span>
                </div>
              </div>
            </div>

            {{-- Repair Bar --}}
            <div style="display: flex; align-items: center; gap: 16px;">
              <div style="width: 32px; font-size: 12px; font-weight: 700; color: #CC0000; text-align: right;">{{ $pctRepair }}%</div>
              <div style="flex: 1; height: 32px; background: #f5f2ee; border-radius: 8px; overflow: hidden; position: relative; box-shadow: inset 0 1px 3px rgba(0,0,0,0.04);">
                <div style="height: 100%; background: #CC0000; width: {{ $pctRepair }}%; transition: width 1s ease-out 0.4s;"></div>
                <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: space-between; padding: 0 12px; font-size: 11px; font-weight: 600;">
                  <span style="color: {{ $pctRepair > 15 ? '#fff' : '#3a3530' }}; text-shadow: {{ $pctRepair > 15 ? '0 1px 2px rgba(0,0,0,0.2)' : 'none' }}; letter-spacing: 0.02em;">To Repair</span>
                  <span style="color: {{ $pctRepair > 85 ? '#fff' : '#5c5550' }};">{{ $toRepair }} {{ Str::plural('item', $toRepair) }}</span>
                </div>
              </div>
            </div>

          </div>

        </div>
        @endif

      </div>
  </div>
  @endif

  {{-- MISSING ITEMS PANEL --}}
  @php
      $missingItems = $event->missingItems()
          ->where('status', 'missing')
          ->with('item')
          ->get();
  @endphp

  @if($missingItems->count() > 0)
  <div class="ev-missing-panel">
      <div class="ev-missing-header">
          <div class="ev-missing-title">
              ⚠ Missing Items
              <span class="ev-missing-count">{{ $missingItems->count() }}</span>
          </div>
          <a href="{{ route('events.receiving-report', $event) }}"
             target="_blank"
             class="ev-missing-report-link">
              View Full Report
          </a>
      </div>
      <div class="ev-missing-list">
          @foreach($missingItems as $missing)
          <div class="ev-missing-item">
              <div class="ev-missing-item-info">
                  <span class="ev-missing-code">{{ $missing->unique_code }}</span>
                  <span class="ev-missing-name">{{ $missing->item->name }}</span>
              </div>
              <div class="ev-missing-item-actions">
                  <form method="POST"
                        action="{{ route('events.missing.resolve', [$event, $missing]) }}"
                        style="display:inline">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="ev-missing-resolve-btn"
                              onclick="return confirm('Mark {{ $missing->unique_code }} as found?')">
                          Mark Found
                      </button>
                  </form>
              </div>
          </div>
          @endforeach
      </div>
  </div>
  @endif

  {{-- TAB 5: GALLERY --}}
  @if($allImages->count() > 0)
  <div id="tab-gallery" class="evsh-tab-body" style="display:none">
    <div style="margin-bottom:14px">
      <div style="font-size:13px;font-weight:600;color:#0f0f0f">Dispatch Photos</div>
          <div style="font-size:11px;color:#a09890;margin-top:2px">{{ $allImages->count() }} photos uploaded across all items</div>
    </div>
    <div class="evsh-gallery">
      @foreach($allImages as $image)
      <div class="evsh-gallery-img" onclick="openImageModal('{{ $image->url }}', '{{ $image->eventItem->item->name ?? "Item" }}', '{{ $image->type }}')">
        <img src="{{ $image->url }}" alt="Dispatch photo">
        <div class="evsh-gallery-overlay">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#fff" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="3"/><path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z"/></svg>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- TAB 6: ACTIVITY --}}
  <div id="tab-activity" class="evsh-tab-body" style="display:none">
    <div style="margin-bottom:14px">
      <div style="font-size:13px;font-weight:600;color:#0f0f0f">Event Activity Log</div>
      <div style="font-size:11px;color:#a09890;margin-top:2px">Complete activity history for this event</div>
    </div>
    @forelse($logs as $log)
    @php
      $iconClass = match(true) {
        str_contains(strtolower($log->action),'dispatch') || str_contains(strtolower($log->action),'scheduled') => 'evsh-act-blue',
        str_contains(strtolower($log->action),'return') || str_contains(strtolower($log->action),'found') => 'evsh-act-green',
        str_contains(strtolower($log->action),'damage') || str_contains(strtolower($log->action),'missing') => 'evsh-act-red',
        str_contains(strtolower($log->action),'repair') => 'evsh-act-amber',
        str_contains(strtolower($log->action),'site') || str_contains(strtolower($log->action),'link') => 'evsh-act-purple',
        default => 'evsh-act-green',
      };

      $icon = match(true) {
        str_contains(strtolower($log->action),'dispatch') => '<path d="M1 8l4-4 4 4M5 4v10"/><path d="M11 8l4 4v-10"/>',
        str_contains(strtolower($log->action),'return') => '<path d="M1 8l4 4 4-4M5 12V2"/><path d="M11 8l4-4v10"/>',
        str_contains(strtolower($log->action),'scheduled') => '<rect x="2" y="3" width="12" height="11" rx="2"/><line x1="5" y1="1" x2="5" y2="5"/><line x1="11" y1="1" x2="11" y2="5"/>',
        str_contains(strtolower($log->action),'site') => '<path d="M2 8h5M9 8h5M7 3l-5 5 5 5M9 3l5 5-5 5"/>',
        str_contains(strtolower($log->action),'found') => '<path d="M4 8l2 2 5-5"/><circle cx="8" cy="8" r="6.5"/>',
        default => '<circle cx="8" cy="8" r="6.5"/><line x1="8" y1="5" x2="8" y2="8.5"/><circle cx="8" cy="11" r="0.5" fill="currentColor"/>',
      };
    @endphp
    <div class="evsh-act-item">
      <div class="evsh-act-icon {{ $iconClass }}">
        <svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">{!! $icon !!}</svg>
      </div>
      <div class="evsh-act-text">
        <span class="evsh-act-main">
          @if($log->item)
            <strong>{{ $log->item->name }}</strong> — {{ $log->description ?? $log->action }}
          @else
            {{ $log->description ?? $log->action }}
          @endif
        </span>
        <span class="evsh-act-time">
          {{ $log->created_at->diffForHumans() }}
          @if($log->user)
            <span style="color:#b0a8a0;margin-left:6px">by {{ $log->user->name }}</span>
          @endif
        </span>
      </div>
    </div>
    @empty
    <div class="evsh-tab-empty">
      <svg width="28" height="28" viewBox="0 0 16 16" fill="none" stroke="#d0c8c0" stroke-width="0.8" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><polyline points="8 4 8 8 11 10"/></svg>
      <p>No activity recorded yet</p>
      <span>Activity will appear here as the event progresses through scheduling, dispatch, and return.</span>
    </div>
    @endforelse
  </div>

  {{-- TAB 7: NOTES --}}
  @if($event->notes)
  <div id="tab-notes" class="evsh-tab-body" style="display:none">
    <div style="margin-bottom:14px">
      <div style="font-size:13px;font-weight:600;color:#0f0f0f">Event Notes</div>
      <div style="font-size:11px;color:#a09890;margin-top:2px">Internal notes and special instructions for this event</div>
    </div>
    <div style="background:#faf8f6;border:1px solid #f0ece8;border-radius:8px;padding:14px">
      <p style="font-size:12px;color:#3a3530;line-height:1.6;margin:0">{{ $event->notes }}</p>
    </div>
  </div>
  @endif

  {{-- TAB 8: ACTIONS --}}
  <div id="tab-actions" class="evsh-tab-body" style="display:none">
    <div style="margin-bottom:14px">
      <div style="font-size:13px;font-weight:600;color:#0f0f0f">Download Reports</div>
      <div style="font-size:11px;color:#a09890;margin-top:2px">Generate PDF reports for this event</div>
    </div>
    <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:20px;">
        <a href="{{ route('reports.event.pdf', ['event' => $event->id, 'type' => 'general']) }}" target="_blank" class="evsh-quick-btn" style="border: 1px solid #CC0000; color: #CC0000; background: #fff8f8;">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M1 12v3h14v-3M8 1v11M4 8l4 4 4-4"/></svg>
          General Event Summary
        </a>
        <a href="{{ route('reports.event.pdf', ['event' => $event->id, 'type' => 'checklist']) }}" target="_blank" class="evsh-quick-btn" style="border-color: #185FA5; color: #185FA5; background: #f0f7ff;">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M1 12v3h14v-3M8 1v11M4 8l4 4 4-4"/></svg>
          Dispatch Checklist
        </a>
        @if(in_array($event->status, ['Set Down', 'Completed']))
        <a href="{{ route('reports.event.pdf', ['event' => $event->id, 'type' => 'receive']) }}" target="_blank" class="evsh-quick-btn" style="border-color: #3B6D11; color: #3B6D11; background: #eaf3de;">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M1 12v3h14v-3M8 1v11M4 8l4 4 4-4"/></svg>
          Return & Triage Report
        </a>
        @endif
      </div>

      <div style="font-size:13px;font-weight:600;color:#0f0f0f;margin:20px 0 10px">Quick Actions</div>
      <div style="display:flex;flex-direction:column;gap:6px">
        <a href="{{ route('events.checklist', $event) }}" class="evsh-quick-btn">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 8l2 2 5-5"/><rect x="2" y="2" width="12" height="12" rx="2"/></svg>
          View / Edit Checklist
        </a>
        <a href="{{ route('events.team', $event) }}" class="evsh-quick-btn">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="6" r="2.5"/><path d="M2 13c0-3 2.5-5 6-5s6 2 6 5"/></svg>
          Assign Team
        </a>
        <a href="{{ route('events.dispatch.manual', $event) }}" class="evsh-quick-btn">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M2 13V5l6-3 6 3v8"/><rect x="6" y="9" width="4" height="4"/></svg>
          Manual Dispatch
        </a>
        <a href="{{ route('events.edit', $event) }}" class="evsh-quick-btn">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M1 12l3-3 8-8 3 3-8 8-3 1z"/></svg>
          Edit Event Details
        </a>
        @if(!in_array($event->status, ['Active','Set Down']))
        <form method="POST" action="{{ route('events.destroy', $event) }}"
              onsubmit="return confirm('Permanently delete this event?')">
          @csrf @method('DELETE')
          <button type="submit" class="evsh-quick-btn evsh-quick-danger" style="width:100%;text-align:left;font-family:'Inter',sans-serif">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M3 4h10M5 4V3h6v1M6 7v5M10 7v5"/><rect x="2" y="4" width="12" height="10" rx="1.5"/></svg>
            Delete Event
          </button>
        </form>
        @endif
      </div>
  </div>

</div>{{-- END tabs-card --}}

{{-- IMAGE PREVIEW MODAL --}}
<div id="imageModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.9);z-index:9999;align-items:center;justify-content:center;" onclick="closeImageModal()">
  <div style="position:relative;max-width:90%;max-height:90%;display:flex;flex-direction:column;align-items:center;">
    <div style="background:#fff;border-radius:8px 8px 0 0;padding:12px 20px;width:100%;display:flex;justify-content:space-between;align-items:center;">
      <div>
        <div style="font-size:14px;font-weight:700;color:#0f0f0f" id="modalItemName"></div>
        <div style="font-size:11px;color:#7c7470;margin-top:2px" id="modalImageType"></div>
      </div>
      <button onclick="closeImageModal()" style="background:none;border:none;cursor:pointer;color:#a09890;font-size:24px;line-height:1;padding:0;width:30px;height:30px;">&times;</button>
    </div>
    <img id="modalImage" src="" style="max-width:100%;max-height:calc(90vh - 60px);object-fit:contain;border-radius:0 0 8px 8px;" onclick="event.stopPropagation()">
  </div>
</div>

<script>
function openImageModal(url, itemName, type) {
  const modal = document.getElementById('imageModal');
  const img = document.getElementById('modalImage');
  const nameEl = document.getElementById('modalItemName');
  const typeEl = document.getElementById('modalImageType');

  img.src = url;
  nameEl.textContent = itemName;
  typeEl.textContent = type === 'dispatch' ? '📦 Dispatch Photo' : '📥 Return Photo';

  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeImageModal() {
  const modal = document.getElementById('imageModal');
  modal.style.display = 'none';
  document.body.style.overflow = '';
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeImageModal();
  }
});

// Status dropdown toggle
function toggleStatusDropdown(event) {
  event.stopPropagation();
  var menu = document.getElementById('status-dropdown-menu');
  if (menu.style.display === 'none' || menu.style.display === '') {
    menu.style.display = 'block';
  } else {
    menu.style.display = 'none';
  }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
  var dropdown = document.querySelector('.evsh-status-dropdown');
  var menu = document.getElementById('status-dropdown-menu');
  if (menu && dropdown && !dropdown.contains(e.target)) {
    menu.style.display = 'none';
  }
});

// Confirm status change
function confirmStatusChange(newStatus, currentStatus) {
  if (newStatus === 'Set Down' && currentStatus === 'Completed') {
    return confirm('Revert event from Completed to Set Down?\n\nThis will allow you to use site-to-site linking or receive remaining items.');
  }
  if (newStatus === 'Cancelled') {
    return confirm('Cancel this event?\n\nThis action can be reversed later.');
  }
  return confirm('Change event status from "' + currentStatus + '" to "' + newStatus + '"?');
}

// Dispatch modal functions
function openDispatchModal() {
  document.getElementById('dispatch-modal').style.display = 'flex';
}

function closeDispatchModal() {
  document.getElementById('dispatch-modal').style.display = 'none';
}

document.getElementById('dispatch-modal')?.addEventListener('click', function(e) {
  if (e.target === this) closeDispatchModal();
});

// Tab switching function
function showTab(name, el) {
  document.querySelectorAll('.evsh-tab-body').forEach(function(t){ t.style.display='none'; });
  document.querySelectorAll('.evsh-tab').forEach(function(t){ t.classList.remove('active'); });
  document.getElementById('tab-'+name).style.display='block';
  el.classList.add('active');
}

// Inline document download with state feedback
function downloadDocument(element, url, filename) {
  const downloadIcon = element.querySelector('.download-icon svg');
  const originalHTML = downloadIcon.outerHTML;

  // Change to downloading state
  downloadIcon.outerHTML = '<svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" class="spinning"><circle cx="8" cy="8" r="6" opacity="0.25"/><path d="M8 2a6 6 0 0 1 6 6"/></svg>';
  element.style.opacity = '0.7';
  element.style.pointerEvents = 'none';

  // Create invisible link and trigger download
  const link = document.createElement('a');
  link.href = url;
  link.download = filename || '';
  link.style.display = 'none';
  document.body.appendChild(link);
  link.click();

  // Reset state after download starts (800ms)
  setTimeout(function() {
    const currentIcon = element.querySelector('.download-icon svg');
    if (currentIcon) {
      currentIcon.outerHTML = '<svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#3B6D11" stroke-width="2" stroke-linecap="round"><path d="M4 8l2 2 5-5"/></svg>';
    }
    element.style.opacity = '1';

    // Remove link
    setTimeout(function() {
      document.body.removeChild(link);

      // Reset icon after success checkmark
      setTimeout(function() {
        const finalIcon = element.querySelector('.download-icon svg');
        if (finalIcon) {
          finalIcon.outerHTML = originalHTML;
        }
        element.style.pointerEvents = 'auto';
      }, 1200);
    }, 100);
  }, 800);
}
</script>

<style>
.spinning {
  animation: spin 0.8s linear infinite;
}
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>

{{-- DISPATCH MODAL --}}
<div id="dispatch-modal" class="disp-modal-overlay" style="display:none">
  <div class="disp-modal">
    <div class="disp-modal-header">
      <div>
        <div class="disp-modal-title">Dispatch Items</div>
        <div class="disp-modal-sub">{{ $event->name }} · {{ $event->loading_date->format('d M Y') }}</div>
      </div>
      <button onclick="closeDispatchModal()" class="disp-modal-close">✕</button>
    </div>
    <div class="disp-modal-summary">
      {{ $event->eventItems->count() }} item types ·
      {{ $event->borrowedItems->count() }} borrowed ·
      {{ $event->operationalItems->count() }} operational
    </div>
    <div class="disp-modal-options">

      {{-- OPTION A: SCAN SESSION --}}
      <div class="disp-option disp-option-primary">
        <div class="disp-option-badge">Recommended</div>
        <div class="disp-option-icon">📱</div>
        <div class="disp-option-title">Scan Session</div>
        <div class="disp-option-desc">
          Generate a session QR code. Warehouse staff scan it with their phones to load items one by one. Fast, accurate, and fully tracked by individual piece.
        </div>
        <form method="POST" action="{{ route('events.scan.start', $event) }}">
          @csrf
          <button type="submit" class="disp-option-btn disp-option-btn-primary">
            Start Scan Session
          </button>
        </form>
      </div>

      {{-- OPTION B: MANUAL DISPATCH --}}
      <div class="disp-option disp-option-secondary">
        <div class="disp-option-icon">📋</div>
        <div class="disp-option-title">Manual Dispatch</div>
        <div class="disp-option-desc">
          Dispatch items manually by entering piece codes and recording conditions. Use this if scanning is unavailable or as a fallback.
        </div>
        <a href="{{ route('events.dispatch.manual', $event) }}"
           class="disp-option-btn disp-option-btn-secondary">
          Manual Dispatch
        </a>
      </div>

    </div>
  </div>
</div>

{{-- DISPATCH PRINT MODAL --}}
@php
  $allSessions = $event->scanSessions()->whereIn('status', ['completed', 'active'])->orderBy('dispatch_batch')->get();
@endphp
<div id="dispatch-print-modal" class="edit-modal-overlay" style="display:none">
  <div class="edit-modal" style="max-width:480px">
    <div class="edit-modal-header">
      <div>
        <div class="edit-modal-title">Select Dispatch Note Type</div>
        <div class="edit-modal-sub">Choose what to include in the document</div>
      </div>
      <button onclick="closeDispatchPrintModal()" class="edit-modal-close">✕</button>
    </div>
    <div class="edit-modal-body">
      <div style="display:flex;flex-direction:column;gap:10px">

        {{-- Option 1: Full Dispatch Note --}}
        <label class="print-option" onclick="printDispatchNote('full')">
          <input type="radio" name="print_type" value="full" style="width:18px;height:18px">
          <div style="flex:1">
            <div style="font-size:12px;font-weight:700;color:#0f0f0f;margin-bottom:3px">Full Dispatch Note</div>
            <div style="font-size:10px;color:#7c7470;line-height:1.4">
              All items from all dispatch batches (initial + additional)
            </div>
          </div>
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
            <path d="M6 4l4 4-4 4"/>
          </svg>
        </label>

        {{-- Option 2: Additional Items Only --}}
        <label class="print-option" onclick="printDispatchNote('additional')">
          <input type="radio" name="print_type" value="additional" style="width:18px;height:18px">
          <div style="flex:1">
            <div style="font-size:12px;font-weight:700;color:#0f0f0f;margin-bottom:3px">Additional Items Only</div>
            <div style="font-size:10px;color:#7c7470;line-height:1.4">
              Only the most recent additional dispatch batch
            </div>
          </div>
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
            <path d="M6 4l4 4-4 4"/>
          </svg>
        </label>

      </div>
    </div>
  </div>
</div>

{{-- EDIT EVENT MODAL --}}
<div id="edit-modal" class="edit-modal-overlay" style="display:none">
  <div class="edit-modal">
    <div class="edit-modal-header">
      <div>
        <div class="edit-modal-title">Edit Event</div>
        <div class="edit-modal-sub">{{ $event->name }}</div>
      </div>
      <button onclick="closeEditModal()" class="edit-modal-close">✕</button>
    </div>
    <div class="edit-modal-body">
      <div class="edit-modal-options">

        {{-- OPTION 1: Edit Event Details --}}
        <a href="{{ route('events.edit', $event) }}" class="edit-option">
          <div class="edit-option-icon" style="background:#e6f1fb;color:#185FA5">
            <svg width="20" height="20" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
              <rect x="2" y="3" width="12" height="11" rx="2"/>
              <line x1="5" y1="1" x2="5" y2="5"/>
              <line x1="11" y1="1" x2="11" y2="5"/>
            </svg>
          </div>
          <div class="edit-option-content">
            <div class="edit-option-title">Edit Event Details</div>
            <div class="edit-option-desc">Update event name, dates, client, venue, and location</div>
          </div>
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" class="edit-option-arrow">
            <path d="M6 4l4 4-4 4"/>
          </svg>
        </a>

        {{-- OPTION 2: Edit Packing List --}}
        @php
          $hasDispatch = $event->eventItems->whereNotNull('dispatched_at')->count() > 0;
        @endphp
        @if(!$hasDispatch)
          <a href="{{ route('events.checklist', $event) }}" class="edit-option">
            <div class="edit-option-icon" style="background:#eaf3de;color:#3B6D11">
              <svg width="20" height="20" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                <rect x="2" y="2" width="12" height="12" rx="2"/>
                <line x1="5" y1="6" x2="11" y2="6"/>
                <line x1="5" y1="9" x2="9" y2="9"/>
              </svg>
            </div>
            <div class="edit-option-content">
              <div class="edit-option-title">Edit Packing List</div>
              <div class="edit-option-desc">Add or remove items, adjust quantities, manage borrowed items</div>
            </div>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" class="edit-option-arrow">
              <path d="M6 4l4 4-4 4"/>
            </svg>
          </a>
        @else
          <div class="edit-option edit-option-disabled">
            <div class="edit-option-icon" style="background:#f5f1ed;color:#a09890">
              <svg width="20" height="20" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                <circle cx="8" cy="8" r="6.5"/>
                <line x1="6" y1="8" x2="10" y2="8"/>
              </svg>
            </div>
            <div class="edit-option-content">
              <div class="edit-option-title">Edit Packing List</div>
              <div class="edit-option-desc">Cannot edit packing list after items have been dispatched</div>
            </div>
          </div>
        @endif

        {{-- OPTION 3: Edit Assigned Team --}}
        <a href="{{ route('events.team', $event) }}" class="edit-option">
          <div class="edit-option-icon" style="background:#eeedfe;color:#534AB7">
            <svg width="20" height="20" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
              <circle cx="8" cy="6" r="2.5"/>
              <path d="M2 13c0-3 2.5-5 6-5s6 2 6 5"/>
            </svg>
          </div>
          <div class="edit-option-content">
            <div class="edit-option-title">Edit Assigned Team</div>
            <div class="edit-option-desc">Manage team members and assign event leader</div>
          </div>
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" class="edit-option-arrow">
            <path d="M6 4l4 4-4 4"/>
          </svg>
        </a>

      </div>
    </div>
  </div>
</div>

{{-- ADDITIONAL DISPATCH MODAL --}}
<div id="additional-dispatch-modal" class="edit-modal-overlay" style="display:none">
  <div class="edit-modal" style="max-width:680px">
    <div class="edit-modal-header">
      <div>
        <div class="edit-modal-title">Dispatch Additional Items</div>
        <div class="edit-modal-sub">{{ $event->name }} • Select items to dispatch</div>
      </div>
      <button onclick="closeAdditionalDispatchModal()" class="edit-modal-close">✕</button>
    </div>
    <div class="edit-modal-body">
      <div style="background:#fff8f8;border:1px solid #f5c0c0;border-radius:8px;padding:12px;margin-bottom:16px;display:flex;gap:10px">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#CC0000" stroke-width="1.5" stroke-linecap="round" style="flex-shrink:0;margin-top:2px">
          <circle cx="8" cy="8" r="6.5"/>
          <line x1="8" y1="6" x2="8" y2="11"/>
          <circle cx="8" cy="4" r="0.5" fill="#CC0000"/>
        </svg>
        <div style="flex:1">
          <div style="font-size:11px;font-weight:600;color:#CC0000;margin-bottom:3px">Additional Dispatch</div>
          <div style="font-size:10px;color:#7c7470;line-height:1.4">
            These items will be added to the existing dispatch for this event. They will be tracked under the same event but marked as a later batch.
          </div>
        </div>
      </div>

      <form id="additional-dispatch-form" onsubmit="handleAdditionalDispatch(event)">
        @csrf
        <div style="max-height:400px;overflow-y:auto;margin-bottom:16px">
          @php
            $undispatched = $event->eventItems()->with('item')->whereNull('dispatched_at')->get();
          @endphp
          @if($undispatched->count() > 0)
            <div style="display:flex;flex-direction:column;gap:8px">
              @foreach($undispatched as $eventItem)
                <label class="additional-item-card" style="display:flex;align-items:center;gap:12px;padding:12px;background:#fff;border:1px solid #e0e4e8;border-radius:8px;cursor:pointer;transition:all 0.2s">
                  <input type="checkbox" name="items[]" value="{{ $eventItem->item_id }}" class="additional-item-checkbox" style="width:18px;height:18px;cursor:pointer">
                  @if($eventItem->item->primaryImage)
                    <img src="{{ asset('storage/' . $eventItem->item->primaryImage->image_path) }}" style="width:50px;height:50px;object-fit:cover;border-radius:6px;border:1px solid #f0ece8">
                  @else
                    <div style="width:50px;height:50px;background:#f5f1ed;border-radius:6px;display:flex;align-items:center;justify-content:center">
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#b0a8a0" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <path d="M21 15l-5-5L5 21"/>
                      </svg>
                    </div>
                  @endif
                  <div style="flex:1">
                    <div style="font-size:12px;font-weight:600;color:#0f0f0f">{{ $eventItem->item->name }}</div>
                    <div style="font-size:10px;color:#7c7470">{{ $eventItem->item->category }} • ITM-{{ str_pad($eventItem->item->id, 3, '0', STR_PAD_LEFT) }}</div>
                  </div>
                  <div style="display:flex;align-items:center;gap:6px">
                    <span style="font-size:10px;color:#7c7470">Qty:</span>
                    <span style="font-size:13px;font-weight:700;color:#0f0f0f">{{ $eventItem->quantity_requested }}</span>
                  </div>
                </label>
              @endforeach
            </div>
          @else
            <div style="text-align:center;padding:40px;color:#b0a8a0">
              <svg width="40" height="40" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1" style="margin:0 auto 12px">
                <path d="M4 8l2 2 5-5"/><circle cx="8" cy="8" r="6.5"/>
              </svg>
              <div style="font-size:12px;font-weight:600;color:#5c5550;margin-bottom:4px">All Items Dispatched</div>
              <div style="font-size:11px">No additional items to dispatch for this event</div>
            </div>
          @endif
        </div>

        @if($undispatched->count() > 0)
        <div style="display:flex;gap:10px;justify-content:flex-end">
          <button type="button" onclick="closeAdditionalDispatchModal()" style="padding:10px 20px;border:1px solid #e0e4e8;background:#fff;color:#5c5550;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
            Cancel
          </button>
          <button type="submit" style="padding:10px 20px;border:1px solid #854F0B;background:#854F0B;color:#fff;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
              <path d="M6 4l4 4-4 4"/>
            </svg>
            Continue to Dispatch
          </button>
        </div>
        @endif
      </form>
    </div>
  </div>
</div>

{{-- RECEIVE MODAL --}}
<div id="receive-modal" class="disp-modal-overlay" style="display:none">
  <div class="disp-modal">
    <div class="disp-modal-header">
      <div>
        <div class="disp-modal-title">Receive Items</div>
        <div class="disp-modal-sub">
            {{ $event->name }} · {{ optional($event->setdown_date)->format('d M Y') }}
        </div>
      </div>
      <button onclick="closeReceiveModal()" class="disp-modal-close">✕</button>
    </div>
    <div class="disp-modal-summary">
        {{ $event->eventPieceDispatches->count() }} pieces dispatched ·
        {{ $event->borrowedItems->count() }} borrowed items ·
        {{ $event->operationalItems->count() }} operational items
    </div>
    <div class="disp-modal-options">

      {{-- SCAN SESSION --}}
      <div class="disp-option disp-option-primary">
        <div class="disp-option-badge">Recommended</div>
        <div class="disp-option-icon">📱</div>
        <div class="disp-option-title">Scan Session</div>
        <div class="disp-option-desc">
            Generate a receipt note with a QR code. Warehouse staff scan returning items one by one on their phones. Accurate, fully tracked.
        </div>
        <form method="POST" action="{{ route('events.receive.start', $event) }}">
          @csrf
          <button type="submit" class="disp-option-btn disp-option-btn-primary">
              Start Receive Session
          </button>
        </form>
      </div>

      {{-- MANUAL RECEIVE --}}
      <div class="disp-option disp-option-secondary">
        <div class="disp-option-icon">📋</div>
        <div class="disp-option-title">Manual Receive</div>
        <div class="disp-option-desc">
            Receive items manually by entering piece codes and recording conditions. Use if scanning is unavailable.
        </div>
        <a href="{{ route('events.receive.manual', $event) }}"
           class="disp-option-btn disp-option-btn-secondary">
            Manual Receive
        </a>
      </div>

    </div>
  </div>
</div>

<script>
function openEditModal() {
  document.getElementById('edit-modal').style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeEditModal() {
  document.getElementById('edit-modal').style.display = 'none';
  document.body.style.overflow = 'auto';
}

function openAdditionalDispatchModal() {
  document.getElementById('additional-dispatch-modal').style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeAdditionalDispatchModal() {
  document.getElementById('additional-dispatch-modal').style.display = 'none';
  document.body.style.overflow = 'auto';
}

function openDispatchPrintModal() {
  document.getElementById('dispatch-print-modal').style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeDispatchPrintModal() {
  document.getElementById('dispatch-print-modal').style.display = 'none';
  document.body.style.overflow = 'auto';
}

function printDispatchNote(type) {
  const lastSession = @json($lastSession ?? null);
  if (!lastSession) {
    alert('No dispatch session found');
    return;
  }

  const url = '{{ route("events.packing-list.dispatch", [$event, ":session"]) }}?type=' + type;
  const finalUrl = url.replace(':session', lastSession.id);
  const filename = type === 'full'
    ? 'DISPATCH NOTE FOR {{ strtoupper($event->name) }}.pdf'
    : 'ADDITIONAL DISPATCH NOTE FOR {{ strtoupper($event->name) }}.pdf';

  closeDispatchPrintModal();

  // Trigger download
  window.location.href = finalUrl;
}

function handleAdditionalDispatch(e) {
  e.preventDefault();

  const checkedItems = document.querySelectorAll('.additional-item-checkbox:checked');
  if (checkedItems.length === 0) {
    alert('Please select at least one item to dispatch');
    return;
  }

  // Store selected items
  const itemIds = Array.from(checkedItems).map(cb => cb.value);
  sessionStorage.setItem('additional_dispatch_items', JSON.stringify(itemIds));
  sessionStorage.setItem('is_additional_dispatch', 'true');

  // Close additional dispatch modal
  closeAdditionalDispatchModal();

  // Open main dispatch modal
  openDispatchModal();
}

function openReceiveModal() {
  document.getElementById('receive-modal').style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeReceiveModal() {
  document.getElementById('receive-modal').style.display = 'none';
  document.body.style.overflow = 'auto';
}

function togglePieces(id) {
  const row = document.getElementById(id);
  if (row.style.display === 'none') {
    row.style.display = 'table-row';
  } else {
    row.style.display = 'none';
  }
}

// Close modal on background click
document.getElementById('edit-modal')?.addEventListener('click', function(e) {
  if (e.target === this) closeEditModal();
});

document.getElementById('receive-modal')?.addEventListener('click', function(e) {
  if (e.target === this) closeReceiveModal();
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape' && document.getElementById('edit-modal').style.display === 'flex') {
    closeEditModal();
  }
  if (e.key === 'Escape' && document.getElementById('receive-modal').style.display === 'flex') {
    closeReceiveModal();
  }
});
</script>

@endsection