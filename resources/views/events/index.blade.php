@extends('layouts.app')
@section('title', 'Events')
@section('page-title', 'Events')

@section('content')

@php
  $currentStatus = request('status', '');
  $currentSearch = request('search', '');
  $statuses = [
    ''          => 'All',
    'Draft'     => 'Draft',
    'Awaiting Payment' => 'Awaiting',
    'Scheduled' => 'Scheduled',
    'Active'    => 'Active',
    'Set Down'  => 'Set Down',
    'Completed' => 'Completed',
    'Cancelled' => 'Cancelled',
  ];
  $statusColors = [
    'Draft'     => 'ev-s-draft',
    'Awaiting Payment' => 'ev-s-draft',
    'Scheduled' => 'ev-s-scheduled',
    'Active'    => 'ev-s-active',
    'Set Down'  => 'ev-s-setdown',
    'Completed' => 'ev-s-completed',
    'Cancelled' => 'ev-s-cancelled',
  ];
@endphp

{{-- PAGE HEADER --}}
<div class="ev-list-header">
  <div class="ev-list-header-left">
    <h1 class="ev-list-title">Events</h1>
    <p class="ev-list-sub">
      {{ $events->total() }} total events &middot; Grey Apple Events Limited
    </p>
  </div>
  <div class="ev-list-header-right">
    <a href="{{ route('events.create') }}" class="ev-btn-primary">
      <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><line x1="8" y1="5" x2="8" y2="11"/><line x1="5" y1="8" x2="11" y2="8"/></svg>
      Create Event
    </a>
  </div>
</div>

{{-- FLASH MESSAGES --}}
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

{{-- FILTER BAR --}}
<form method="GET" action="{{ route('events.index') }}">
  <div class="ev-filter-bar">
    <div class="ev-search-box">
      <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="#b0a8a0" stroke-width="1.5" stroke-linecap="round"><circle cx="7" cy="7" r="4.5"/><line x1="10.5" y1="10.5" x2="14" y2="14"/></svg>
      <input type="text" name="search" value="{{ $currentSearch }}"
             placeholder="Search by event name or client..."
             class="ev-search-input" onchange="this.form.submit()">
    </div>
    <select name="status" class="ev-filter-select" onchange="this.form.submit()">
      <option value="">All Statuses</option>
      @foreach(['Draft','Scheduled','Active','Set Down','Completed','Cancelled'] as $s)
        <option value="{{ $s }}" {{ $currentStatus === $s ? 'selected' : '' }}>{{ $s }}</option>
      @endforeach
    </select>
    <select name="sort" class="ev-filter-select" onchange="this.form.submit()">
      <option value="event_date_desc" {{ request('sort')==='event_date_desc' ? 'selected':'' }}>Sort: Event Date ↓</option>
      <option value="event_date_asc"  {{ request('sort')==='event_date_asc'  ? 'selected':'' }}>Sort: Event Date ↑</option>
      <option value="name_asc"        {{ request('sort')==='name_asc'        ? 'selected':'' }}>Sort: Name A–Z</option>
      <option value="created_desc"    {{ request('sort')==='created_desc'    ? 'selected':'' }}>Sort: Recently Created</option>
    </select>
    @if($currentSearch || $currentStatus)
      <a href="{{ route('events.index') }}" class="ev-clear-btn">Clear filters</a>
    @endif
  </div>

  {{-- STATUS TABS --}}
  <div class="ev-status-tabs">
    @foreach($statuses as $val => $label)
      <a href="{{ request()->fullUrlWithQuery(['status' => $val, 'page' => 1]) }}"
         class="ev-stab {{ $currentStatus === $val ? 'active' : '' }}">
        {{ $label }}
        <span class="ev-stab-count">
          @if($val === '')
            {{ $events->total() }}
          @else
            {{ $statusCounts[$val] ?? 0 }}
          @endif
        </span>
      </a>
    @endforeach
  </div>
</form>

