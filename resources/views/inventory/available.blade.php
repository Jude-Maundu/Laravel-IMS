@extends('layouts.app')
@section('title', 'Available Items')
@section('page-title', 'Inventory')

@section('content')

@php
  $currentStatus   = 'Available'; // Force status to Available
  $currentSearch   = request('search', '');
  $currentCategory = request('category', '');
  $currentLocation = request('location', '');
  $currentSort     = request('sort', 'last_updated_at');
@endphp

{{-- PAGE HEADER --}}
<div class="inv-header">
  <div class="inv-header-left">
    <h1 class="inv-title">Available Items</h1>
    <p class="inv-subtitle">{{ number_format($availableCount) }} items currently available &middot; Grey Apple Events Warehouse</p>
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
            <svg width="24" height="24" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="2" width="12" height="12" rx="2"/><circle cx="8" cy="7" r="2"/><path d="M21 15l-5-5L5 21"/></svg>
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

{{-- FILTER BAR --}}
<form method="GET" action="{{ route('inventory.available') }}" id="inv-filter-form">
  <div class="inv-filter-bar">

    <div class="inv-search-box">
      <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="#b0a8a0" stroke-width="1.5" stroke-linecap="round"><circle cx="7" cy="7" r="4.5"/><line x1="10.5" y1="10.5" x2="14" y2="14"/></svg>
      <input type="text" name="search" value="{{ $currentSearch }}"
             placeholder="Search available items..."
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
      <option value="category"        {{ $currentSort==='category'?'selected':'' }}>Sort: Category</option>
    </select>

    @if($currentSearch || $currentCategory || $currentLocation)
      <a href="{{ route('inventory.available') }}" class="inv-clear-btn">Clear filters</a>
    @endif

    <div style="flex:1"></div>

    <span class="inv-result-count">{{ $items->total() }} available items</span>
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
  <div class="inv-empty">
    <svg width="48" height="48" viewBox="0 0 16 16" fill="none" stroke="#d0c8c0" stroke-width="1" stroke-linecap="round"><rect x="2" y="2" width="12" height="12" rx="2"/><circle cx="8" cy="7" r="2"/><path d="M2 12c1-2 3-3 6-3s5 1 6 3"/></svg>
    <h3>No Available Items</h3>
    <p>All items are currently assigned to events or under maintenance.</p>
    <a href="{{ route('inventory.index') }}" class="inv-btn-primary" style="margin-top:16px">View All Items</a>
  </div>
  @endforelse
</div>

{{-- PAGINATION --}}
@if($items->hasPages())
<div class="inv-pagination">
  {{ $items->appends(request()->query())->links() }}
</div>
@endif

@endsection

@push('scripts')
<script>
function clearSearch() {
  document.getElementById('inv-search-input').value = '';
  document.getElementById('inv-filter-form').submit();
}

function openAddModal() {
  document.getElementById('add-modal').style.display = 'flex';
  document.getElementById('modal-choices').style.display = 'block';
  document.getElementById('modal-cat-form').style.display = 'none';
}

function closeAddModal() {
  document.getElementById('add-modal').style.display = 'none';
}

function showCategoryForm() {
  document.getElementById('modal-choices').style.display = 'none';
  document.getElementById('modal-cat-form').style.display = 'block';
}

function showChoices() {
  document.getElementById('modal-cat-form').style.display = 'none';
  document.getElementById('modal-choices').style.display = 'block';
}
</script>
@endpush