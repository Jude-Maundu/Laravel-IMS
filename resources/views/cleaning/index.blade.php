@extends('layouts.app')
@section('title', 'Cleaning Bay')
@section('page-title', 'Inventory')

@section('content')

{{-- BREADCRUMB --}}
<div class="itd-breadcrumb">
  <a href="{{ route('inventory.index') }}" class="itd-bc-link">
    <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M10 3L6 8l4 5"/></svg>
    Inventory
  </a>
  <span class="itd-bc-sep">/</span>
  <span class="itd-bc-cur">Cleaning Bay</span>
</div>

{{-- PAGE HEADER --}}
<div class="inv-header">
  <div class="inv-header-left">
    <h1 class="inv-title">Cleaning Bay</h1>
    <p class="inv-subtitle">{{ $items->total() }} items currently being cleaned &middot; Grey Apple Events</p>
  </div>
</div>

{{-- SUCCESS / ERROR FLASH --}}
@if(session('success'))
  <div class="inv-flash inv-flash-success">
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 8l2 2 5-5"/><circle cx="8" cy="8" r="6.5"/></svg>
    {{ session('success') }}
  </div>
@endif

{{-- FILTER BAR --}}
<form method="GET" action="{{ route('cleaning.index') }}" id="cln-filter-form" style="margin-top: 20px;">
  <div class="inv-filter-bar">
    <div class="inv-search-box" style="width: 320px;">
      <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="#b0a8a0" stroke-width="1.5" stroke-linecap="round"><circle cx="7" cy="7" r="4.5"/><line x1="10.5" y1="10.5" x2="14" y2="14"/></svg>
      <input type="text" name="search" value="{{ $search ?? '' }}"
             placeholder="Search items in cleaning..."
             class="inv-search-input"
             onchange="this.form.submit()">
    </div>

    @if(!empty($search))
      <a href="{{ route('cleaning.index') }}" class="inv-clear-btn">Clear search</a>
    @endif

    <div style="flex:1"></div>
    <span class="inv-result-count">{{ $items->total() }} items found</span>
  </div>
</form>

{{-- BULK ACTION BAR (Sticky/Floating) --}}
<div id="bulk-action-bar" class="cln-bulk-bar" style="display: none;">
  <div class="cln-bulk-info">
    <span id="bulk-count" class="cln-bulk-count">0</span> items selected
  </div>
  <div class="cln-bulk-actions">
    <button type="button" class="inv-clear-btn" onclick="clearSelection()" style="color: #6a6560; background: #fff; padding: 6px 12px; border-radius: 6px; border: 1px solid #ece8e3;">Cancel</button>
    <button type="button" class="cln-btn-primary" onclick="submitBulkComplete()">
      <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 8l2 2 5-5"/><circle cx="8" cy="8" r="6.5"/></svg>
      Mark Selected as Cleaned
    </button>
  </div>
</div>

{{-- TABLE VIEW --}}
<form id="bulk-complete-form" method="POST" action="{{ route('cleaning.bulkComplete') }}">
  @csrf
  <div class="inv-table-wrap">
    <table class="inv-table">
      <thead>
        <tr>
          <th style="width: 40px; text-align: center; padding-right: 0;">
            <div class="cln-checkbox-wrap">
              <input type="checkbox" id="selectAll" class="cln-checkbox" onchange="toggleSelectAll(this)">
            </div>
          </th>
          <th>Item ID</th>
          <th>Item Name</th>
          <th>Category</th>
          <th>Since</th>
          <th>Status</th>
          <th style="text-align:right">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($items as $item)
        <tr class="cln-row" onclick="toggleRowSelection(event, this)">
          <td style="text-align: center; padding-right: 0;" onclick="event.stopPropagation()">
            <div class="cln-checkbox-wrap">
              <input type="checkbox" name="item_ids[]" value="{{ $item->id }}" class="cln-checkbox cln-item-checkbox" onchange="updateSelection()">
            </div>
          </td>
          <td style="font-weight:600;color:#CC0000">#ITM-{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</td>
          <td>
            <div style="display:flex;align-items:center;gap:10px">
              <div style="width:32px;height:32px;background:#f0ece8;border-radius:4px;overflow:hidden;display:flex;align-items:center;justify-content:center">
                @if($item->primaryImageUrl)
                  <img src="{{ $item->primaryImageUrl }}" style="width:100%;height:100%;object-fit:cover">
                @else
                  <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#a09890" stroke-width="1"><rect x="2" y="2" width="12" height="12" rx="2"/><circle cx="8" cy="7" r="2"/><path d="M2 12c1-2 3-3 6-3s5 1 6 3"/></svg>
                @endif
              </div>
              <span>{{ $item->name }}</span>
            </div>
          </td>
          <td>{{ $item->category }}</td>
          <td>{{ $item->last_updated_at ? $item->last_updated_at->diffForHumans() : '—' }}</td>
          <td><x-ui.status-badge :status="$item->status" /></td>
          <td style="text-align:right">
            <button type="submit" form="single-complete-{{ $item->id }}" class="inv-btn-outline" style="padding:4px 10px;font-size:11px" onclick="event.stopPropagation()">
              Mark as Cleaned
            </button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" style="text-align:center;padding:48px;color:#a09890">
            <div style="margin-bottom:10px">
              <svg width="32" height="32" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"><path d="M13 10V4h-3M3 10V4h3M8 1v14"/></svg>
            </div>
            <p style="font-size:13px;font-weight:600;margin:0;color:#5c5550">Cleaning bay is empty</p>
            <p style="font-size:11px;margin:4px 0 0">{{ empty($search) ? 'All items are currently available or in use.' : 'No items match your search criteria.' }}</p>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</form>

