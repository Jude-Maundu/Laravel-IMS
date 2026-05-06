@extends('layouts.app')
@section('title', 'Packing List — ' . $event->name)
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
      <span style="font-size:12px;color:#5c5550;font-weight:500">Packing List</span>
    </div>
    <h1 class="wiz-page-title">Build Packing List</h1>
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
  <div class="wiz-step wiz-step-active">
    <div class="wiz-step-num">2</div>
    <div class="wiz-step-info">
      <span class="wiz-step-label">Packing List</span>
      <span class="wiz-step-sub">Items, borrowed & operational</span>
    </div>
  </div>
  <div class="wiz-step-line"></div>
  <div class="wiz-step wiz-step-inactive">
    <div class="wiz-step-num">3</div>
    <div class="wiz-step-info">
      <span class="wiz-step-label">Assign Team</span>
      <span class="wiz-step-sub">Crew members</span>
    </div>
  </div>
  <div class="wiz-step-line"></div>
  <div class="wiz-step wiz-step-inactive">
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
    <form method="POST" action="{{ route('events.checklist.save', $event) }}" id="packing-form" autocomplete="off">
      @csrf

      {{-- VALIDATION ERRORS --}}
      @if($errors->any())
      <div class="wiz-errors">
        <div class="wiz-error-header">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
            <circle cx="8" cy="8" r="6.5"/>
            <line x1="8" y1="5" x2="8" y2="9"/>
            <circle cx="8" cy="11" r="0.5" fill="currentColor"/>
          </svg>
          <span>Please fix the following errors:</span>
        </div>
        <ul class="wiz-error-list">
          @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      <div class="wiz-card">
        {{-- TAB NAVIGATION --}}
        <div class="wiz-tab-nav">
          <button type="button" class="wiz-tab-btn active" data-tab="own" id="tab-btn-own">
            📦 Own Inventory <span class="wiz-tab-badge" id="own-badge">0</span>
          </button>
          <button type="button" class="wiz-tab-btn" data-tab="borrowed" id="tab-btn-borrowed">
            🔄 Borrowed Items
          </button>
          <button type="button" class="wiz-tab-btn" data-tab="operational" id="tab-btn-operational">
            🔧 Operational Items
          </button>
        </div>

        {{-- TAB 1: OWN INVENTORY --}}
        <div class="wiz-tab-panel active" id="panel-own">
          <div class="wiz-card-body">
            {{-- SEARCH BAR --}}
            <div class="wiz-search-bar">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" class="wiz-search-icon">
                <circle cx="7" cy="7" r="5"/>
                <path d="M11 11l3.5 3.5"/>
              </svg>
              <input type="text" id="item-search" class="wiz-search-input" placeholder="Search items by name, category, or item code..." autocomplete="off">
              <button type="button" id="clear-search-btn" class="wiz-search-clear" style="display:none" onclick="clearSearch()">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                  <path d="M4 4l8 8M12 4l-8 8"/>
                </svg>
              </button>
            </div>
            <div id="search-results-info" class="wiz-search-info" style="display:none"></div>

            {{-- INSTRUCTION BANNER --}}
            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px">
              <div class="wiz-notice" style="flex:1;margin:0">
                <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><line x1="8" y1="7" x2="8" y2="11"/><circle cx="8" cy="5" r="0.5" fill="currentColor"/></svg>
                <span>Select items from Grey Apple's inventory and enter the quantity needed for this event. Availability shows pieces currently in the warehouse.</span>
              </div>
              <button type="button" onclick="clearAllItems()" class="wiz-btn-clear-all" id="clear-all-btn" style="display:none">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                  <path d="M4 4l8 8M12 4l-8 8"/>
                </svg>
                Clear All
              </button>
            </div>

            {{-- SELECTED ITEMS PANEL (appears at the top when not searching) --}}
            <div class="wiz-selected-panel wiz-selected-panel-empty" id="selected-panel">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                <span style="font-size:12px;font-weight:600;color:#0f0f0f">Selected Items (<span id="selected-count">0</span>)</span>
                <span style="font-size:10px;font-weight:700;color:#7c7470;text-transform:uppercase;letter-spacing:0.05em">Quantity</span>
              </div>
              <div id="selected-list"></div>
            </div>

            {{-- SEARCH RESULTS AREA (overlays selected when searching) --}}
            <div id="search-results-container">
              {{-- CATEGORY ACCORDION --}}
            @foreach($itemsByCategory as $category => $items)
            <div class="wiz-cat-group" data-category="{{ $loop->index }}" data-category-name="{{ strtolower($category) }}">
              <div class="wiz-cat-header" onclick="toggleCategory({{ $loop->index }})">
                <div style="display:flex;align-items:center;gap:8px">
                  <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="wiz-cat-arrow" id="arrow-{{ $loop->index }}">
                    <path d="M6 4l4 4-4 4"/>
                  </svg>
                  <span class="wiz-cat-name">{{ $category }}</span>
                </div>
                <span class="wiz-cat-count" id="cat-count-{{ $loop->index }}">{{ $items->count() }} items</span>
              </div>
              <div class="wiz-cat-body {{ $loop->first ? '' : 'collapsed' }}" id="cat-{{ $loop->index }}">
                @foreach($items as $item)
                @php
                  $existing = $existingEventItems->get($item->id);
                  $isPreSelected = $existing !== null;
                  $preQuantity = $existing ? $existing->quantity_requested : 1;
                  $itemCode = 'ITM-' . str_pad($item->id, 3, '0', STR_PAD_LEFT);
                @endphp
                <div class="wiz-item-row {{ $isPreSelected ? 'wiz-item-row-checked' : '' }}"
                     id="row-{{ $item->id }}"
                     data-item-id="{{ $item->id }}"
                     data-item-name="{{ strtolower($item->name) }}"
                     data-item-code="{{ strtolower($itemCode) }}"
                     data-item-category="{{ strtolower($category) }}">
                  <input type="checkbox" class="wiz-item-checkbox" id="check-{{ $item->id }}" data-item-id="{{ $item->id }}" {{ $isPreSelected ? 'checked' : '' }} onchange="handleItemCheck({{ $item->id }})">
                  <label for="check-{{ $item->id }}" style="cursor:pointer;display:flex;align-items:center;gap:12px;flex:1">
                    @if($item->primaryImage)
                      <img src="{{ asset('storage/' . $item->primaryImage->image_path) }}" class="wiz-item-thumb" alt="{{ $item->name }}">
                    @else
                      <div class="wiz-item-thumb" style="background:#f5f1ed;display:flex;align-items:center;justify-content:center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#b0a8a0" stroke-width="1.5">
                          <rect x="3" y="3" width="18" height="18" rx="2"/>
                          <circle cx="8.5" cy="8.5" r="1.5"/>
                          <path d="M21 15l-5-5L5 21"/>
                        </svg>
                      </div>
                    @endif
                    <div style="flex:1">
                      <div style="font-size:13px;font-weight:600;color:#0f0f0f">{{ $item->name }}</div>
                      <div style="font-size:11px;color:#a09890">{{ $item->category }}</div>
                    </div>
                  </label>
                  <div class="wiz-avail-badge wiz-avail-loading" id="avail-{{ $item->id }}" data-item-id="{{ $item->id }}">
                    Loading...
                  </div>
                  <div style="display:{{ $isPreSelected ? 'flex' : 'none' }};align-items:center;gap:8px" id="qty-{{ $item->id }}">
                    <label for="qty-input-{{ $item->id }}" style="font-size:11px;color:#5c5550">Qty:</label>
                    <input type="number" name="items[{{ $item->id }}][quantity]" id="qty-input-{{ $item->id }}" class="wiz-qty-input" min="1" value="{{ $preQuantity }}" oninput="handleQtyChange({{ $item->id }})" style="width:60px" {{ $isPreSelected ? '' : 'disabled' }}>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
            @endforeach
            </div>{{-- End search results container --}}

          </div>
        </div>

        {{-- TAB 2: BORROWED ITEMS --}}
        <div class="wiz-tab-panel" id="panel-borrowed">
          <div class="wiz-card-body">
            {{-- INSTRUCTION BANNER --}}
            <div class="wiz-notice">
              <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><line x1="8" y1="7" x2="8" y2="11"/><circle cx="8" cy="5" r="0.5" fill="currentColor"/></svg>
              <span>If items are being borrowed from another company for this event, enable this section and add the details. Borrowed items appear on the packing list and must be returned after the event.</span>
            </div>

            {{-- TOGGLE SWITCH --}}
            <div style="margin:16px 0">
              <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
                <input type="checkbox" id="borrowed-toggle" onchange="toggleBorrowed()" {{ $existingBorrowedItems->count() > 0 ? 'checked' : '' }}>
                <span style="font-size:12px;font-weight:600;color:#0f0f0f">Include borrowed items for this event</span>
              </label>
            </div>

            {{-- BORROWED FORM --}}
            <div id="borrowed-form" style="display:{{ $existingBorrowedItems->count() > 0 ? 'block' : 'none' }}">
              <input type="hidden" name="borrowed_enabled" id="borrowed-enabled" value="{{ $existingBorrowedItems->count() > 0 ? '1' : '0' }}">
              <div id="borrowed-rows">
                @if($existingBorrowedItems->count() > 0)
                  @foreach($existingBorrowedItems as $borrowed)
                  <div class="wiz-borrowed-row">
                    <input type="text" name="borrowed[{{ $loop->index }}][item_name]" placeholder="Item description *" class="wiz-input" style="flex:2" value="{{ $borrowed->item_name }}" required>
                    <input type="text" name="borrowed[{{ $loop->index }}][source_company]" placeholder="Source company" class="wiz-input" style="flex:1" value="{{ $borrowed->source_company }}">
                    <input type="number" name="borrowed[{{ $loop->index }}][quantity]" placeholder="Qty" class="wiz-input" style="width:80px" min="1" value="{{ $borrowed->quantity_dispatched }}" required>
                    <input type="text" name="borrowed[{{ $loop->index }}][notes]" placeholder="Notes" class="wiz-input" style="flex:1" value="{{ $borrowed->notes }}">
                    <button type="button" class="wiz-btn-icon-danger" onclick="removeBorrowedRow(this)" {{ $loop->first && $existingBorrowedItems->count() === 1 ? 'disabled' : '' }}>
                      <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 4l8 8M12 4l-8 8"/></svg>
                    </button>
                  </div>
                  @endforeach
                @endif
              </div>
              <button type="button" class="wiz-btn-secondary" onclick="addBorrowedRow()" style="margin-top:10px">
                <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M8 3v10M3 8h10"/></svg>
                Add Another Item
              </button>
            </div>

            <div id="borrowed-muted" style="display:{{ $existingBorrowedItems->count() > 0 ? 'none' : 'block' }};padding:20px;text-align:center;color:#b0a8a0;font-size:12px">
              No borrowed items for this event
            </div>

          </div>
        </div>

        {{-- TAB 3: OPERATIONAL ITEMS --}}
        <div class="wiz-tab-panel" id="panel-operational">
          <div class="wiz-card-body">
            {{-- SEARCH BAR FOR OPERATIONAL --}}
            <div class="wiz-search-bar">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" class="wiz-search-icon">
                <circle cx="7" cy="7" r="5"/>
                <path d="M11 11l3.5 3.5"/>
              </svg>
              <input type="text" id="op-search" class="wiz-search-input" placeholder="Search operational items by name or category..." autocomplete="off">
              <button type="button" id="clear-op-search-btn" class="wiz-search-clear" style="display:none" onclick="clearOpSearch()">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                  <path d="M4 4l8 8M12 4l-8 8"/>
                </svg>
              </button>
            </div>
            <div id="op-search-results-info" class="wiz-search-info" style="display:none"></div>

            {{-- INSTRUCTION BANNER --}}
            <div class="wiz-notice">
              <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><line x1="8" y1="7" x2="8" y2="11"/><circle cx="8" cy="5" r="0.5" fill="currentColor"/></svg>
              <span>Operational items are internal tools and safety equipment sent to every event. Select what is needed and set quantities. These are tracked by count only — no QR scanning required.</span>
            </div>

            {{-- SELECTED OPERATIONAL ITEMS PANEL --}}
            <div class="wiz-selected-panel wiz-selected-panel-empty" id="op-selected-panel">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                <span style="font-size:12px;font-weight:600;color:#0f0f0f">Selected Items (<span id="op-selected-count">0</span>)</span>
                <span style="font-size:10px;font-weight:700;color:#7c7470;text-transform:uppercase;letter-spacing:0.05em">Quantity</span>
              </div>
              <div id="op-selected-list"></div>
            </div>

            {{-- OPERATIONAL ITEMS BY CATEGORY (ACCORDION) --}}
            <div id="op-search-results-container">
              @foreach($operationalItems as $category => $items)
              <div class="wiz-cat-group" data-op-category-idx="{{ $loop->index }}" data-op-category-name="{{ strtolower($category) }}">
                <div class="wiz-cat-header" onclick="toggleOpCategory({{ $loop->index }})">
                  <div style="display:flex;align-items:center;gap:8px">
                    <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="wiz-cat-arrow" id="op-arrow-{{ $loop->index }}">
                      <path d="M6 4l4 4-4 4"/>
                    </svg>
                    <span class="wiz-cat-name">{{ $category }}</span>
                  </div>
                  <span class="wiz-cat-count" id="op-cat-count-{{ $loop->index }}">{{ $items->count() }} items</span>
                </div>
                <div class="wiz-cat-body {{ $loop->first ? '' : 'collapsed' }}" id="op-cat-{{ $loop->index }}">
                  @foreach($items as $opItem)
                  @php
                    $existing = $existingOperationalItems->firstWhere('operational_item_id', $opItem->id);
                    $isPreSelected = $existing !== null;
                    $preQty = $existing ? $existing->quantity_dispatched : 1;
                  @endphp
                  <div class="wiz-item-row {{ $isPreSelected ? 'wiz-item-row-checked' : '' }}"
                       id="op-row-{{ $opItem->id }}"
                       data-op-name="{{ strtolower($opItem->name) }}"
                       data-op-category="{{ strtolower($category) }}">
                    <input type="checkbox" class="wiz-item-checkbox" id="op-check-{{ $opItem->id }}" data-op-id="{{ $opItem->id }}" {{ $isPreSelected ? 'checked' : '' }} onchange="handleOpItemCheck({{ $opItem->id }})">
                    <label for="op-check-{{ $opItem->id }}" style="cursor:pointer;display:flex;align-items:center;gap:12px;flex:1">
                      <div style="flex:1">
                        <div style="font-size:13px;font-weight:600;color:#0f0f0f">{{ $opItem->name }}</div>
                        <div style="font-size:11px;color:#a09890">{{ $category }}</div>
                      </div>
                    </label>
                    <div style="display:{{ $isPreSelected ? 'flex' : 'none' }};align-items:center;gap:8px" id="op-qty-{{ $opItem->id }}">
                      <label for="op-qty-input-{{ $opItem->id }}" style="font-size:11px;color:#5c5550">Qty:</label>
                      <input type="number" name="operational[{{ $loop->parent->index * 100 + $loop->index }}][quantity]" id="op-qty-input-{{ $opItem->id }}" class="wiz-qty-input" min="1" value="{{ $preQty }}" oninput="handleOpQtyChange({{ $opItem->id }})" style="width:60px" {{ $isPreSelected ? '' : 'disabled' }}>
                      <input type="hidden" name="operational[{{ $loop->parent->index * 100 + $loop->index }}][operational_item_id]" value="{{ $opItem->id }}" id="op-hidden-{{ $opItem->id }}" {{ $isPreSelected ? '' : 'disabled' }}>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
              @endforeach
            </div>

            {{-- ADD CUSTOM ITEM BUTTON --}}
            <div style="margin-top:16px">
              <button type="button" class="wiz-btn-secondary" onclick="showCustomOpForm()">
                <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M8 3v10M3 8h10"/></svg>
                Add Custom Item
              </button>
            </div>

            {{-- CUSTOM ITEMS CONTAINER (hidden inputs) --}}
            <div id="custom-op-container" style="display:none"></div>

          </div>
        </div>

        {{-- CARD FOOTER --}}
        <div class="wiz-card-footer">
          <span class="wiz-footer-hint">Step 2 of 4 &mdash; Next: assign team members</span>
          <div class="wiz-footer-actions">
            <a href="{{ route('events.show', $event) }}" class="wiz-btn-cancel">← Back</a>
            <button type="submit" class="wiz-btn-next" id="submit-btn">
              Save & Continue
              <svg width="13" height="13" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4.5 2L7.5 6l-3 4"/></svg>
            </button>
          </div>
        </div>

      </div>

    </form>
  </div>

