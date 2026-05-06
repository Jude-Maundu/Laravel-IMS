@extends('layouts.app')
@section('title', 'Event Requests')
@section('page-title', 'Events')

@section('content')

<div class="ev-list-header">
  <div>
    <h1 class="ev-list-title">Event Requests</h1>
    <p class="ev-list-sub">{{ $requests->total() }} total requests pending review</p>
  </div>
  <div class="ev-list-header-right">
    <a href="{{ route('events.index') }}" class="evsh-btn-outline">
      <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="3" width="12" height="11" rx="2"/></svg>
      All Events
    </a>
    <a href="{{ route('events.create') }}" class="ev-btn-primary">
      <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><line x1="8" y1="5" x2="8" y2="11"/><line x1="5" y1="8" x2="11" y2="8"/></svg>
      Create Event
    </a>
  </div>
</div>

@if(session('success'))
  <div class="ev-flash ev-flash-success">
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 8l2 2 5-5"/><circle cx="8" cy="8" r="6.5"/></svg>
    {{ session('success') }}
  </div>
@endif

<div class="ev-table-wrap">
  <table class="ev-table">
    <colgroup>
      <col style="width:28%">
      <col style="width:13%">
      <col style="width:12%">
      <col style="width:12%">
      <col style="width:12%">
      <col style="width:13%">
      <col style="width:10%">
    </colgroup>
    <thead>
      <tr>
        <th>Event Name</th>
        <th>Status</th>
        <th>Setup Date</th>
        <th>Event Date</th>
        <th>Set-down Date</th>
        <th>Requested</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($requests as $event)
      @php
        $colorClass = match($event->status) {
          'Scheduled' => 'ev-s-scheduled',
          'Active'    => 'ev-s-active',
          'Cancelled' => 'ev-s-cancelled',
          default     => 'ev-s-draft',
        };
      @endphp
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
        <td><div class="ev-td-inner"><span class="ev-status-pill {{ $colorClass }}">{{ $event->status }}</span></div></td>
        <td><div class="ev-td-inner"><span class="ev-date-text">{{ $event->setup_date->format('d M Y') }}</span></div></td>
        <td><div class="ev-td-inner"><span class="ev-date-text ev-date-main">{{ $event->event_date->format('d M Y') }}</span></div></td>
        <td><div class="ev-td-inner"><span class="ev-date-text">{{ $event->setdown_date->format('d M Y') }}</span></div></td>
        <td><div class="ev-td-inner"><span class="ev-date-text">{{ $event->created_at->diffForHumans() }}</span></div></td>
        <td onclick="event.stopPropagation()">
          <div class="ev-td-inner">
            <div class="ev-row-actions" style="opacity:1">
              @if($event->status === 'Draft')
              <form method="POST" action="{{ route('events.update', $event) }}" style="display:inline">
                @csrf @method('PUT')
                <input type="hidden" name="name"         value="{{ $event->name }}">
                <input type="hidden" name="client_name"  value="{{ $event->client_name }}">
                <input type="hidden" name="venue"        value="{{ $event->venue }}">
                <input type="hidden" name="loading_date" value="{{ $event->loading_date->format('Y-m-d') }}">
                <input type="hidden" name="setup_date"   value="{{ $event->setup_date->format('Y-m-d') }}">
                <input type="hidden" name="event_date"   value="{{ $event->event_date->format('Y-m-d') }}">
                <input type="hidden" name="setdown_date" value="{{ $event->setdown_date->format('Y-m-d') }}">
                <input type="hidden" name="status"       value="Scheduled">
                <button type="submit" style="background:#eaf3de;color:#3B6D11;border:1px solid #c0dd97;padding:3px 10px;border-radius:6px;font-size:10px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif">
                  Approve
                </button>
              </form>
              <form method="POST" action="{{ route('events.update', $event) }}" style="display:inline">
                @csrf @method('PUT')
                <input type="hidden" name="name"         value="{{ $event->name }}">
                <input type="hidden" name="client_name"  value="{{ $event->client_name }}">
                <input type="hidden" name="venue"        value="{{ $event->venue }}">
                <input type="hidden" name="loading_date" value="{{ $event->loading_date->format('Y-m-d') }}">
                <input type="hidden" name="setup_date"   value="{{ $event->setup_date->format('Y-m-d') }}">
                <input type="hidden" name="event_date"   value="{{ $event->event_date->format('Y-m-d') }}">
                <input type="hidden" name="setdown_date" value="{{ $event->setdown_date->format('Y-m-d') }}">
                <input type="hidden" name="status"       value="Cancelled">
                <button type="submit" style="background:#fff0f0;color:#CC0000;border:1px solid #f5c0c0;padding:3px 10px;border-radius:6px;font-size:10px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif">
                  Decline
                </button>
              </form>
              @else
              <a href="{{ route('events.show', $event->id) }}" class="ev-ra-btn">
                <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="3"/><path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z"/></svg>
              </a>
              @endif
            </div>
          </div>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="7" class="ev-empty-cell">
          <div class="ev-empty-state">
            <svg width="36" height="36" viewBox="0 0 16 16" fill="none" stroke="#d0c8c0" stroke-width="0.8" stroke-linecap="round"><rect x="2" y="3" width="12" height="11" rx="2"/><line x1="5" y1="1" x2="5" y2="5"/><line x1="11" y1="1" x2="11" y2="5"/></svg>
            <p>No event requests</p>
            <span>All requests have been processed</span>
          </div>
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

@if($requests->hasPages())
<div class="ev-pagination">
  <span class="ev-pg-info">Showing {{ $requests->firstItem() }}–{{ $requests->lastItem() }} of {{ $requests->total() }}</span>
  <div class="ev-pg-links">
    @if(!$requests->onFirstPage())
      <a href="{{ $requests->previousPageUrl() }}" class="ev-pg-btn">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M7.5 2L4.5 6l3 4"/></svg>
      </a>
    @endif
    @foreach($requests->getUrlRange(max(1,$requests->currentPage()-2), min($requests->lastPage(),$requests->currentPage()+2)) as $page => $url)
      <a href="{{ $url }}" class="ev-pg-btn {{ $page == $requests->currentPage() ? 'active' : '' }}">{{ $page }}</a>
    @endforeach
    @if($requests->hasMorePages())
      <a href="{{ $requests->nextPageUrl() }}" class="ev-pg-btn">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4.5 2L7.5 6l-3 4"/></svg>
      </a>
    @endif
  </div>
</div>
@endif

@endsection
