@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

@php
  $nameParts = explode(' ', trim(auth()->user()->name ?? 'Admin'));
  $firstName = $nameParts[0];
  $hour = (int) date('H');
  $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
  $healthPct = $totalItems > 0 ? round(($available / $totalItems) * 100) : 0;
  $damagedPct = $totalItems > 0 ? round(($damaged / $totalItems) * 100) : 0;
  $deployedPct = $totalItems > 0 ? round(($deployed / $totalItems) * 100) : 0;
  $repairPct = $totalItems > 0 ? round(($underRepair / $totalItems) * 100) : 0;
@endphp

{{-- WELCOME ROW --}}
<div class="db-welcome-row">
  <div class="db-welcome-left">
    <h1 class="db-welcome-title">{{ $greeting }}, {{ $firstName }}</h1>
    <p class="db-welcome-sub">{{ now()->format('l j F Y') }} &middot; Grey Apple Events Limited</p>
  </div>
  <div class="db-welcome-right">
    <a href="{{ route('reports.index') }}" class="db-btn-secondary">Export Report</a>
    @if(Route::has('events.create'))
      <a href="{{ route('events.create') }}" class="db-btn-primary">+ Create Event</a>
    @endif
  </div>
</div>

{{-- ALERT STRIP --}}
@if($damaged > 0 || $underRepair > 0)
<div class="db-alert-strip">
  <div class="db-alert-dot"></div>
  <div class="db-alert-text">
    @if($damaged > 0)
      {{ $damaged }} item{{ $damaged > 1 ? 's' : '' }} flagged as damaged.
    @endif
    @if($underRepair > 0)
      {{ $underRepair }} item{{ $underRepair > 1 ? 's' : '' }} currently under repair.
    @endif
  </div>
  <a href="{{ route('repairs.index') }}" class="db-alert-link">Review now &rarr;</a>
</div>
@endif

{{-- KPI CARDS --}}
<div class="db-kpi-row">
  <div class="db-kpi db-kpi-neutral">
    <div class="db-kpi-top">
      <div class="db-kpi-icon db-icon-green"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="2" width="12" height="12" rx="2"/><line x1="5" y1="6" x2="11" y2="6"/><line x1="5" y1="9" x2="9" y2="9"/></svg></div>
      <span class="db-kpi-badge db-badge-neutral">Total</span>
    </div>
    <div class="db-kpi-value">{{ $totalItems }}</div>
    <div class="db-kpi-label">Total Items</div>
  </div>

  <div class="db-kpi db-kpi-green">
    <div class="db-kpi-top">
      <div class="db-kpi-icon db-icon-green"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 8l2 2 5-5"/><rect x="2" y="2" width="12" height="12" rx="2"/></svg></div>
      <span class="db-kpi-badge db-badge-green">{{ $healthPct }}%</span>
    </div>
    <div class="db-kpi-value db-val-green">{{ $available }}</div>
    <div class="db-kpi-label">Available</div>
  </div>

  <div class="db-kpi db-kpi-blue">
    <div class="db-kpi-top">
      <div class="db-kpi-icon db-icon-blue"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M2 13V5l6-3 6 3v8"/><rect x="6" y="9" width="4" height="4"/></svg></div>
      <span class="db-kpi-badge db-badge-blue">{{ $deployedPct }}%</span>
    </div>
    <div class="db-kpi-value db-val-blue">{{ $deployed }}</div>
    <div class="db-kpi-label">Deployed</div>
  </div>

  <div class="db-kpi db-kpi-red">
    <div class="db-kpi-top">
      <div class="db-kpi-icon db-icon-red"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M8 6v3M8 11v1"/><path d="M3 13L8 3l5 10H3z"/></svg></div>
      <span class="db-kpi-badge db-badge-red">{{ $damagedPct }}%</span>
    </div>
    <div class="db-kpi-value db-val-red">{{ $damaged }}</div>
    <div class="db-kpi-label">Damaged</div>
  </div>

  <div class="db-kpi db-kpi-amber">
    <div class="db-kpi-top">
      <div class="db-kpi-icon db-icon-amber"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="3"/><path d="M8 1v2M8 13v2M1 8h2M13 8h2"/></svg></div>
      <span class="db-kpi-badge db-badge-amber">{{ $repairPct }}%</span>
    </div>
    <div class="db-kpi-value db-val-amber">{{ $underRepair }}</div>
    <div class="db-kpi-label">Repairing</div>
  </div>