{{-- Hidden forms for single complete actions --}}
@foreach($items as $item)
  <form id="single-complete-{{ $item->id }}" method="POST" action="{{ route('cleaning.complete', $item) }}" style="display:none;">
    @csrf
  </form>
@endforeach

{{-- PAGINATION --}}
@if($items->hasPages())
<div class="inv-pagination">
  {{ $items->links() }}
</div>
@endif

<style>
/* Original Table Styles + Selection Highlights */
.inv-table-wrap {
  background: #fff;
  border: 1px solid #ece8e3;
  border-radius: 10px;
  overflow: hidden;
  margin-top: 20px;
}
.inv-table {
  width: 100%;
  border-collapse: collapse;
}
.inv-table th {
  text-align: left;
  font-size: 9px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #a09890;
  background: #faf8f6;
  padding: 12px 16px;
  border-bottom: 1px solid #f0ece8;
}
.inv-table td {
  padding: 12px 16px;
  font-size: 12px;
  color: #3a3530;
  border-bottom: 1px solid #f8f6f3;
}
.cln-row {
  cursor: pointer;
  transition: background-color 0.15s ease;
}
.cln-row:hover {
  background: #fdf9f8;
}
.cln-row.selected {
  background: #f0f7ff;
}
.cln-row.selected td {
  border-bottom-color: #e0efff;
}

/* Custom Checkbox */
.cln-checkbox-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
}
.cln-checkbox {
  appearance: none;
  width: 16px;
  height: 16px;
  border: 1px solid #c0b8b0;
  border-radius: 4px;
  background: #fff;
  cursor: pointer;
  position: relative;
  transition: all 0.2s ease;
  margin: 0;
}
.cln-checkbox:hover {
  border-color: #185FA5;
}
.cln-checkbox:checked {
  background: #185FA5;
  border-color: #185FA5;
}
.cln-checkbox:checked::after {
  content: '';
  position: absolute;
  top: 3px;
  left: 5px;
  width: 4px;
  height: 8px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

/* Bulk Action Bar */
.cln-bulk-bar {
  position: fixed;
  bottom: 30px;
  left: 50%;
  transform: translateX(-50%);
  background: #1a1a1a;
  color: #fff;
  padding: 12px 20px;
  border-radius: 10px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2), 0 4px 12px rgba(0,0,0,0.1);
  display: flex;
  align-items: center;
  gap: 24px;
  z-index: 1000;
  animation: slideUp 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
@keyframes slideUp {
  from { opacity: 0; transform: translate(-50%, 20px); }
  to { opacity: 1; transform: translate(-50%, 0); }
}
.cln-bulk-info {
  font-size: 14px;
  font-weight: 500;
}
.cln-bulk-count {
  background: rgba(255,255,255,0.2);
  padding: 2px 8px;
  border-radius: 12px;
  font-weight: 700;
  margin-right: 4px;
}
.cln-bulk-actions {
  display: flex;
  gap: 10px;
}
.cln-btn-primary {
  background: #0F6E56;
  color: #fff;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: background 0.2s;
}
.cln-btn-primary:hover {
  background: #0b5441;
}
</style>

<script>
function toggleSelectAll(source) {
  const checkboxes = document.querySelectorAll('.cln-item-checkbox');
  checkboxes.forEach(cb => {
    cb.checked = source.checked;
    updateRowStyling(cb);
  });
  updateSelection();
}

function toggleRowSelection(event, row) {
  // Ignore clicks on buttons, inputs, or links inside the row
  if (['INPUT', 'BUTTON', 'A'].includes(event.target.tagName)) return;
  
  const checkbox = row.querySelector('.cln-item-checkbox');
  if (checkbox) {
    checkbox.checked = !checkbox.checked;
    updateRowStyling(checkbox);
    updateSelection();
  }
}

function updateRowStyling(checkbox) {
  const row = checkbox.closest('tr');
  if (checkbox.checked) {
    row.classList.add('selected');
  } else {
    row.classList.remove('selected');
  }
}

function updateSelection() {
  const checkboxes = document.querySelectorAll('.cln-item-checkbox');
  let checkedCount = 0;
  
  checkboxes.forEach(cb => {
    if (cb.checked) checkedCount++;
    updateRowStyling(cb);
  });
  
  const selectAll = document.getElementById('selectAll');
  if (selectAll) {
    selectAll.checked = (checkedCount === checkboxes.length && checkboxes.length > 0);
  }
  
  const bulkBar = document.getElementById('bulk-action-bar');
  const bulkCount = document.getElementById('bulk-count');
  
  if (checkedCount > 0) {
    bulkCount.textContent = checkedCount;
    if (bulkBar.style.display === 'none') {
      bulkBar.style.display = 'flex';
    }
  } else {
    bulkBar.style.display = 'none';
  }
}

function clearSelection() {
  const checkboxes = document.querySelectorAll('.cln-item-checkbox');
  checkboxes.forEach(cb => {
    cb.checked = false;
    updateRowStyling(cb);
  });
  const selectAll = document.getElementById('selectAll');
  if(selectAll) selectAll.checked = false;
  
  updateSelection();
}

function submitBulkComplete() {
  const count = document.querySelectorAll('.cln-item-checkbox:checked').length;
  if (confirm(`Are you sure you want to mark ${count} items as cleaned and return them to the warehouse?`)) {
    document.getElementById('bulk-complete-form').submit();
  }
}
</script>

@endsection
