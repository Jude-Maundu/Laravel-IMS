<aside id="ga-sidebar">

  {{-- LOGO --}}
  <div class="ga-logo">

    <div class="ga-logo-mark">
      <img src="{{ asset('images/grey-apple-events-logo.png') }}" alt="Grey Apple Events">
    </div>

    <div class="ga-logo-text">
      <span class="ga-logo-name">Grey Apple Events</span>
      <span class="ga-logo-sub">Inventory System</span>
    </div>

    <button class="ga-collapse-btn" onclick="gaToggleCollapse()" type="button" aria-label="Toggle sidebar">
      <svg viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.8"
           stroke-linecap="round" stroke-linejoin="round">
        <path d="M6.5 2L3.5 5l3 3"/>
      </svg>
    </button>

  </div>

  {{-- NAV --}}
  <nav class="ga-nav">

    {{-- GROUP 1: OVERVIEW --}}
    <div class="ga-group" id="ga-group-overview">
      <div class="ga-group-header" onclick="gaToggleGroup('ga-group-overview')">
        <span class="ga-group-label">OVERVIEW</span>
        <span class="ga-group-arrow">
          <svg viewBox="0 0 9 9" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M1.5 3L4.5 6l3-3"/></svg>
        </span>
      </div>
      <div class="ga-group-items">
        <a href="{{ route('dashboard.index') }}" class="ga-item {{ request()->routeIs('dashboard.*') ? 'ga-active' : '' }}">
          <svg class="ga-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="1" y="1" width="6" height="6" rx="1.5"/>
            <rect x="9" y="1" width="6" height="6" rx="1.5"/>
            <rect x="1" y="9" width="6" height="6" rx="1.5"/>
            <rect x="9" y="9" width="6" height="6" rx="1.5"/>
          </svg>
          <span class="ga-label">Dashboard</span>
          <span class="ga-tooltip"><span class="ga-tooltip-inner">Dashboard</span></span>
        </a>
      </div>
    </div>

    <div class="ga-divider"></div>

    {{-- GROUP 2: INVENTORY --}}
    <div class="ga-group" id="ga-group-inventory">
      <div class="ga-group-header" onclick="gaToggleGroup('ga-group-inventory')">
        <span class="ga-group-label">INVENTORY</span>
        <span class="ga-group-arrow">
          <svg viewBox="0 0 9 9" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M1.5 3L4.5 6l3-3"/></svg>
        </span>
      </div>
      <div class="ga-group-items">
        <a href="{{ route('inventory.index') }}" class="ga-item {{ request()->routeIs('inventory.index') || request()->routeIs('inventory.show') ? 'ga-active' : '' }}">
          <svg class="ga-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="1" width="12" height="14" rx="1.5"/>
            <line x1="5" y1="5" x2="11" y2="5"/>
            <line x1="5" y1="8" x2="11" y2="8"/>
            <line x1="5" y1="11" x2="9" y2="11"/>
          </svg>
          <span class="ga-label">All Items</span>
          <span class="ga-tooltip"><span class="ga-tooltip-inner">All Items</span></span>
        </a>
        <a href="{{ route('inventory.available') }}" class="ga-item {{ request()->routeIs('inventory.available') ? 'ga-active' : '' }}">
          <svg class="ga-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 12l2 2 4-4"/>
            <path d="M21 12V7a2 2 0 0 0-2-2h-5"/>
            <path d="M3 5a2 2 0 0 1 2-2h5"/>
            <path d="M3 9v6a2 2 0 0 0 2 2h5"/>
          </svg>
          <span class="ga-label">Available Items</span>
          @php $gaAvailableCount = \App\Models\Item::where('status', 'Available')->count(); @endphp
          @if($gaAvailableCount > 0)
            <span class="ga-badge ga-badge-green">{{ $gaAvailableCount }}</span>
          @endif
          <span class="ga-tooltip"><span class="ga-tooltip-inner">Available Items</span></span>
        </a>
        <a href="{{ route('inventory.pieces') }}" class="ga-item {{ request()->routeIs('inventory.pieces') ? 'ga-active' : '' }}">
          <svg class="ga-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="1" y="1" width="6" height="6" rx="1"/>
            <rect x="9" y="1" width="6" height="6" rx="1"/>
            <rect x="1" y="9" width="6" height="6" rx="1"/>
            <rect x="9" y="9" width="6" height="6" rx="1"/>
          </svg>
          <span class="ga-label">Item Pieces</span>
          <span class="ga-tooltip"><span class="ga-tooltip-inner">Item Pieces</span></span>
        </a>
        <a href="{{ route('inventory.create') }}" class="ga-item {{ request()->routeIs('inventory.create') ? 'ga-active' : '' }}">
          <svg class="ga-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="8" cy="8" r="6.5"/>
            <line x1="8" y1="5" x2="8" y2="11"/>
            <line x1="5" y1="8" x2="11" y2="8"/>
          </svg>
          <span class="ga-label">Add New Item</span>
          <span class="ga-tooltip"><span class="ga-tooltip-inner">Add New Item</span></span>
        </a>
        <a href="{{ route('categories.index') }}" class="ga-item {{ request()->routeIs('categories.*') ? 'ga-active' : '' }}">
          <svg class="ga-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 4h12M4 8h8M6 12h4"/>
          </svg>
          <span class="ga-label">Categories</span>
          <span class="ga-tooltip"><span class="ga-tooltip-inner">Categories</span></span>
        </a>
        <a href="{{ route('cleaning.index') }}" class="ga-item {{ request()->routeIs('cleaning.*') ? 'ga-active' : '' }}">
          <svg class="ga-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M13 10V4h-3M3 10V4h3M8 1v14"/>
          </svg>
          <span class="ga-label">Cleaning Bay</span>
          @php $gaCleaningCount = \App\Models\Item::where('status', 'Cleaning')->count(); @endphp
          @if($gaCleaningCount > 0)
            <span class="ga-badge ga-badge-green">{{ $gaCleaningCount }}</span>
          @endif
          <span class="ga-tooltip"><span class="ga-tooltip-inner">Cleaning Bay</span></span>
        </a>
        <a href="{{ route('repairs.index') }}" class="ga-item {{ request()->routeIs('repairs*') ? 'ga-active' : '' }}">
          <svg class="ga-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="1,8 4,8 5.5,4 7.5,12 9.5,6 11,8 15,8"/>
          </svg>
          <span class="ga-label">Repairs</span>
          @php $gaRepairCount = \App\Models\Repair::whereIn('status', ['Pending', 'In Progress'])->count(); @endphp
          @if($gaRepairCount > 0)
            <span class="ga-badge ga-badge-red">{{ $gaRepairCount }}</span>
          @endif
          <span class="ga-tooltip"><span class="ga-tooltip-inner">Repairs</span></span>
        </a>
      </div>
    </div>

    <div class="ga-divider"></div>

    {{-- GROUP 3: EVENT MANAGEMENT --}}
    <div class="ga-group" id="ga-group-events">
      <div class="ga-group-header" onclick="gaToggleGroup('ga-group-events')">
        <span class="ga-group-label">EVENT MANAGEMENT</span>
        <span class="ga-group-arrow">
          <svg viewBox="0 0 9 9" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M1.5 3L4.5 6l3-3"/></svg>
        </span>
      </div>
      <div class="ga-group-items">
        <a href="{{ route('events.index') }}" class="ga-item {{ (request()->routeIs('events.index') || request()->routeIs('events.show') || request()->routeIs('events.edit')) ? 'ga-active' : '' }}">
          <svg class="ga-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="1" y="3" width="14" height="11" rx="1.5"/>
            <line x1="1" y1="7" x2="15" y2="7"/>
            <line x1="5" y1="1" x2="5" y2="5"/>
            <line x1="11" y1="1" x2="11" y2="5"/>
          </svg>
          <span class="ga-label">All Events</span>
          <span class="ga-tooltip"><span class="ga-tooltip-inner">All Events</span></span>
        </a>
        <a href="{{ route('events.create') }}" class="ga-item {{ request()->routeIs('events.create') ? 'ga-active' : '' }}">
          <svg class="ga-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 13V3a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v10l-5-2-5 2z"/>
          </svg>
          <span class="ga-label">Book New Event</span>
          <span class="ga-tooltip"><span class="ga-tooltip-inner">Book New Event</span></span>
        </a>
        <a href="{{ route('events.requests') }}" class="ga-item {{ request()->routeIs('events.requests*') ? 'ga-active' : '' }}">
          <svg class="ga-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 10a1.5 1.5 0 0 1-1.5 1.5H4.5L2 14V3.5A1.5 1.5 0 0 1 3.5 2h9A1.5 1.5 0 0 1 14 3.5V10z"/>
          </svg>
          <span class="ga-label">Event Requests</span>
          @php
            $draftCount = \App\Models\Event::where('status','Draft')->count();
          @endphp
          @if($draftCount > 0)
            <span class="ga-badge ga-badge-red">{{ $draftCount }}</span>
          @endif
          <span class="ga-tooltip"><span class="ga-tooltip-inner">Event Requests</span></span>
        </a>
      </div>
    </div>

    <div class="ga-divider"></div>

    {{-- GROUP 4: ANALYTICS --}}
    <div class="ga-group" id="ga-group-analytics">
      <div class="ga-group-header" onclick="gaToggleGroup('ga-group-analytics')">
        <span class="ga-group-label">ANALYTICS</span>
        <span class="ga-group-arrow">
          <svg viewBox="0 0 9 9" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M1.5 3L4.5 6l3-3"/></svg>
        </span>
      </div>
      <div class="ga-group-items">
        <a href="{{ route('reports.index') }}" class="ga-item {{ request()->routeIs('reports*') ? 'ga-active' : '' }}">
          <svg class="ga-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="2" y1="14" x2="14" y2="14"/>
            <rect x="3" y="8" width="3" height="6"/>
            <rect x="6.5" y="5" width="3" height="9"/>
            <rect x="10" y="2" width="3" height="12"/>
          </svg>
          <span class="ga-label">Reports</span>
          <span class="ga-tooltip"><span class="ga-tooltip-inner">Reports</span></span>
        </a>
        <a href="{{ route('activity.index') }}" class="ga-item {{ request()->routeIs('activity*') ? 'ga-active' : '' }}">
          <svg class="ga-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="4" cy="8" r="2"/>
            <circle cx="12" cy="4" r="2"/>
            <circle cx="12" cy="12" r="2"/>
            <line x1="6" y1="7" x2="10" y2="5"/>
            <line x1="6" y1="9" x2="10" y2="11"/>
          </svg>
          <span class="ga-label">Activity Log</span>
          <span class="ga-tooltip"><span class="ga-tooltip-inner">Activity Log</span></span>
        </a>
      </div>
    </div>

    {{-- GROUP 5: ADMINISTRATION (Admin only) --}}
    @if(auth()->user()->hasRole('Admin'))
      <div class="ga-divider"></div>
      <div class="ga-group" id="ga-group-admin">
        <div class="ga-group-header" onclick="gaToggleGroup('ga-group-admin')">
          <span class="ga-group-label">ADMINISTRATION</span>
          <span class="ga-group-arrow">
            <svg viewBox="0 0 9 9" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M1.5 3L4.5 6l3-3"/></svg>
          </span>
        </div>
        <div class="ga-group-items">
          <a href="{{ route('users.index') }}" class="ga-item {{ request()->routeIs('users*') ? 'ga-active' : '' }}">
            <svg class="ga-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="8" cy="5" r="3"/>
              <path d="M1 14c0-3.3 3.1-6 7-6s7 2.7 7 6"/>
            </svg>
            <span class="ga-label">Users</span>
            <span class="ga-tooltip"><span class="ga-tooltip-inner">Users</span></span>
          </a>
          <a href="{{ route('settings.index') }}" class="ga-item {{ request()->routeIs('settings*') ? 'ga-active' : '' }}">
            <svg class="ga-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="8" cy="8" r="2.5"/>
              <path d="M8 1v2M8 13v2M1 8h2M13 8h2M3.1 3.1l1.4 1.4M11.5 11.5l1.4 1.4M3.1 12.9l1.4-1.4M11.5 4.5l1.4-1.4"/>
            </svg>
            <span class="ga-label">Settings</span>
            <span class="ga-tooltip"><span class="ga-tooltip-inner">Settings</span></span>
          </a>
        </div>
      </div>
    @endif

  </nav>

  {{-- FOOTER --}}
  <div class="ga-footer">
    <div class="ga-footer-row">
      <div class="ga-avatar">
        {{ strtoupper(substr(explode(' ', auth()->user()->name)[0], 0, 1)) }}{{ strtoupper(substr(explode(' ', auth()->user()->name)[1] ?? 'X', 0, 1)) }}
      </div>
      <div class="ga-user-info">
        <span class="ga-user-name">{{ auth()->user()->name }}</span>
        <span class="ga-user-role">{{ auth()->user()->getRoleNames()->first() ?? 'User' }}</span>
      </div>
      <button class="ga-theme-toggle" onclick="gaToggleTheme()" type="button">
        <div class="ga-theme-dot"></div>
      </button>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="ga-logout-form">
      @csrf
      <button type="submit" class="ga-logout-btn">
        <svg class="ga-logout-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M6 2H3a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h3"/>
          <path d="M10 11l3-3-3-3"/>
          <line x1="13" y1="8" x2="6" y2="8"/>
        </svg>
        <span class="ga-logout-label">Sign Out</span>
      </button>
    </form>
  </div>