</div>

{{-- BILLING KPI ROW --}}
<div class="db-kpi-row" style="margin-top: -8px; margin-bottom: 24px;">
  <div class="db-kpi db-kpi-neutral">
    <div class="db-kpi-top">
      <div class="db-kpi-icon" style="background:#faeeda; color:#854F0B"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 2v20M17 5H9.5a4.5 4.5 0 0 0 0 9h5a4.5 4.5 0 0 1 0 9H6"/></svg></div>
      <span class="db-kpi-badge db-badge-neutral">Billing</span>
    </div>
    <div class="db-kpi-value" style="color:#854F0B">{{ $pendingPayments }}</div>
    <div class="db-kpi-label">Pending Payments</div>
  </div>

  <div class="db-kpi db-kpi-neutral" style="grid-column: span 2;">
    <div class="db-kpi-top">
      <div class="db-kpi-icon" style="background:#fcebeb; color:#A32D2D"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8M12 18V6"/></svg></div>
      <span class="db-kpi-badge db-badge-red">Receivable</span>
    </div>
    <div class="db-kpi-value" style="color:#A32D2D">KES {{ number_format($totalPendingAmount, 0) }}</div>
    <div class="db-kpi-label">Total Outstanding</div>
  </div>

  <div class="db-kpi db-kpi-neutral" style="grid-column: span 2;">
    <div class="db-kpi-top">
      <div class="db-kpi-icon" style="background:#eaf3de; color:#3B6D11"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
      <span class="db-kpi-badge db-badge-green">Revenue</span>
    </div>
    <div class="db-kpi-value" style="color:#3B6D11">KES {{ number_format($totalRevenue, 0) }}</div>
    <div class="db-kpi-label">Total Collected</div>
  </div>
</div>

