@extends('layouts.app')
@section('title', 'Review & Confirm — ' . $event->name)
@section('page-title', 'Events')

@section('content')

{{-- PAGE HEADER --}}
<div class="wiz-page-header">
  <div>
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
      <a href="{{ route('events.index') }}" class="wiz-back-link">Events</a>
      <span style="color:#d0c8c0;font-size:12px">/</span>
      <span style="font-size:12px;color:#5c5550">{{ $event->name }}</span>
      <span style="color:#d0c8c0;font-size:12px">/</span>
      <span style="font-size:12px;color:#5c5550;font-weight:500">Review & Confirm</span>
    </div>
    <h1 class="wiz-page-title">Review Event & Confirm Schedule</h1>
  </div>
</div>

{{-- STEP INDICATOR --}}
<div class="wiz-stepper">
  <div class="wiz-step wiz-step-done">
    <div class="wiz-step-num wiz-step-num-done">
      <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 6l2.5 2.5 5.5-5"/></svg>
    </div>
    <div class="wiz-step-info">
      <span class="wiz-step-label" style="color:#3B6D11">Event Details</span>
      <span class="wiz-step-sub">Completed</span>
    </div>
  </div>
  <div class="wiz-step-line wiz-step-line-done"></div>
  <div class="wiz-step wiz-step-done">
    <div class="wiz-step-num wiz-step-num-done">
      <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 6l2.5 2.5 5.5-5"/></svg>
    </div>
    <div class="wiz-step-info">
      <span class="wiz-step-label" style="color:#3B6D11">Packing List</span>
      <span class="wiz-step-sub">Completed</span>
    </div>
  </div>
  <div class="wiz-step-line wiz-step-line-done"></div>
  <div class="wiz-step wiz-step-done">
    <div class="wiz-step-num wiz-step-num-done">
      <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 6l2.5 2.5 5.5-5"/></svg>
    </div>
    <div class="wiz-step-info">
      <span class="wiz-step-label" style="color:#3B6D11">Assign Team</span>
      <span class="wiz-step-sub">Completed</span>
    </div>
  </div>
  <div class="wiz-step-line wiz-step-line-done"></div>
  <div class="wiz-step wiz-step-active">
    <div class="wiz-step-num">4</div>
    <div class="wiz-step-info">
      <span class="wiz-step-label">Review & Confirm</span>
      <span class="wiz-step-sub">Summary & schedule</span>
    </div>
  </div>
</div>