</aside>

<script>
(function() {
  const SB = document.getElementById('ga-sidebar');
  const LS_COLLAPSED = 'ga_sb_collapsed';
  const LS_THEME = 'ga_sb_theme';
  const LS_GROUPS = 'ga_sb_groups';

  // Restore collapse state
  if (localStorage.getItem(LS_COLLAPSED) === '1') {
    SB.classList.add('ga-collapsed');
    updateMainMargin(true);
  }

  // Restore theme
  if (localStorage.getItem(LS_THEME) === 'dark') {
    SB.classList.add('ga-dark');
  }

  // Restore group states
  if (localStorage.getItem(LS_GROUPS) === null) {
    localStorage.setItem(LS_GROUPS, '[]');
  }
  const closedGroups = JSON.parse(localStorage.getItem(LS_GROUPS) || '[]');
  closedGroups.forEach(id => {
    const g = document.getElementById(id);
    if (g) g.classList.add('ga-closed');
  });

  window.gaToggleCollapse = function() {
    const collapsed = SB.classList.toggle('ga-collapsed');
    localStorage.setItem(LS_COLLAPSED, collapsed ? '1' : '0');
    updateMainMargin(collapsed);
    var tb = document.getElementById('ga-topbar');
    if(tb) tb.style.left = collapsed ? '64px' : '260px';
  };

  window.gaToggleTheme = function() {
    const dark = SB.classList.toggle('ga-dark');
    localStorage.setItem(LS_THEME, dark ? 'dark' : 'light');
  };

  window.gaToggleGroup = function(id) {
    const g = document.getElementById(id);
    if (!g) return;
    g.classList.toggle('ga-closed');
    const closed = Array.from(document.querySelectorAll('.ga-group.ga-closed')).map(el => el.id);
    localStorage.setItem(LS_GROUPS, JSON.stringify(closed));
  };

  function updateMainMargin(collapsed) {
    const main = document.getElementById('ga-main-content');
    if (!main) return;
    main.style.marginLeft = collapsed ? '64px' : '260px';
  }

  // Tooltip positioning for collapsed state
  document.querySelectorAll('.ga-item').forEach(function(item) {
    var tip = item.querySelector('.ga-tooltip');
    if (!tip) return;
    item.addEventListener('mouseenter', function() {
      if (!SB.classList.contains('ga-collapsed')) return;
      var rect = item.getBoundingClientRect();
      tip.style.top = (rect.top + rect.height / 2 - 14) + 'px';
      tip.style.opacity = '1';
      tip.style.transform = 'translateX(0)';
    });
    item.addEventListener('mouseleave', function() {
      tip.style.opacity = '0';
      tip.style.transform = 'translateX(-6px)';
    });
  });
})();
</script>