{{-- ANALYTICS GRID --}}
<div class="db-grid-2" style="display:grid; grid-template-columns: 1.2fr 2fr; gap: 20px; align-items: stretch; margin-bottom: 24px;">

  {{-- 1. Inventory Health (Enhanced UI) --}}
  <div class="db-card" style="display:flex; flex-direction:column;">
    <div class="db-card-head" style="border-bottom: 1px solid #f5f1ed; margin-bottom: 16px;">
      <div><div class="db-card-title">Inventory Health</div><div class="db-card-sub">Live item status breakdown & distribution</div></div>
    </div>
    <div class="db-card-body" style="flex:1; display:flex; align-items:center; padding: 0 24px 24px 24px;">
      @php
        $total = max($totalItems, 1);
        $circ = 2 * pi() * 60;
        $availDash   = round(($available   / $total) * $circ, 1);
        $deployDash  = round(($deployed    / $total) * $circ, 1);
        $clnDash     = round(($cleaning    / $total) * $circ, 1);
        $unservDash  = round((($damaged + $underRepair) / $total) * $circ, 1);
        
        $off1 = 0;
        $off2 = -$availDash;
        $off3 = -($availDash + $deployDash);
        $off4 = -($availDash + $deployDash + $clnDash);
      @endphp
      
      <div style="display:flex; flex-direction:row; align-items:center; justify-content:space-between; width: 100%; gap: 20px;">
        
        {{-- Advanced Donut Chart --}}
        <div style="position:relative; width:160px; height:160px; flex-shrink:0;">
          <svg width="160" height="160" viewBox="0 0 160 160" style="transform: rotate(-90deg); filter: drop-shadow(0px 4px 8px rgba(0,0,0,0.06));">
            <circle cx="80" cy="80" r="60" fill="none" stroke="#f5f1ed" stroke-width="16"/>
            <circle cx="80" cy="80" r="60" fill="none" stroke="#3B6D11" stroke-width="16" stroke-dasharray="{{ $availDash }} {{ $circ - $availDash }}" stroke-dashoffset="{{ $off1 }}" style="transition: stroke-dashoffset 1s ease-out;"/>
            <circle cx="80" cy="80" r="60" fill="none" stroke="#185FA5" stroke-width="16" stroke-dasharray="{{ $deployDash }} {{ $circ - $deployDash }}" stroke-dashoffset="{{ $off2 }}" style="transition: stroke-dashoffset 1s ease-out;"/>
            <circle cx="80" cy="80" r="60" fill="none" stroke="#854F0B" stroke-width="16" stroke-dasharray="{{ $clnDash }} {{ $circ - $clnDash }}" stroke-dashoffset="{{ $off3 }}" style="transition: stroke-dashoffset 1s ease-out;"/>
            <circle cx="80" cy="80" r="60" fill="none" stroke="#CC0000" stroke-width="16" stroke-dasharray="{{ $unservDash }} {{ $circ - $unservDash }}" stroke-dashoffset="{{ $off4 }}" style="transition: stroke-dashoffset 1s ease-out;"/>
          </svg>
          <div style="position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; transform: rotate(0deg)">
            <span style="font-size:28px; font-weight:800; color:#0f0f0f; line-height:1;">{{ $totalItems }}</span>
            <span style="font-size:10px; font-weight:700; color:#a09890; text-transform:uppercase; letter-spacing:0.05em; margin-top:4px;">Items</span>
          </div>
        </div>
        
        {{-- Advanced Legend --}}
        <div style="flex:1; display:flex; flex-direction:column; gap:8px;">
          
          <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 12px; background:#faf8f6; border-radius:8px; border:1px solid #f0ece8;">
            <div style="display:flex; align-items:center; gap:8px;">
              <div style="width:10px; height:10px; border-radius:50%; background:#3B6D11; box-shadow: 0 0 0 2px rgba(59, 109, 17, 0.2);"></div>
              <span style="font-size:12px; font-weight:600; color:#5c5550;">Available</span>
            </div>
            <div style="display:flex; align-items:baseline; gap:6px;">
              <span style="font-size:14px; font-weight:800; color:#0f0f0f;">{{ $available }}</span>
              <span style="font-size:10px; font-weight:600; color:#a09890;">({{ $totalItems > 0 ? round(($available/$totalItems)*100) : 0 }}%)</span>
            </div>
          </div>

          <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 12px; background:#faf8f6; border-radius:8px; border:1px solid #f0ece8;">
            <div style="display:flex; align-items:center; gap:8px;">
              <div style="width:10px; height:10px; border-radius:50%; background:#185FA5; box-shadow: 0 0 0 2px rgba(24, 95, 165, 0.2);"></div>
              <span style="font-size:12px; font-weight:600; color:#5c5550;">Deployed</span>
            </div>
            <div style="display:flex; align-items:baseline; gap:6px;">
              <span style="font-size:14px; font-weight:800; color:#0f0f0f;">{{ $deployed }}</span>
              <span style="font-size:10px; font-weight:600; color:#a09890;">({{ $totalItems > 0 ? round(($deployed/$totalItems)*100) : 0 }}%)</span>
            </div>
          </div>

          <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 12px; background:#faf8f6; border-radius:8px; border:1px solid #f0ece8;">
            <div style="display:flex; align-items:center; gap:8px;">
              <div style="width:10px; height:10px; border-radius:50%; background:#854F0B; box-shadow: 0 0 0 2px rgba(133, 79, 11, 0.2);"></div>
              <span style="font-size:12px; font-weight:600; color:#5c5550;">Cleaning</span>
            </div>
            <div style="display:flex; align-items:baseline; gap:6px;">
              <span style="font-size:14px; font-weight:800; color:#0f0f0f;">{{ $cleaning }}</span>
              <span style="font-size:10px; font-weight:600; color:#a09890;">({{ $totalItems > 0 ? round(($cleaning/$totalItems)*100) : 0 }}%)</span>
            </div>
          </div>

          <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 12px; background:#faf8f6; border-radius:8px; border:1px solid #f0ece8;">
            <div style="display:flex; align-items:center; gap:8px;">
              <div style="width:10px; height:10px; border-radius:50%; background:#CC0000; box-shadow: 0 0 0 2px rgba(204, 0, 0, 0.2);"></div>
              <span style="font-size:12px; font-weight:600; color:#5c5550;">Dam/Repair</span>
            </div>
            <div style="display:flex; align-items:baseline; gap:6px;">
              <span style="font-size:14px; font-weight:800; color:#0f0f0f;">{{ $damaged + $underRepair }}</span>
              <span style="font-size:10px; font-weight:600; color:#a09890;">({{ $totalItems > 0 ? round((($damaged + $underRepair)/$totalItems)*100) : 0 }}%)</span>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  {{-- 2. Item Locations (Dynamic Tracking) --}}
  <div class="db-card" style="display:flex; flex-direction:column;">
    <div class="db-card-head" style="flex-shrink:0; border-bottom:1px solid #f5f1ed; margin-bottom:12px;">
      <div><div class="db-card-title">Location Tracking</div><div class="db-card-sub">Real-time item distribution</div></div>
    </div>
    <div class="db-card-body custom-scroll" style="flex:1; overflow-y:auto; padding:0 20px 20px 20px;">
      <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:12px;">
        @foreach($locationStats as $loc)
        <a href="{{ $loc['url'] }}" style="text-decoration:none; display:block; transition:transform 0.1s ease, box-shadow 0.1s ease;" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='translateY(0)';">
          <div style="background:#faf8f6; border:1px solid #f0ece8; border-radius:10px; padding:12px; position:relative; overflow:hidden; height:100%;">
            {{-- Decorative side accent --}}
            <div style="position:absolute; left:0; top:0; bottom:0; width:4px; background:{{ $loc['color'] }};"></div>
            
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
              <div style="display:flex; align-items:center; gap:8px;">
                <div style="width:26px; height:26px; border-radius:6px; background:#fff; border:1px solid #ece8e3; display:flex; align-items:center; justify-content:center; color:{{ $loc['color'] }};">
                  {!! $loc['icon'] !!}
                </div>
                <div style="min-width:0;">
                  <div style="font-size:12px; font-weight:700; color:#0f0f0f; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:140px;" title="{{ $loc['name'] }}">{{ $loc['name'] }}</div>
                  <div style="font-size:9px; font-weight:600; color:#a09890; text-transform:uppercase; letter-spacing:0.04em;">{{ $loc['type'] }}</div>
                </div>
              </div>
              <div style="text-align:right;">
                <div style="font-size:14px; font-weight:800; color:#0f0f0f;">{{ $loc['count'] }}</div>
                <div style="font-size:9px; color:#b0a8a0; font-weight:500;">Items</div>
              </div>
            </div>
            
            <div style="height:4px; background:#ece8e3; border-radius:2px; overflow:hidden;">
              <div style="height:100%; width:{{ $totalItems > 0 ? ($loc['count']/$totalItems)*100 : 0 }}%; background:{{ $loc['color'] }}; border-radius:2px; transition:width 0.6s ease;"></div>
            </div>
          </div>
        </a>
        @endforeach
        
        @if($locationStats->isEmpty())
          <div style="padding:20px; text-align:center; color:#a09890; font-size:12px; font-style:italic; grid-column:1/-1;">No locations tracked</div>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- 3. Categories (Full Width Panel) --}}