{{-- EVENTS TABLE --}}
<div class="ev-table-wrap">
  <table class="ev-table">
    <colgroup>
      <col style="width:26%">
      <col style="width:12%">
      <col style="width:13%">
      <col style="width:11%">
      <col style="width:11%">
      <col style="width:11%">
      <col style="width:8%">
      <col style="width:8%">
    </colgroup>
    <thead>
      <tr>
        <th>Event</th>
        <th>Status</th>
        <th>Venue</th>
        <th>Loading Date</th>
        <th>Event Date</th>
        <th>Set-down Date</th>
        <th>Items</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($events as $event)
      @php $colorClass = $statusColors[$event->status] ?? 'ev-s-draft'; @endphp
      <tr class="ev-row" onclick="window.location='{{ route('events.show', $event->id) }}'">

        <td>
          <div class="ev-td-inner">
            <div class="ev-event-icon">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="3" width="12" height="11" rx="2"/><line x1="5" y1="1" x2="5" y2="5"/><line x1="11" y1="1" x2="11" y2="5"/><line x1="2" y1="7" x2="14" y2="7"/></svg>
            </div>
            <div class="ev-name-col">
              <span class="ev-event-name">{{ $event->name }}</span>
              <span class="ev-client-name">{{ $event->client_name }}</span>
            </div>
          </div>
        </td>

        <td>
          <div class="ev-td-inner">
            <span class="ev-status-pill {{ $colorClass }}">
              {{ $event->status }}
            </span>
            @if($event->link_type === 'site-to-site')
              <span class="ev-status-pill" style="background:#f0f7ff;color:#185FA5;border-color:#dae8f7;font-size:9px;padding:2px 6px;margin-left:4px" title="Site-to-Site Linked Event">
                <svg width="9" height="9" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" style="display:inline-block;vertical-align:middle;margin-right:2px">
                  <path d="M2 8h5M9 8h5M7 3l-5 5 5 5M9 3l5 5-5 5"/>
                </svg>
                S2S
              </span>
            @endif
          </div>
        </td>

        <td>
          <div class="ev-td-inner">
            <span class="ev-venue-text">{{ Str::limit($event->venue, 22) }}</span>
          </div>
        </td>

        <td>
          <div class="ev-td-inner">
            <span class="ev-date-text">{{ $event->loading_date->format('d M Y') }}</span>
          </div>
        </td>

        <td>
          <div class="ev-td-inner">
            <span class="ev-date-text ev-date-main">{{ $event->event_date->format('d M Y') }}</span>
          </div>
        </td>

        <td>
          <div class="ev-td-inner">
            <span class="ev-date-text">{{ $event->setdown_date->format('d M Y') }}</span>
          </div>
        </td>

        <td>
          <div class="ev-td-inner">
            <span class="ev-item-count">
              {{ $event->event_items_count ?? 0 }}
              <span class="ev-item-count-label">items</span>
            </span>
          </div>
        </td>

        <td onclick="event.stopPropagation()">
          <div class="ev-td-inner">
            <div class="ev-row-actions">
              <a href="{{ route('events.show', $event->id) }}"
                 class="ev-ra-btn" title="View event">
                <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="3"/><path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z"/></svg>
              </a>
              <a href="{{ route('events.edit', $event->id) }}"
                 class="ev-ra-btn" title="Edit event">
                <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M1 12l3-3 8-8 3 3-8 8-3 1z"/></svg>
              </a>
              <form method="POST" action="{{ route('events.destroy', $event->id) }}"
                    onsubmit="return confirm('⚠️ DELETE EVENT: {{ addslashes($event->name) }}?\n\nThis will:\n• Delete the event permanently\n• Return all {{ $event->event_items_count ?? 0 }} items to warehouse\n• Free up trapped items\n\nThis action cannot be undone. Continue?')">
                @csrf @method('DELETE')
                <button type="submit" class="ev-ra-btn ev-ra-danger" title="Delete event and return all items to warehouse">
                  <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M3 4h10M5 4V3h6v1M6 7v5M10 7v5"/><rect x="2" y="4" width="12" height="10" rx="1.5"/></svg>
                </button>
              </form>
            </div>
          </div>
        </td>

      </tr>
      @empty
      <tr>
        <td colspan="8" class="ev-empty-cell">
          <div class="ev-empty-state">
            <svg width="36" height="36" viewBox="0 0 16 16" fill="none" stroke="#d0c8c0" stroke-width="0.8" stroke-linecap="round"><rect x="2" y="3" width="12" height="11" rx="2"/><line x1="5" y1="1" x2="5" y2="5"/><line x1="11" y1="1" x2="11" y2="5"/><line x1="2" y1="7" x2="14" y2="7"/></svg>
            <p>No events found</p>
            <span>
              @if($currentSearch || $currentStatus)
                Try adjusting your filters or
                <a href="{{ route('events.index') }}" style="color:#CC0000">clear all filters</a>
              @else
                <a href="{{ route('events.create') }}" style="color:#CC0000">Create your first event</a>
              @endif
            </span>
          </div>
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- PAGINATION --}}
@if($events->hasPages())
<div class="ev-pagination">
  <span class="ev-pg-info">
    Showing {{ $events->firstItem() }}–{{ $events->lastItem() }} of {{ $events->total() }} events
  </span>
  <div class="ev-pg-links">
    @if($events->onFirstPage())
      <span class="ev-pg-btn ev-pg-disabled">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M7.5 2L4.5 6l3 4"/></svg>
      </span>
    @else
      <a href="{{ $events->previousPageUrl() }}" class="ev-pg-btn">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M7.5 2L4.5 6l3 4"/></svg>
      </a>
    @endif
    @foreach($events->getUrlRange(max(1,$events->currentPage()-2), min($events->lastPage(),$events->currentPage()+2)) as $page => $url)
      <a href="{{ $url }}" class="ev-pg-btn {{ $page == $events->currentPage() ? 'active' : '' }}">{{ $page }}</a>
    @endforeach
    @if($events->hasMorePages())
      <a href="{{ $events->nextPageUrl() }}" class="ev-pg-btn">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4.5 2L7.5 6l-3 4"/></svg>
      </a>
    @else
      <span class="ev-pg-btn ev-pg-disabled">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4.5 2L7.5 6l-3 4"/></svg>
      </span>
    @endif
  </div>
</div>
@endif

@endsection