<div class="wiz-layout">

  {{-- SIDEBAR --}}
  <div class="wiz-sidebar">
    <div class="wiz-sidebar-card">
      <div class="wiz-sidebar-title">Event</div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">Name</span>
        <span class="wiz-sum-val">{{ $event->name }}</span>
      </div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">Client</span>
        <span class="wiz-sum-val">{{ $event->client_name }}</span>
      </div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">Event date</span>
        <span class="wiz-sum-val">{{ $event->event_date->format('d M Y') }}</span>
      </div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">Venue</span>
        <span class="wiz-sum-val">{{ $event->venue }}</span>
      </div>
    </div>
  </div>

  {{-- MAIN --}}
  <div class="wiz-main">

    <div class="wiz-card">
      <div class="wiz-card-head">
        <div class="wiz-card-title">Review Event Details</div>
        <div class="wiz-card-sub">Review all details before scheduling this event. Once confirmed, the event will be marked as Scheduled.</div>
      </div>
      <div class="wiz-card-body">

        {{-- SECTION 1: EVENT SUMMARY --}}
        <div style="margin-bottom:24px">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
            <h3 style="font-size:13px;font-weight:700;color:#0f0f0f;margin:0">Event Summary</h3>
            <a href="{{ route('events.edit', $event) }}" style="font-size:11px;color:#CC0000;text-decoration:none">Edit</a>
          </div>
          <div class="wiz-review-grid">
            <div>
              <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#a09890;margin-bottom:4px">Event Name</div>
              <div style="font-size:12px;color:#0f0f0f">{{ $event->name }}</div>
            </div>
            <div>
              <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#a09890;margin-bottom:4px">Client</div>
              <div style="font-size:12px;color:#0f0f0f">{{ $event->client_name }}</div>
            </div>
            <div>
              <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#a09890;margin-bottom:4px">Venue</div>
              <div style="font-size:12px;color:#0f0f0f">{{ $event->venue }}</div>
            </div>
            <div>
              <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#a09890;margin-bottom:4px">Loading Date</div>
              <div style="font-size:12px;color:#0f0f0f">{{ $event->loading_date->format('d M Y') }}</div>
            </div>
            <div>
              <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#a09890;margin-bottom:4px">Event Date</div>
              <div style="font-size:12px;color:#0f0f0f">{{ $event->event_date->format('d M Y') }}</div>
            </div>
            <div>
              <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#a09890;margin-bottom:4px">Set-down Date</div>
              <div style="font-size:12px;color:#0f0f0f">{{ $event->setdown_date->format('d M Y') }}</div>
            </div>
            @if($event->location_name)
            <div>
              <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#a09890;margin-bottom:4px">Location</div>
              <div style="font-size:12px;color:#0f0f0f">{{ $event->location_name }}</div>
            </div>
            @endif
            @if($event->cost)
            <div>
              <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#a09890;margin-bottom:4px">Cost</div>
              <div style="font-size:12px;color:#0f0f0f">KES {{ number_format($event->cost, 2) }}</div>
            </div>
            @endif
          </div>
          @if($event->notes)
          <div style="margin-top:12px">
            <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#a09890;margin-bottom:4px">Notes</div>
            <div style="font-size:12px;color:#5c5550">{{ $event->notes }}</div>
          </div>
          @endif
        </div>

        <div style="border-top:1px solid #f5f1ed;margin:24px 0"></div>

        {{-- SECTION 2: PACKING LIST SUMMARY --}}
        <div style="margin-bottom:24px">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
            <h3 style="font-size:13px;font-weight:700;color:#0f0f0f;margin:0">Packing List Summary</h3>
            <a href="{{ route('events.checklist', $event) }}" style="font-size:11px;color:#CC0000;text-decoration:none">Edit</a>
          </div>

          <div class="wiz-review-subcards">
            <div class="wiz-review-subcard">
              <div style="font-size:20px;margin-bottom:6px">📦</div>
              <div style="font-size:11px;font-weight:600;color:#0f0f0f;margin-bottom:4px">Own Items</div>
              <div style="font-size:13px;font-weight:700;color:#CC0000">{{ $totalOwnItems }} item{{ $totalOwnItems === 1 ? '' : ' types' }}</div>
              <div style="font-size:10px;color:#a09890">{{ $event->eventItems->sum('quantity_requested') }} total pieces</div>
            </div>
            <div class="wiz-review-subcard">
              <div style="font-size:20px;margin-bottom:6px">🔄</div>
              <div style="font-size:11px;font-weight:600;color:#0f0f0f;margin-bottom:4px">Borrowed Items</div>
              <div style="font-size:13px;font-weight:700;color:#CC0000">{{ $totalBorrowedItems }} item{{ $totalBorrowedItems === 1 ? '' : 's' }}</div>
              @if($totalBorrowedItems > 0)
              <div style="font-size:10px;color:#a09890">from {{ $event->borrowedItems->whereNotNull('source_company')->pluck('source_company')->unique()->count() }} source{{ $event->borrowedItems->whereNotNull('source_company')->pluck('source_company')->unique()->count() === 1 ? '' : 's' }}</div>
              @endif
            </div>
            <div class="wiz-review-subcard">
              <div style="font-size:20px;margin-bottom:6px">🔧</div>
              <div style="font-size:11px;font-weight:600;color:#0f0f0f;margin-bottom:4px">Operational Items</div>
              <div style="font-size:13px;font-weight:700;color:#CC0000">{{ $totalOperationalItems }} item{{ $totalOperationalItems === 1 ? '' : 's' }}</div>
            </div>
          </div>

          {{-- EXPANDABLE FULL PACKING LIST --}}
          <div style="margin-top:16px">
            <button type="button" onclick="togglePackingPreview()" style="background:none;border:none;color:#CC0000;font-size:11px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px">
              <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" id="preview-arrow">
                <path d="M6 4l4 4-4 4"/>
              </svg>
              <span id="preview-text">View Full Packing List</span>
            </button>

            <div class="wiz-packing-preview" id="packing-preview" style="margin-top:12px;padding:16px;background:#faf8f6;border:1px solid #f0ece8;border-radius:8px">
              @if($event->eventItems->count() > 0)
              <div style="margin-bottom:16px">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#5c5550;margin-bottom:10px">Own Inventory</div>
                @php $itemsByCategory = $event->eventItems->load('item')->groupBy('item.category'); @endphp
                @foreach($itemsByCategory as $category => $items)
                <div style="margin-bottom:12px">
                  <div style="font-size:10px;font-weight:600;color:#a09890;margin-bottom:6px">{{ $category }}</div>
                  @foreach($items as $eventItem)
                  <div style="display:flex;justify-content:space-between;font-size:11px;color:#3a3530;padding:4px 0">
                    <span>{{ $eventItem->item->name }}</span>
                    <span style="font-weight:600">{{ $eventItem->quantity_requested }} pc{{ $eventItem->quantity_requested === 1 ? '' : 's' }}</span>
                  </div>
                  @endforeach
                </div>
                @endforeach
              </div>
              @endif

              @if($event->borrowedItems->count() > 0)
              <div style="margin-bottom:16px">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#5c5550;margin-bottom:10px">Borrowed Items</div>
                @foreach($event->borrowedItems as $borrowed)
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:11px;color:#3a3530;padding:4px 0">
                  <div>
                    <div style="font-weight:600">{{ $borrowed->item_name }}</div>
                    @if($borrowed->source_company)
                    <div style="font-size:10px;color:#a09890">from {{ $borrowed->source_company }}</div>
                    @endif
                  </div>
                  <span style="font-weight:600">{{ $borrowed->quantity_dispatched }}</span>
                </div>
                @endforeach
              </div>
              @endif

              @if($event->operationalItems->count() > 0)
              <div>
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#5c5550;margin-bottom:10px">Operational Items</div>
                @foreach($event->operationalItems as $op)
                <div style="display:flex;justify-content:space-between;font-size:11px;color:#3a3530;padding:4px 0">
                  <span>{{ $op->display_name }}</span>
                  <span style="font-weight:600">{{ $op->quantity_dispatched }}</span>
                </div>
                @endforeach
              </div>
              @endif
            </div>
          </div>

        </div>

        <div style="border-top:1px solid #f5f1ed;margin:24px 0"></div>

        {{-- SECTION 3: TEAM --}}
        <div style="margin-bottom:24px">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
            <h3 style="font-size:13px;font-weight:700;color:#0f0f0f;margin:0">Team</h3>
            <a href="{{ route('events.team', $event) }}" style="font-size:11px;color:#CC0000;text-decoration:none">Edit</a>
          </div>

          @if($event->staff->count() > 0)
          <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px">
            @foreach($event->staff as $member)
            <div style="display:flex;align-items:center;gap:8px;padding:8px;background:#faf8f6;border:1px solid #f0ece8;border-radius:6px">
              <div style="width:32px;height:32px;border-radius:50%;background:#CC0000;color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700">
                {{ strtoupper(substr($member->name, 0, 1)) }}
              </div>
              <div style="flex:1">
                <div style="font-size:12px;font-weight:600;color:#0f0f0f">{{ $member->name }}</div>
                <div style="font-size:10px;color:#a09890">{{ $member->pivot->role === 'leader' ? '⭐ Team Leader' : 'Team Member' }}</div>
              </div>
            </div>
            @endforeach
          </div>
          @else
          <div style="padding:12px;background:#faeeda;border:1px solid #f5e5c4;border-radius:6px">
            <div style="font-size:11px;color:#854F0B">No team members assigned yet. You can assign team after scheduling.</div>
          </div>
          @endif
        </div>

        <div style="border-top:1px solid #f5f1ed;margin:24px 0"></div>

        {{-- SECTION 4: PLAN REFERENCE --}}
        <div style="margin-bottom:24px">
          <h3 style="font-size:13px;font-weight:700;color:#0f0f0f;margin:0 0 12px">Plan Reference</h3>
          <div style="padding:16px;background:#faf8f6;border:1px solid #f0ece8;border-radius:8px">
            <div style="font-size:20px;font-weight:700;color:#CC0000;margin-bottom:6px">{{ $planRef }}</div>
            <div style="font-size:11px;color:#5c5550;margin-bottom:12px">This reference will be used on both planning and final packing lists.</div>
            <a href="{{ route('events.packing-list.planning', $event) }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border:1px solid #CC0000;color:#CC0000;background:#fff;border-radius:6px;font-size:11px;font-weight:600;text-decoration:none">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
              </svg>
              Download Planning Packing List (Draft)
            </a>
          </div>
        </div>

      </div>

      {{-- CARD FOOTER --}}
      <div class="wiz-card-footer">
        <span class="wiz-footer-hint">Step 4 of 4 &mdash; Confirm to schedule this event</span>
        <div class="wiz-footer-actions">
          <a href="{{ route('events.team', $event) }}" class="wiz-btn-cancel">← Back</a>
          <form method="POST" action="{{ route('events.confirm', $event) }}" style="display:inline" id="confirm-form">
            @csrf
            <button type="submit" class="wiz-btn-next" id="confirm-btn">
              Confirm & Schedule
              <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6"/><path d="M6 8l2 2 4-4"/></svg>
            </button>
          </form>
        </div>
      </div>

    </div>

  </div>

</div>

<script>
function togglePackingPreview() {
  const preview = document.getElementById('packing-preview');
  const arrow = document.getElementById('preview-arrow');
  const text = document.getElementById('preview-text');

  if (preview.classList.contains('open')) {
    preview.classList.remove('open');
    arrow.style.transform = 'rotate(0deg)';
    text.textContent = 'View Full Packing List';
  } else {
    preview.classList.add('open');
    arrow.style.transform = 'rotate(90deg)';
    text.textContent = 'Hide Full Packing List';
  }
}

document.getElementById('confirm-form').addEventListener('submit', function() {
  const btn = document.getElementById('confirm-btn');
  btn.disabled = true;
  btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="animation:wizSpin 0.8s linear infinite"><path d="M8 1.5a6.5 6.5 0 1 1-4.6 1.9"/></svg> Scheduling...';
});
</script>

@endsection