<div class="db-card" style="margin-bottom: 24px;">
  <div class="db-card-head" style="border-bottom:1px solid #f5f1ed; margin-bottom:16px;">
    <div><div class="db-card-title">Category Distribution</div><div class="db-card-sub">Item distribution across categories</div></div>
  </div>
  <div class="db-card-body" style="padding:0 20px 20px 20px;">
    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap:24px;">
      @php
        $catColors = ['#CC0000','#185FA5','#854F0B','#534AB7','#3B6D11','#0F6E56','#E67E22','#9B59B6','#34495E','#16A085','#F39C12','#D35400'];
        $maxCat = $categoryStats->max('total') ?: 1;
      @endphp
      @foreach($categoryStats as $i => $cat)
        <a href="{{ route('inventory.index', ['category' => $cat->category]) }}" style="text-decoration:none; display:block; padding:12px; margin:-12px; border-radius:8px; transition:background 0.15s;" onmouseover="this.style.background='#faf8f6'" onmouseout="this.style.background='transparent'">
          <div style="display:flex; justify-content:space-between; margin-bottom:6px; align-items:center;">
            <span style="font-size:12px; font-weight:600; color:#3a3530;">{{ Str::limit($cat->category, 22) }}</span>
            <span style="font-size:12px; font-weight:700; color:#0f0f0f;">{{ $cat->total }} <span style="font-size:10px; color:#a09890; font-weight:500;">items</span></span>
          </div>
          <div style="height:6px; background:#f5f1ed; border-radius:3px; overflow:hidden;">
            <div style="height:100%; width:{{ round(($cat->total/$maxCat)*100) }}%; background:{{ $catColors[$i % count($catColors)] }}; border-radius:3px;"></div>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</div>