</div>

<script>
(function() {
  // State
  let availabilityData = {};
  let selectedItems = new Set();
  let borrowedRowCount = {{ $existingBorrowedItems->count() }};

  // 1. TAB SWITCHING
  document.querySelectorAll('.wiz-tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const targetTab = this.getAttribute('data-tab');

      // Update buttons
      document.querySelectorAll('.wiz-tab-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');

      // Update panels
      document.querySelectorAll('.wiz-tab-panel').forEach(p => p.classList.remove('active'));
      document.getElementById('panel-' + targetTab).classList.add('active');

      // Save to session
      sessionStorage.setItem('activePackingTab', targetTab);
    });
  });

  // Restore last active tab
  const lastTab = sessionStorage.getItem('activePackingTab');
  if (lastTab && document.querySelector('[data-tab="' + lastTab + '"]')) {
    document.querySelector('[data-tab="' + lastTab + '"]').click();
  }

  // 1.5 SEARCH FUNCTIONALITY
  const searchInput = document.getElementById('item-search');
  const clearSearchBtn = document.getElementById('clear-search-btn');
  const searchInfo = document.getElementById('search-results-info');
  let searchTimeout;

  window.clearSearch = function() {
    searchInput.value = '';
    performSearch('');
    clearSearchBtn.style.display = 'none';
    searchInfo.style.display = 'none';
  };

  if (searchInput) {
    searchInput.addEventListener('input', function(e) {
      const query = e.target.value.trim();

      // Show/hide clear button
      clearSearchBtn.style.display = query ? 'flex' : 'none';

      // Debounce search
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        performSearch(query);
      }, 200);
    });

    // Clear on Escape key
    searchInput.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        clearSearch();
      }
    });
  }

  function performSearch(query) {
    const lowerQuery = query.toLowerCase();
    const allCategories = document.querySelectorAll('.wiz-cat-group');
    const searchContainer = document.getElementById('search-results-container');
    let totalMatches = 0;
    let matchedCategories = 0;

    if (!query) {
      // Remove searching state
      if (searchContainer) searchContainer.classList.remove('searching');

      // Reset to default state - only first category open
      allCategories.forEach((catGroup, index) => {
        catGroup.style.display = 'block';
        const catBody = catGroup.querySelector('.wiz-cat-body');
        const arrow = catGroup.querySelector('.wiz-cat-arrow');
        const items = catGroup.querySelectorAll('.wiz-item-row');

        // Show all items
        items.forEach(item => item.style.display = 'flex');

        // Update count
        const countEl = catGroup.querySelector('.wiz-cat-count');
        if (countEl) {
          const originalCount = items.length;
          countEl.textContent = originalCount + ' items';
        }

        // Only first category open by default
        if (index === 0) {
          catBody.classList.remove('collapsed');
          if (arrow) arrow.style.transform = 'rotate(90deg)';
        } else {
          catBody.classList.add('collapsed');
          if (arrow) arrow.style.transform = 'rotate(0deg)';
        }
      });
      searchInfo.style.display = 'none';
      return;
    }

    // Add searching state to overlay selected panel
    if (searchContainer) searchContainer.classList.add('searching');

    // Search mode
    allCategories.forEach((catGroup, catIndex) => {
      const items = catGroup.querySelectorAll('.wiz-item-row');
      const catBody = catGroup.querySelector('.wiz-cat-body');
      const arrow = catGroup.querySelector('.wiz-cat-arrow');
      const categoryName = catGroup.getAttribute('data-category-name') || '';
      let visibleItemsInCategory = 0;

      items.forEach(item => {
        const itemName = item.getAttribute('data-item-name') || '';
        const itemCode = item.getAttribute('data-item-code') || '';
        const itemCategory = item.getAttribute('data-item-category') || '';

        // Check if item matches search query
        const matches = itemName.includes(lowerQuery) ||
                       itemCode.includes(lowerQuery) ||
                       itemCategory.includes(lowerQuery);

        if (matches) {
          item.style.display = 'flex';
          visibleItemsInCategory++;
          totalMatches++;
        } else {
          item.style.display = 'none';
        }
      });

      // Update category count
      const countEl = catGroup.querySelector('.wiz-cat-count');
      if (countEl) {
        if (visibleItemsInCategory > 0) {
          countEl.textContent = visibleItemsInCategory + ' of ' + items.length + ' items';
        } else {
          countEl.textContent = items.length + ' items';
        }
      }

      // Show/hide category and expand if has matches
      if (visibleItemsInCategory > 0) {
        catGroup.style.display = 'block';
        catBody.classList.remove('collapsed');
        if (arrow) arrow.style.transform = 'rotate(90deg)';
        matchedCategories++;
      } else {
        catGroup.style.display = 'none';
      }
    });

    // Show search results info
    if (totalMatches > 0) {
      searchInfo.innerHTML = '<svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><line x1="8" y1="6" x2="8" y2="11"/><circle cx="8" cy="4" r="0.5" fill="currentColor"/></svg><span>Found <strong>' + totalMatches + '</strong> item(s) in <strong>' + matchedCategories + '</strong> category(ies)</span>';
      searchInfo.style.display = 'flex';
      searchInfo.style.color = '#3B6D11';
      searchInfo.style.background = '#eaf3de';
      searchInfo.style.borderColor = '#d4e7c5';
    } else {
      searchInfo.innerHTML = '<svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><path d="M5 8h6"/></svg><span>No items found matching "<strong>' + query + '</strong>"</span>';
      searchInfo.style.display = 'flex';
      searchInfo.style.color = '#854F0B';
      searchInfo.style.background = '#faeeda';
      searchInfo.style.borderColor = '#f0dcc5';
    }
  }

  // 1.6 OPERATIONAL ITEMS SEARCH
  const opSearchInput = document.getElementById('op-search');
  const clearOpSearchBtn = document.getElementById('clear-op-search-btn');
  const opSearchInfo = document.getElementById('op-search-results-info');
  let opSearchTimeout;

  window.clearOpSearch = function() {
    opSearchInput.value = '';
    performOpSearch('');
    clearOpSearchBtn.style.display = 'none';
    opSearchInfo.style.display = 'none';
  };

  if (opSearchInput) {
    opSearchInput.addEventListener('input', function(e) {
      const query = e.target.value.trim();
      clearOpSearchBtn.style.display = query ? 'flex' : 'none';

      clearTimeout(opSearchTimeout);
      opSearchTimeout = setTimeout(() => {
        performOpSearch(query);
      }, 200);
    });

    opSearchInput.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        clearOpSearch();
      }
    });
  }

  function performOpSearch(query) {
    const lowerQuery = query.toLowerCase();
    const opSearchContainer = document.getElementById('op-search-results-container');
    const allOpCategories = opSearchContainer.querySelectorAll('.wiz-cat-group');
    let totalMatches = 0;
    let matchedCategories = 0;

    if (!query) {
      // Remove searching state
      if (opSearchContainer) opSearchContainer.classList.remove('searching');

      // Reset to default state - only first category open
      allOpCategories.forEach((catGroup, index) => {
        catGroup.style.display = 'block';
        const catBody = catGroup.querySelector('.wiz-cat-body');
        const arrow = catGroup.querySelector('.wiz-cat-arrow');
        const items = catGroup.querySelectorAll('.wiz-item-row');

        // Show all items
        items.forEach(item => item.style.display = 'flex');

        // Update count
        const countEl = catGroup.querySelector('.wiz-cat-count');
        if (countEl) {
          const originalCount = items.length;
          countEl.textContent = originalCount + ' items';
        }

        // Only first category open by default
        if (index === 0) {
          catBody.classList.remove('collapsed');
          if (arrow) arrow.style.transform = 'rotate(90deg)';
        } else {
          catBody.classList.add('collapsed');
          if (arrow) arrow.style.transform = 'rotate(0deg)';
        }
      });
      opSearchInfo.style.display = 'none';
      return;
    }

    // Add searching state to overlay selected panel
    if (opSearchContainer) opSearchContainer.classList.add('searching');

    // Search mode
    allOpCategories.forEach((catGroup, catIndex) => {
      const items = catGroup.querySelectorAll('.wiz-item-row');
      const catBody = catGroup.querySelector('.wiz-cat-body');
      const arrow = catGroup.querySelector('.wiz-cat-arrow');
      const categoryName = catGroup.getAttribute('data-op-category-name') || '';
      let visibleItemsInCategory = 0;

      items.forEach(item => {
        const itemName = item.getAttribute('data-op-name') || '';
        const itemCategory = item.getAttribute('data-op-category') || '';

        // Check if item matches search query
        const matches = itemName.includes(lowerQuery) || itemCategory.includes(lowerQuery);

        if (matches) {
          item.style.display = 'flex';
          visibleItemsInCategory++;
          totalMatches++;
        } else {
          item.style.display = 'none';
        }
      });

      // Update category count
      const countEl = catGroup.querySelector('.wiz-cat-count');
      if (countEl) {
        if (visibleItemsInCategory > 0) {
          countEl.textContent = visibleItemsInCategory + ' of ' + items.length + ' items';
        } else {
          countEl.textContent = items.length + ' items';
        }
      }

      // Show/hide category and expand if has matches
      if (visibleItemsInCategory > 0) {
        catGroup.style.display = 'block';
        catBody.classList.remove('collapsed');
        if (arrow) arrow.style.transform = 'rotate(90deg)';
        matchedCategories++;
      } else {
        catGroup.style.display = 'none';
      }
    });

    // Show search info
    if (totalMatches > 0) {
      opSearchInfo.innerHTML = '<svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><line x1="8" y1="6" x2="8" y2="11"/><circle cx="8" cy="4" r="0.5" fill="currentColor"/></svg><span>Found <strong>' + totalMatches + '</strong> operational item(s) in <strong>' + matchedCategories + '</strong> category(ies)</span>';
      opSearchInfo.style.display = 'flex';
      opSearchInfo.style.color = '#3B6D11';
      opSearchInfo.style.background = '#eaf3de';
      opSearchInfo.style.borderColor = '#d4e7c5';
    } else {
      opSearchInfo.innerHTML = '<svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><path d="M5 8h6"/></svg><span>No operational items found matching "<strong>' + query + '</strong>"</span>';
      opSearchInfo.style.display = 'flex';
      opSearchInfo.style.color = '#854F0B';
      opSearchInfo.style.background = '#faeeda';
      opSearchInfo.style.borderColor = '#f0dcc5';
    }
  }

  // 2. LOAD AVAILABILITY VIA AJAX
  @foreach($itemsByCategory as $category => $items)
    @foreach($items as $item)
      fetch('/api/items/{{ $item->id }}/availability')
        .then(r => r.json())
        .then(data => {
          availabilityData[{{ $item->id }}] = data;
          updateAvailabilityBadge({{ $item->id }});
        })
        .catch(() => {
          const badge = document.getElementById('avail-{{ $item->id }}');
          if (badge) {
            badge.textContent = 'Error';
            badge.className = 'wiz-avail-badge wiz-avail-red';
          }
        });
    @endforeach
  @endforeach

  // 3. ITEM CHECKBOX HANDLER
  window.handleItemCheck = function(itemId) {
    const checkbox = document.getElementById('check-' + itemId);
    const row = document.getElementById('row-' + itemId);
    const qtyDiv = document.getElementById('qty-' + itemId);
    const qtyInput = document.getElementById('qty-input-' + itemId);

    if (checkbox.checked) {
      selectedItems.add(itemId);
      row.classList.add('wiz-item-row-checked');
      qtyDiv.style.display = 'flex';
      if (qtyInput) qtyInput.disabled = false;
      addToSelectedPanel(itemId);
    } else {
      selectedItems.delete(itemId);
      row.classList.remove('wiz-item-row-checked');
      qtyDiv.style.display = 'none';
      if (qtyInput) qtyInput.disabled = true;
      removeFromSelectedPanel(itemId);
    }

    updateOwnBadge();
    updateAvailabilityBadge(itemId);
    validateAllQuantities(); // Validate all quantities after item selection changes
  };

  // 4. QUANTITY INPUT HANDLER
  window.handleQtyChange = function(itemId) {
    const input = document.getElementById('qty-input-' + itemId);
    const requested = parseInt(input.value) || 0;
    const data = availabilityData[itemId];

    if (!data) return;

    // Validate this input
    if (requested > data.available) {
      input.style.borderColor = '#CC0000';
    } else {
      input.style.borderColor = '#ece8e3';
    }

    updateAvailabilityBadge(itemId);
    updateSelectedPanelQty(itemId, requested);

    // Check if any selected item has invalid quantity
    validateAllQuantities();
  };

  // Validate all quantities and update submit button
  function validateAllQuantities() {
    let hasError = false;
    selectedItems.forEach(itemId => {
      const input = document.getElementById('qty-input-' + itemId);
      const data = availabilityData[itemId];
      if (data && input && parseInt(input.value) > data.available) {
        hasError = true;
      }
    });

    const submitBtn = document.getElementById('submit-btn');
    if (hasError) {
      submitBtn.disabled = true;
      submitBtn.style.opacity = '0.6';
    } else {
      submitBtn.disabled = false;
      submitBtn.style.opacity = '1';
    }
  }

  function updateAvailabilityBadge(itemId) {
    const badge = document.getElementById('avail-' + itemId);
    const input = document.getElementById('qty-input-' + itemId);
    const data = availabilityData[itemId];

    if (!badge || !data) return;

    const requested = input && input.value ? parseInt(input.value) : 0;
    const remaining = data.available - requested;

    if (requested > data.available) {
      badge.className = 'wiz-avail-badge wiz-avail-red';
      badge.textContent = 'Only ' + data.available + ' available';
    } else if (remaining < 5 && remaining >= 0) {
      badge.className = 'wiz-avail-badge wiz-avail-amber';
      badge.textContent = 'Only ' + remaining + ' left';
    } else {
      badge.className = 'wiz-avail-badge wiz-avail-green';
      badge.textContent = data.available + ' available';
    }
  }

  // SELECTED PANEL FUNCTIONS
  function addToSelectedPanel(itemId) {
    const row = document.getElementById('row-' + itemId);
    const itemName = row.querySelector('label > div > div').textContent;
    const panel = document.getElementById('selected-panel');
    const list = document.getElementById('selected-list');

    panel.classList.remove('wiz-selected-panel-empty');

    const div = document.createElement('div');
    div.id = 'selected-' + itemId;
    div.style.cssText = 'display:flex;align-items:center;gap:10px;padding:8px;background:#fff;border:1px solid #f0ece8;border-radius:6px;margin-bottom:6px';
    div.innerHTML = `
      <span style="flex:1;font-size:12px;font-weight:600;color:#0f0f0f">${itemName}</span>
      <input type="number" id="selected-qty-${itemId}" min="1" value="1" oninput="syncQty(${itemId}, this.value)" style="width:60px;padding:4px 8px;border:1px solid #ece8e3;border-radius:4px;font-size:11px">
      <button type="button" onclick="uncheckItem(${itemId})" style="background:none;border:none;color:#A32D2D;cursor:pointer;padding:4px">
        <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4l8 8M12 4l-8 8"/></svg>
      </button>
    `;
    list.appendChild(div);
  }

  function removeFromSelectedPanel(itemId) {
    const item = document.getElementById('selected-' + itemId);
    if (item) item.remove();

    if (selectedItems.size === 0) {
      document.getElementById('selected-panel').classList.add('wiz-selected-panel-empty');
    }
  }

  function updateSelectedPanelQty(itemId, qty) {
    const input = document.getElementById('selected-qty-' + itemId);
    if (input) input.value = qty;
  }

  window.syncQty = function(itemId, value) {
    const mainInput = document.getElementById('qty-input-' + itemId);
    if (mainInput) {
      mainInput.value = value;
      handleQtyChange(itemId);
    }
  };

  window.uncheckItem = function(itemId) {
    const checkbox = document.getElementById('check-' + itemId);
    if (checkbox) {
      checkbox.checked = false;
      handleItemCheck(itemId);
    }
  };

  function updateOwnBadge() {
    const badge = document.getElementById('own-badge');
    const count = document.getElementById('selected-count');
    const clearBtn = document.getElementById('clear-all-btn');

    badge.textContent = selectedItems.size;
    count.textContent = selectedItems.size;

    // Show/hide Clear All button
    if (clearBtn) {
      clearBtn.style.display = selectedItems.size > 0 ? 'inline-flex' : 'none';
    }
  }

  // Clear All Items function
  window.clearAllItems = function() {
    if (!confirm('Are you sure you want to clear all selected items?')) {
      return;
    }

    // Clone the set to avoid modification during iteration
    const itemsToUncheck = Array.from(selectedItems);

    itemsToUncheck.forEach(itemId => {
      const checkbox = document.getElementById('check-' + itemId);
      if (checkbox && checkbox.checked) {
        checkbox.checked = false;
        handleItemCheck(itemId);
      }
    });
  };

  // Initialize selected items from pre-checked checkboxes
  document.querySelectorAll('.wiz-item-checkbox:checked').forEach(cb => {
    const itemId = parseInt(cb.getAttribute('data-item-id'));
    selectedItems.add(itemId);
    addToSelectedPanel(itemId);
  });
  updateOwnBadge();
  validateAllQuantities(); // Validate quantities for pre-selected items

  // 5. CATEGORY ACCORDION
  window.toggleCategory = function(index) {
    const body = document.getElementById('cat-' + index);
    const arrow = document.getElementById('arrow-' + index);
    body.classList.toggle('collapsed');
    arrow.style.transform = body.classList.contains('collapsed') ? 'rotate(0deg)' : 'rotate(90deg)';
  };

  // 6. BORROWED ITEMS TOGGLE
  window.toggleBorrowed = function() {
    const toggle = document.getElementById('borrowed-toggle');
    const form = document.getElementById('borrowed-form');
    const muted = document.getElementById('borrowed-muted');
    const enabled = document.getElementById('borrowed-enabled');

    if (toggle.checked) {
      form.style.display = 'block';
      muted.style.display = 'none';
      enabled.value = '1';

      // Add first row if none exist
      if (document.querySelectorAll('#borrowed-rows .wiz-borrowed-row').length === 0) {
        addBorrowedRow();
      }
    } else {
      form.style.display = 'none';
      muted.style.display = 'block';
      enabled.value = '0';
    }
  };

  window.addBorrowedRow = function() {
    const container = document.getElementById('borrowed-rows');
    const index = borrowedRowCount++;

    const row = document.createElement('div');
    row.className = 'wiz-borrowed-row';
    row.innerHTML = `
      <input type="text" name="borrowed[${index}][item_name]" placeholder="Item description *" class="wiz-input" style="flex:2" required>
      <input type="text" name="borrowed[${index}][source_company]" placeholder="Source company" class="wiz-input" style="flex:1">
      <input type="number" name="borrowed[${index}][quantity]" placeholder="Qty" class="wiz-input" style="width:80px" min="1" value="1" required>
      <input type="text" name="borrowed[${index}][notes]" placeholder="Notes" class="wiz-input" style="flex:1">
      <button type="button" class="wiz-btn-icon-danger" onclick="removeBorrowedRow(this)">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 4l8 8M12 4l-8 8"/></svg>
      </button>
    `;
    container.appendChild(row);
  };

  window.removeBorrowedRow = function(btn) {
    const rows = document.querySelectorAll('#borrowed-rows .wiz-borrowed-row');
    if (rows.length > 1) {
      btn.closest('.wiz-borrowed-row').remove();
    }
  };

  // 7. OPERATIONAL ITEMS
  let selectedOpItems = new Set();
  let customOpCount = 1000;

  // Category accordion for operational items
  window.toggleOpCategory = function(index) {
    const body = document.getElementById('op-cat-' + index);
    const arrow = document.getElementById('op-arrow-' + index);

    if (!body || !arrow) {
      console.error('Accordion elements not found for index:', index);
      return;
    }

    body.classList.toggle('collapsed');
    arrow.style.transform = body.classList.contains('collapsed') ? 'rotate(0deg)' : 'rotate(90deg)';
  };

  // Handle operational item checkbox
  window.handleOpItemCheck = function(opId) {
    const checkbox = document.getElementById('op-check-' + opId);
    const row = document.getElementById('op-row-' + opId);
    const qtyDiv = document.getElementById('op-qty-' + opId);
    const qtyInput = document.getElementById('op-qty-input-' + opId);
    const hiddenInput = document.getElementById('op-hidden-' + opId);

    if (checkbox.checked) {
      selectedOpItems.add(opId);
      row.classList.add('wiz-item-row-checked');
      qtyDiv.style.display = 'flex';
      if (qtyInput) qtyInput.disabled = false;
      if (hiddenInput) hiddenInput.disabled = false;
      addToOpSelectedPanel(opId);
    } else {
      selectedOpItems.delete(opId);
      row.classList.remove('wiz-item-row-checked');
      qtyDiv.style.display = 'none';
      if (qtyInput) qtyInput.disabled = true;
      if (hiddenInput) hiddenInput.disabled = true;
      removeFromOpSelectedPanel(opId);
    }

    updateOpBadge();
  };

  // Handle operational item quantity change
  window.handleOpQtyChange = function(opId) {
    const input = document.getElementById('op-qty-input-' + opId);
    const qty = parseInt(input.value) || 1;
    updateOpSelectedPanelQty(opId, qty);
  };

  // Add to operational selected panel
  function addToOpSelectedPanel(opId) {
    const row = document.getElementById('op-row-' + opId);
    if (!row) {
      console.error('Row not found for opId:', opId);
      return;
    }

    const itemNameEl = row.querySelector('label > div > div');
    if (!itemNameEl) {
      console.error('Item name element not found for opId:', opId);
      return;
    }

    const itemName = itemNameEl.textContent;
    const panel = document.getElementById('op-selected-panel');
    const list = document.getElementById('op-selected-list');

    panel.classList.remove('wiz-selected-panel-empty');

    // Get current quantity from the main input
    const mainQtyInput = document.getElementById('op-qty-input-' + opId);
    const currentQty = mainQtyInput ? parseInt(mainQtyInput.value) || 1 : 1;

    const div = document.createElement('div');
    div.id = 'op-selected-' + opId;
    div.style.cssText = 'display:flex;align-items:center;gap:10px;padding:8px;background:#fff;border:1px solid #f0ece8;border-radius:6px;margin-bottom:6px';
    div.innerHTML = `
      <span style="flex:1;font-size:12px;font-weight:600;color:#0f0f0f">${itemName}</span>
      <input type="number" id="op-selected-qty-${opId}" min="1" value="${currentQty}" oninput="syncOpQty(${opId}, this.value)" style="width:60px;padding:4px 8px;border:1px solid #ece8e3;border-radius:4px;font-size:11px">
      <button type="button" onclick="uncheckOpItem(${opId})" style="background:none;border:none;color:#A32D2D;cursor:pointer;padding:4px">
        <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4l8 8M12 4l-8 8"/></svg>
      </button>
    `;
    list.appendChild(div);
  }

  // Remove from operational selected panel
  function removeFromOpSelectedPanel(opId) {
    const item = document.getElementById('op-selected-' + opId);
    if (item) item.remove();

    if (selectedOpItems.size === 0) {
      document.getElementById('op-selected-panel').classList.add('wiz-selected-panel-empty');
    }
  }

  // Update operational selected panel quantity
  function updateOpSelectedPanelQty(opId, qty) {
    const input = document.getElementById('op-selected-qty-' + opId);
    if (input) input.value = qty;
  }

  // Sync quantity between selected panel and main list
  window.syncOpQty = function(opId, value) {
    const mainInput = document.getElementById('op-qty-input-' + opId);
    if (mainInput) {
      mainInput.value = value;
    }
  };

  // Uncheck operational item from selected panel
  window.uncheckOpItem = function(opId) {
    const checkbox = document.getElementById('op-check-' + opId);
    if (checkbox) {
      checkbox.checked = false;
      handleOpItemCheck(opId);
    }
  };

  // Update operational badge
  function updateOpBadge() {
    const count = document.getElementById('op-selected-count');
    count.textContent = selectedOpItems.size;
  }

  // Force reset form state to prevent browser autocomplete issues
  // This ensures checkboxes match the server-rendered state
  document.querySelectorAll('[id^="op-check-"]').forEach(cb => {
    const row = document.getElementById('op-row-' + cb.getAttribute('data-op-id'));
    const qtyDiv = document.getElementById('op-qty-' + cb.getAttribute('data-op-id'));

    // If checkbox doesn't have the 'checked' attribute in HTML but browser checked it, uncheck it
    if (!cb.hasAttribute('checked') && cb.checked) {
      cb.checked = false;
      if (row) row.classList.remove('wiz-item-row-checked');
      if (qtyDiv) qtyDiv.style.display = 'none';
    }
  });

  // Initialize arrow rotation for operational categories on page load
  document.querySelectorAll('#op-search-results-container .wiz-cat-group').forEach((catGroup, index) => {
    const catBody = catGroup.querySelector('.wiz-cat-body');
    const arrow = catGroup.querySelector('.wiz-cat-arrow');

    if (catBody && arrow) {
      if (catBody.classList.contains('collapsed')) {
        arrow.style.transform = 'rotate(0deg)';
      } else {
        arrow.style.transform = 'rotate(90deg)';
      }
    }
  });

  // Initialize selected operational items from pre-checked checkboxes
  const preCheckedOp = document.querySelectorAll('[id^="op-check-"]:checked');
  console.log('Pre-checked operational items:', preCheckedOp.length);

  preCheckedOp.forEach(cb => {
    const opId = parseInt(cb.getAttribute('data-op-id'));
    console.log('Initializing op item:', opId);
    selectedOpItems.add(opId);
    addToOpSelectedPanel(opId);
  });
  updateOpBadge();

  // Add custom operational item
  window.showCustomOpForm = function() {
    const name = prompt('Enter custom item name:');
    if (!name || name.trim() === '') return;

    const container = document.getElementById('custom-op-container');

    // Add hidden inputs for custom item
    const wrapper = document.createElement('div');
    wrapper.innerHTML = `
      <input type="hidden" name="operational[${customOpCount}][custom_name]" value="${name.trim()}">
      <input type="hidden" name="operational[${customOpCount}][quantity]" value="1">
    `;
    container.appendChild(wrapper);

    customOpCount++;
    alert('Custom item "' + name + '" added to packing list.');
  };

  // 8. FORM SUBMIT GUARD
  document.getElementById('packing-form').addEventListener('submit', function(e) {
    const ownItems = selectedItems.size;
    const borrowedEnabled = document.getElementById('borrowed-toggle').checked;
    const opItems = selectedOpItems.size;
    const customOpItems = document.querySelectorAll('#custom-op-container input[type="hidden"]').length / 2;

    if (ownItems === 0 && !borrowedEnabled && opItems === 0 && customOpItems === 0) {
      e.preventDefault();
      alert('Please add at least one item to the packing list before continuing.');
      return false;
    }

    // Check no quantity exceeds available
    let hasError = false;
    selectedItems.forEach(itemId => {
      const input = document.getElementById('qty-input-' + itemId);
      const data = availabilityData[itemId];
      if (data && input && parseInt(input.value) > data.available) {
        hasError = true;
      }
    });

    if (hasError) {
      e.preventDefault();
      alert('Some quantities exceed available stock. Please adjust the quantities before continuing.');
      return false;
    }

    // Disable submit button
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="animation:wizSpin 0.8s linear infinite"><path d="M8 1.5a6.5 6.5 0 1 1-4.6 1.9"/></svg> Saving...';
  });

})();
</script>

@endsection
