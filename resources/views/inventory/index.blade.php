@extends('layouts.app')
@section('title', 'All Items')
@section('page-title', 'Inventory')

@section('content')

@php
  $currentStatus   = request('status', '');
  $currentSearch   = request('search', '');
  $currentCategory = request('category', '');
  $currentLocation = request('location', '');
  $currentSort     = request('sort', 'last_updated_at');
@endphp

{{-- PAGE HEADER --}}
<div class="inv-header">
  <div class="inv-header-left">
    <h1 class="inv-title">All Items</h1>
    <p class="inv-subtitle">{{ number_format($totalItems) }} total items &middot; Grey Apple Events Warehouse</p>
  </div>
  <div class="inv-header-right">
    <a href="{{ route('reports.inventory') }}" class="inv-btn-outline">
      <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M2 4h12M4 8h8M6 12h4"/></svg>
      Export
    </a>
    <button type="button" onclick="openAddModal()" class="inv-btn-primary">
      <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="6.5"/><line x1="8" y1="5" x2="8" y2="11"/><line x1="5" y1="8" x2="11" y2="8"/></svg>
      Add New
    </button>
  </div>
</div>

{{-- ADD MODAL --}}
<div id="add-modal" class="ga-modal-overlay" style="display:none">
  <div class="ga-modal-card">
    <div class="ga-modal-head">
      <h3 class="ga-modal-title">Inventory Options</h3>
      <button type="button" onclick="closeAddModal()" class="ga-modal-close">&times;</button>
    </div>
    
    {{-- CHOICE VIEW --}}
    <div id="modal-choices" class="ga-modal-body">
      <p class="ga-modal-sub">Choose an action to proceed</p>
      <div class="ga-choice-grid">
        <a href="{{ route('inventory.create') }}" class="ga-choice-card">
          <div class="ga-choice-icon ga-ci-red">
            <svg width="24" height="24" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="2" width="12" height="12" rx="2"/><circle cx="8" cy="7" r="2"/><path d="M2 12c1-2 3-3 6-3s5 1 6 3"/></svg>
          </div>
          <div class="ga-choice-info">
            <strong>Create New Item</strong>
            <span>Add a physical asset to your warehouse</span>
          </div>
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#d0c8c0" stroke-width="1.5" stroke-linecap="round"><path d="M6 3l5 5-5 5"/></svg>
        </a>

        <button type="button" onclick="showCategoryForm()" class="ga-choice-card">
          <div class="ga-choice-icon ga-ci-blue">
            <svg width="24" height="24" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M2 4h12M4 8h8M6 12h4"/></svg>
          </div>
          <div class="ga-choice-info">
            <strong>Create Category</strong>
            <span>Define a new group for item classification</span>
          </div>
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#d0c8c0" stroke-width="1.5" stroke-linecap="round"><path d="M6 3l5 5-5 5"/></svg>
        </button>
      </div>
    </div>

    {{-- CATEGORY FORM VIEW --}}
    <div id="modal-cat-form" class="ga-modal-body" style="display:none">
      <div style="margin-bottom:16px">
        <button type="button" onclick="showChoices()" class="inv-clear-btn" style="padding:0;font-size:12px;display:inline-flex;align-items:center;gap:4px">
          <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M10 3L6 8l4 5"/></svg>
          Back to options
        </button>
      </div>
      <form method="POST" action="{{ route('inventory.category.store') }}">
        @csrf
        <div class="wiz-form-group">
          <label class="wiz-label">Category Name <span class="wiz-req">*</span></label>
          <input type="text" name="name" class="wiz-input" placeholder="e.g. Lighting, Sound Systems..." required autofocus>
          <p style="font-size:10px;color:#a09890;margin-top:6px">This category will be available for selection when creating or editing items.</p>
        </div>
        <div style="margin-top:20px;display:flex;justify-content:flex-end;gap:10px">
          <button type="button" onclick="closeAddModal()" class="inv-btn-outline">Cancel</button>
          <button type="submit" class="inv-btn-primary">Save Category</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.ga-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.4);
  backdrop-filter: blur(2px);
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}
.ga-modal-card {
  background: #fff;
  width: 100%;
  max-width: 460px;
  border-radius: 14px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.15), 0 5px 15px rgba(0,0,0,0.05);
  overflow: hidden;
  animation: gaModalFade 0.2s ease-out;
}
@keyframes gaModalFade {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.ga-modal-head {
  padding: 16px 20px;
  border-bottom: 1px solid #f5f1ed;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.ga-modal-title {
  font-size: 15px;
  font-weight: 700;
  color: #0f0f0f;
  margin: 0;
}
.ga-modal-close {
  background: none;
  border: none;
  font-size: 20px;
  color: #b0a8a0;
  cursor: pointer;
  padding: 0;
  line-height: 1;
}
.ga-modal-close:hover { color: #CC0000; }
.ga-modal-body { padding: 20px; }
.ga-modal-sub {
  font-size: 12px;
  color: #a09890;
  margin: 0 0 16px;
}
.ga-choice-grid {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.ga-choice-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px;
  border: 1px solid #ece8e3;
  border-radius: 12px;
  background: #fff;
  cursor: pointer;
  text-align: left;
  transition: all 0.15s;
  width: 100%;
}
.ga-choice-card:hover {
  border-color: #CC0000;
  background: #fff8f8;
  transform: translateY(-1px);
}
.ga-choice-icon {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.ga-ci-red { background: #fff0f0; color: #CC0000; }
.ga-ci-blue { background: #e6f1fb; color: #185FA5; }
.ga-choice-info { flex: 1; min-width: 0; }
.ga-choice-info strong {
  display: block;
  font-size: 13px;
  color: #0f0f0f;
  margin-bottom: 2px;
}
.ga-choice-info span {
  display: block;
  font-size: 11px;
  color: #a09890;
}
</style>

<script>
function openAddModal() {
  document.getElementById('add-modal').style.display = 'flex';
  showChoices();
}
function closeAddModal() {
  document.getElementById('add-modal').style.display = 'none';
}
function showCategoryForm() {
  document.getElementById('modal-choices').style.display = 'none';
  document.getElementById('modal-cat-form').style.display = 'block';
}
function showChoices() {
  document.getElementById('modal-choices').style.display = 'block';
  document.getElementById('modal-cat-form').style.display = 'none';
}
// Close on outside click
document.getElementById('add-modal').addEventListener('click', function(e) {
  if (e.target === this) closeAddModal();
});

// SEARCH FUNCTIONALITY
let searchTimeout;
const searchInput = document.getElementById('inv-search-input');

if (searchInput) {
  searchInput.addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      performSearch(e.target.value);
    }, 200);
  });
}

function performSearch(query) {
  const cards = document.querySelectorAll('.inv-card');
  const grid = document.querySelector('.inv-grid');
  const resultCount = document.querySelector('.inv-result-count');
  const normalizedQuery = query.toLowerCase().trim();

  if (!normalizedQuery) {
    cards.forEach(card => card.style.display = '');
    if (resultCount) resultCount.textContent = '{{ $items->total() }} items';
    return;
  }

  let visibleCount = 0;

  cards.forEach(card => {
    const name = card.querySelector('.inv-card-name')?.textContent.toLowerCase() || '';
    const category = card.querySelector('.inv-card-cat')?.textContent.toLowerCase() || '';
    const location = card.querySelector('.inv-card-loc')?.textContent.toLowerCase() || '';

    const matches = name.includes(normalizedQuery) ||
                    category.includes(normalizedQuery) ||
                    location.includes(normalizedQuery);

    if (matches) {
      card.style.display = '';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });

  if (resultCount) {
    resultCount.textContent = visibleCount + ' item' + (visibleCount !== 1 ? 's' : '');
  }
}

function clearSearch() {
  const searchInput = document.getElementById('inv-search-input');
  if (searchInput) {
    searchInput.value = '';
    performSearch('');
    searchInput.focus();
  }
}
</script>

{{-- SUCCESS / ERROR FLASH --}}
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

{{-- FILTER BAR --}}
<form method="GET" action="{{ route('inventory.index') }}" id="inv-filter-form">
  <div class="inv-filter-bar">

    <div class="inv-search-box">
      <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="#b0a8a0" stroke-width="1.5" stroke-linecap="round"><circle cx="7" cy="7" r="4.5"/><line x1="10.5" y1="10.5" x2="14" y2="14"/></svg>
      <input type="text" name="search" value="{{ $currentSearch }}"
             placeholder="Search by name, category, ID..."
             class="inv-search-input"
             id="inv-search-input"
             autocomplete="off">
      @if($currentSearch)
      <button type="button" onclick="clearSearch()" class="inv-search-clear" title="Clear search">
        <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <path d="M4 4l8 8M12 4l-8 8"/>
        </svg>
      </button>
      @endif
    </div>

    <select name="category" class="inv-select" onchange="this.form.submit()">
      <option value="">All Categories</option>
      @foreach($categories as $cat)
        <option value="{{ $cat }}" {{ $currentCategory === $cat ? 'selected' : '' }}>{{ $cat }}</option>
      @endforeach
    </select>

    <select name="location" class="inv-select" onchange="this.form.submit()">
      <option value="">All Locations</option>
      @foreach($locations as $loc)
        <option value="{{ $loc }}" {{ $currentLocation === $loc ? 'selected' : '' }}>{{ $loc }}</option>
      @endforeach
    </select>

    <select name="sort" class="inv-select" onchange="this.form.submit()">
      <option value="last_updated_at" {{ $currentSort==='last_updated_at'?'selected':'' }}>Sort: Last Updated</option>
      <option value="name"            {{ $currentSort==='name'?'selected':'' }}>Sort: Name A–Z</option>
      <option value="status"          {{ $currentSort==='status'?'selected':'' }}>Sort: Status</option>
      <option value="category"        {{ $currentSort==='category'?'selected':'' }}>Sort: Category</option>
    </select>

    @if($currentSearch || $currentCategory || $currentLocation || $currentStatus)
      <a href="{{ route('inventory.index') }}" class="inv-clear-btn">Clear filters</a>
    @endif

    <div style="flex:1"></div>

    <span class="inv-result-count">{{ $items->total() }} items</span>
  </div>

  {{-- STATUS TABS --}}
  <div class="inv-status-tabs">
    @php
      $allStatuses = [
        '' => 'All',
        'Available' => 'Available',
        'Assigned' => 'Assigned',
        'In Use' => 'In Use',
        'Under Repair' => 'Under Repair',
        'Damaged' => 'Damaged',
        'Irreparable' => 'Irreparable',
      ];
    @endphp
    @foreach($allStatuses as $val => $label)
      <a href="{{ request()->fullUrlWithQuery(['status' => $val, 'page' => 1]) }}"
         class="inv-stab {{ $currentStatus === $val ? 'active' : '' }}">
        {{ $label }}
        <span class="inv-stab-count">
          @if($val === '')
            {{ $totalItems }}
          @else
            {{ $statusCounts[$val] ?? 0 }}
          @endif
        </span>
      </a>
    @endforeach
  </div>
</form>

{{-- GRID VIEW --}}
<div class="inv-grid">
  @forelse($items as $item)
  <div class="inv-card" onclick="window.location='{{ route('inventory.show', $item->id) }}'">
    <div class="inv-card-img">
      @php $cardImage = $item->primaryImageUrl; @endphp
      @if($cardImage)
        <img src="{{ $cardImage }}" alt="{{ $item->name }}">
      @else
        <svg width="32" height="32" viewBox="0 0 16 16" fill="none" stroke="#d0c8c0" stroke-width="1" stroke-linecap="round"><rect x="2" y="2" width="12" height="12" rx="2"/><circle cx="8" cy="7" r="2"/><path d="M2 12c1-2 3-3 6-3s5 1 6 3"/></svg>
      @endif
      <div class="inv-card-status-wrap">
        <x-ui.status-badge :status="$item->status" />
      </div>
    </div>
    <div class="inv-card-body">
      <div class="inv-card-name">{{ $item->name }}</div>
      <div class="inv-card-cat">{{ $item->category }}</div>
      @if(in_array($item->status, ['Assigned','In Use']))
        @php $currentEvent = $item->currentEvent(); @endphp
        @if($currentEvent)
        <div class="inv-card-assigned-ctx">
          <svg width="10" height="10" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="3" width="12" height="11" rx="2"/><line x1="5" y1="1" x2="5" y2="5"/><line x1="11" y1="1" x2="11" y2="5"/><line x1="2" y1="7" x2="14" y2="7"/></svg>
          {{ Str::limit($currentEvent->name, 28) }}
        </div>
        @endif
      @endif
      <div class="inv-card-meta">
        <span class="inv-card-loc">
          <svg width="10" height="10" viewBox="0 0 16 16" fill="none" stroke="#b0a8a0" stroke-width="1.5" stroke-linecap="round"><path d="M8 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path d="M8 14s5-3.5 5-7A5 5 0 0 0 3 7c0 3.5 5 7 5 7z"/></svg>
          {{ $item->location ?? 'Warehouse' }}
        </span>
        <span class="inv-card-ago">{{ $item->last_updated_at ? $item->last_updated_at->diffForHumans() : '—' }}</span>
      </div>
    </div>
  </div>
  @empty
  <div style="grid-column:1/-1;text-align:center;padding:40px;color:#a09890;font-size:13px">No items found</div>
  @endforelse
</div>

{{-- PAGINATION --}}
@if($items->hasPages())
<div class="inv-pagination">
  <span class="inv-pg-info">
    Showing {{ $items->firstItem() }}–{{ $items->lastItem() }} of {{ $items->total() }} items
  </span>
  <div class="inv-pg-links">
    @if($items->onFirstPage())
      <span class="inv-pg-btn inv-pg-disabled">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M7.5 2L4.5 6l3 4"/></svg>
      </span>
    @else
      <a href="{{ $items->previousPageUrl() }}" class="inv-pg-btn">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M7.5 2L4.5 6l3 4"/></svg>
      </a>
    @endif

    @foreach($items->getUrlRange(max(1, $items->currentPage()-2), min($items->lastPage(), $items->currentPage()+2)) as $page => $url)
      <a href="{{ $url }}" class="inv-pg-btn {{ $page == $items->currentPage() ? 'active' : '' }}">{{ $page }}</a>
    @endforeach

    @if($items->hasMorePages())
      <a href="{{ $items->nextPageUrl() }}" class="inv-pg-btn">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4.5 2L7.5 6l-3 4"/></svg>
      </a>
    @else
      <span class="inv-pg-btn inv-pg-disabled">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4.5 2L7.5 6l-3 4"/></svg>
      </span>
    @endif
  </div>
</div>
@endif

@endsection