{{-- LOGISTICS HUB: EVENT MOVEMENT FLOW --}}
<div class="db-card" style="margin-bottom: 24px;">
  <div class="db-card-head">
    <div>
      <div class="db-card-title">Upcoming Events</div>
      <div class="db-card-sub">Active and scheduled events</div>
    </div>
    <a href="{{ route('events.index') }}" class="db-card-action">View all &rarr;</a>
  </div>
  <div class="db-card-body" style="padding: 20px 24px;">
    @if($upcomingEvents->isEmpty())
      <div style="padding: 32px; text-align:center; color:#a09890; font-size:13px;">No upcoming events found.</div>
    @else
      <div style="display:grid; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap:14px;">
        @foreach($upcomingEvents as $event)
          <a href="{{ route('events.show', $event) }}" style="padding:16px; display:block; border:1px solid #ece8e3; border-radius:12px; background:#fff; color:#0f0f0f; text-decoration:none; transition:transform 0.15s ease, box-shadow 0.15s ease;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-bottom:12px;">
              <div style="font-size:14px; font-weight:700;">{{ $event->name }}</div>
              <span style="font-size:11px; font-weight:700; color:#3B6D11; background:#eaf3de; padding:4px 8px; border-radius:999px;">{{ $event->status }}</span>
            </div>
            <div style="font-size:12px; color:#5c5550; margin-bottom:8px;">{{ $event->client_name }} · {{ $event->venue }}</div>
            <div style="display:flex; justify-content:space-between; align-items:center; font-size:12px; color:#a09890;">
              <span>{{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('d M Y') : 'Date TBC' }}</span>
              <span>{{ $event->loading_date ? \Carbon\Carbon::parse($event->loading_date)->format('d M') : '' }}</span>
            </div>
          </a>
        @endforeach
      </div>
    @endif
  </div>
</div>

<div class="db-card" style="margin-bottom: 24px;">
  <div class="db-card-head">
    <div>
      <div class="db-card-title">Inventory Movement Forecast</div>
      <div class="db-card-sub">Next 7 days: Dispatch (Red) vs. Returns (Dark)</div>
    </div>
    <div style="display:flex; gap:16px;">
      <div style="display:flex; align-items:center; gap:6px;"><div style="width:10px; height:10px; border-radius:3px; background:#CC0000;"></div><span style="font-size:11px; color:#5c5550; font-weight:600;">Outbound</span></div>
      <div style="display:flex; align-items:center; gap:6px;"><div style="width:10px; height:10px; border-radius:3px; background:#0f0f0f;"></div><span style="font-size:11px; color:#5c5550; font-weight:600;">Inbound</span></div>
    </div>
  </div>
  <div class="db-card-body" style="padding: 24px 30px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-end; height:150px; gap:12px;">
      @foreach($movements ?? [] as $m)
        @php
          $maxVal = collect($movements)->max(fn($item) => max($item['out'], $item['in'], 1));
          $outH = ($m['out'] / $maxVal) * 100;
          $inH = ($m['in'] / $maxVal) * 100;
        @endphp
        <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:12px;">
          <div style="flex:1; width:100%; display:flex; align-items:flex-end; justify-content:center; gap:4px;">
            <div style="width:14px; height:{{ $outH }}%; background:#CC0000; border-radius:4px 4px 0 0; transition:height 0.8s ease;" title="{{ $m['out'] }} Dispatches"></div>
            <div style="width:14px; height:{{ $inH }}%; background:#0f0f0f; border-radius:4px 4px 0 0; transition:height 0.8s ease;" title="{{ $m['in'] }} Returns"></div>
          </div>
          <div style="text-align:center;">
            <div style="font-size:12px; font-weight:700; color:#0f0f0f;">{{ $m['day'] }}</div>
            <div style="font-size:10px; color:#a09890; font-weight:500;">{{ date('j M', strtotime($m['date'])) }}</div>
          </div>
        </div>
      @endforeach
      @if(empty($movements))
        <div style="width:100%; text-align:center; padding:50px 0; color:#a09890; font-size:13px;">No movements scheduled for the next 7 days.</div>
      @endif
    </div>
  </div>
</div>

{{-- BOTTOM ROW --}}
<div class="db-grid-3-1">
  {{-- Recently updated items --}}
  <div class="db-card">
    <div class="db-card-head">
      <div><div class="db-card-title">Recent Inventory Movements</div><div class="db-card-sub">Latest status and location updates</div></div>
      <a href="{{ route('inventory.index') }}" class="db-card-action">View all &rarr;</a>
    </div>
    <table class="db-table">
      <thead><tr><th>Item</th><th>Status</th><th>Location</th><th>Updated</th></tr></thead>
      <tbody>
        @forelse($recentItems as $item)
        <tr>
          <td><a href="{{ route('inventory.show', $item->id) }}" class="db-item-name">{{ $item->name }}</a><span class="db-item-cat">{{ $item->category }}</span></td>
          <td><x-ui.status-badge :status="$item->status" /></td>
          <td class="db-item-loc">{{ $item->location ?? '—' }}</td>
          <td class="db-item-ago">{{ $item->last_updated_at ? $item->last_updated_at->diffForHumans() : '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="4" class="db-empty-row">No items found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Active repairs --}}
  <div class="db-card">
    <div class="db-card-head">
      <div><div class="db-card-title">Active Repairs</div><div class="db-card-sub">{{ $activeRepairs->count() }} items in workshop</div></div>
      <a href="{{ route('repairs.index') }}" class="db-card-action">View all &rarr;</a>
    </div>
    <div class="db-card-body" style="padding:0 16px">
      <div class="db-repair-list">
        @forelse($activeRepairs as $repair)
        <div class="db-repair-item">
          <div style="flex:1;min-width:0">
            <div class="db-repair-name">{{ $repair->item->name ?? 'Unknown item' }}</div>
            <div class="db-repair-cat">{{ $repair->item->category ?? '' }}</div>
          </div>
          <span class="db-repair-badge {{ $repair->status === 'In Progress' ? 'db-rb-progress' : 'db-rb-pending' }}">{{ $repair->status }}</span>
        </div>
        @empty
        <div class="db-repair-empty">No active repairs</div>
        @endforelse
      </div>
    </div>
  </div>
</div>

@endsection

<style>
.custom-scroll::-webkit-scrollbar { width: 6px; }
.custom-scroll::-webkit-scrollbar-track { background: #faf8f6; border-radius: 6px; }
.custom-scroll::-webkit-scrollbar-thumb { background: #d0c8c0; border-radius: 6px; }
.custom-scroll::-webkit-scrollbar-thumb:hover { background: #a09890; }
</style>
