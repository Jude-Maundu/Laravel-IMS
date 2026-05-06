@extends('layouts.app')
@section('title', 'Item Pieces Register')
@section('page-title', 'Inventory')

@section('content')

{{-- BREADCRUMB --}}
<div class="itd-breadcrumb">
  <a href="{{ route('inventory.index') }}" class="itd-bc-link">
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M10 3L6 8l4 5"/></svg>
    Inventory
  </a>
  <span class="itd-bc-sep">/</span>
  <span class="itd-bc-cur">Item Pieces Register</span>
</div>

{{-- FLASH --}}
@if(session('success'))
  <div class="inv-flash inv-flash-success">
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 8l2 2 5-5"/><circle cx="8" cy="8" r="6.5"/></svg>
    {{ session('success') }}
  </div>
@endif

{{-- HEADER --}}
<div class="inv-pc-header">
  <div>
    <h1 class="inv-pc-title">Item Pieces Register</h1>
    <p class="inv-pc-subtitle">Manage piece quantities and unique identifiers for all inventory items</p>
  </div>
  <div class="inv-pc-actions">
    <button type="button" class="inv-pc-btn-outline">
      <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M14 11v2H2v-2M8 10V2M5 6l3-3 3 3"/></svg>
      Export CSV
    </button>
    <button type="button" class="inv-pc-btn-red" onclick="openBulkEditModal()">
      <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M2 12l2.5-2.5m0 0L12 2l2 2L6.5 11.5m0 0L4 14l-2-2 2.5-2.5"/></svg>
      Update Quantities
    </button>
  </div>
</div>

{{-- FILTER BAR --}}
<div class="inv-pc-filters">
  <form method="GET" action="{{ route('inventory.pieces') }}" class="inv-pc-filter-form">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by item name or piece code..." class="inv-pc-search">

    <select name="category" class="inv-pc-select" onchange="this.form.submit()">
      <option value="">All Categories</option>
      @foreach($categories as $cat)
        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
      @endforeach
    </select>

    <select name="status" class="inv-pc-select" onchange="this.form.submit()">
      <option value="">All Statuses</option>
      @foreach($statuses as $stat)
        <option value="{{ $stat }}" {{ request('status') === $stat ? 'selected' : '' }}>{{ $stat }}</option>
      @endforeach
    </select>

    @if(request('search') || request('category') || request('status'))
      <a href="{{ route('inventory.pieces') }}" class="inv-pc-clear">Clear Filters</a>
    @endif
  </form>
</div>

{{-- TABLE --}}
<div class="inv-pc-table-wrap">
  <table class="inv-pc-table">
    <thead>
      <tr>
        <th>Item Name</th>
        <th>Category</th>
        <th>Total Pieces</th>
        <th>Available</th>
        <th>Assigned</th>
        <th>Other</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @php $currentCategory = null; @endphp
      @foreach($items as $item)
        @if($currentCategory !== $item->category)
          @php
            $currentCategory = $item->category;
            $categoryCount = $items->where('category', $item->category)->count();
          @endphp
          <tr class="inv-pc-category-row">
            <td colspan="7">
              <strong>{{ $currentCategory }}</strong>
              <span class="inv-pc-cat-count">({{ $categoryCount }} items)</span>
              <a href="{{ route('labels.byCategory', urlencode($currentCategory)) }}" target="_blank" style="margin-left:16px;font-size:11px;color:#CC0000;font-weight:600;text-decoration:none">
                Print Labels
              </a>
            </td>
          </tr>
        @endif
        <tr class="inv-pc-clickable-row" onclick="openEditModal({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->total_pieces }}, '{{ $item->category }}')">
          <td>
            <div class="inv-pc-item-name">{{ $item->name }}</div>
            <div class="inv-pc-item-cat">{{ $item->category }}</div>
          </td>
          <td>{{ $item->category }}</td>
          <td><strong>{{ $item->total_pieces }}</strong></td>
          <td><span class="inv-pc-badge inv-pc-badge-green">{{ $item->available_count }}</span></td>
          <td><span class="inv-pc-badge inv-pc-badge-blue">{{ $item->assigned_count }}</span></td>
          <td><span class="inv-pc-badge inv-pc-badge-amber">{{ $item->other_count }}</span></td>
          <td onclick="event.stopPropagation()">
            <div style="display:flex;gap:6px">
              <a href="{{ route('inventory.show', $item->id) }}#pieces" class="inv-pc-action-btn" title="View Pieces">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="8" cy="8" r="2"/><path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z"/></svg>
              </a>
              <button type="button" onclick="event.stopPropagation();previewItemQR({{ $item->id }}, '{{ addslashes($item->name) }}')" class="inv-pc-action-btn" title="Preview QR Codes">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                  <rect x="2" y="2" width="5" height="5" rx="1"/><rect x="9" y="2" width="5" height="5" rx="1"/><rect x="2" y="9" width="5" height="5" rx="1"/><rect x="9" y="9" width="5" height="5" rx="1"/>
                </svg>
              </button>
              <a href="{{ route('labels.byItem', $item) }}" target="_blank" class="inv-pc-action-btn" title="Print Labels">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                  <path d="M4 5V2h8v3M4 11H2V6h12v5h-2M5 9h6v5H5z"/>
                </svg>
              </a>
            </div>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

{{-- PAGINATION --}}
<div class="inv-pc-pagination">
  {{ $items->links() }}
</div>

{{-- SINGLE ITEM EDIT MODAL --}}
<div id="editModal" class="inv-pc-modal" style="display:none">
  <div class="inv-pc-modal-content inv-pc-single-modal">
    <div class="inv-pc-modal-header">
      <h3>Edit Item Pieces</h3>
      <button type="button" class="inv-pc-modal-close" onclick="closeEditModal()">&times;</button>
    </div>
    <form method="POST" action="{{ route('inventory.pieces.bulkUpdate') }}" id="editForm">
      @csrf
      <div class="inv-pc-modal-body">
        <div class="inv-pc-edit-info">
          <div class="inv-pc-edit-label">Item Name</div>
          <div class="inv-pc-edit-value" id="editItemName"></div>
        </div>
        <div class="inv-pc-edit-info">
          <div class="inv-pc-edit-label">Category</div>
          <div class="inv-pc-edit-value" id="editItemCategory"></div>
        </div>
        <div class="inv-pc-edit-info">
          <div class="inv-pc-edit-label">Current Quantity</div>
          <div class="inv-pc-edit-value" id="editItemCurrent"></div>
        </div>
        <div class="inv-pc-edit-field">
          <label class="inv-pc-edit-label" for="editItemQty">New Quantity</label>
          <input type="number" id="editItemQty" name="quantity" class="inv-pc-qty-input-large" min="1" required>
          <input type="hidden" id="editItemId" name="item_id">
          <small style="display:block;font-size:10px;color:#a09890;margin-top:6px">
            Increasing this will add new pieces. Decreasing will only update the total count without deleting existing pieces.
          </small>
        </div>
      </div>
      <div class="inv-pc-modal-footer">
        <button type="button" class="inv-pc-btn-outline" onclick="closeEditModal()">Cancel</button>
        <button type="submit" class="inv-pc-btn-red">Save Changes</button>
      </div>
    </form>
  </div>
</div>

{{-- BULK EDIT MODAL --}}
<div id="bulkEditModal" class="inv-pc-modal" style="display:none">
  <div class="inv-pc-modal-content">
    <div class="inv-pc-modal-header">
      <h3>Update Item Quantities</h3>
      <button type="button" class="inv-pc-modal-close" onclick="closeBulkEditModal()">&times;</button>
    </div>
    <div class="inv-pc-modal-body">
      <input type="text" id="modalSearch" placeholder="Search items..." class="inv-pc-search" onkeyup="filterModalItems()">

      <form method="POST" action="{{ route('inventory.pieces.bulkUpdate') }}" id="bulkEditForm">
        @csrf
        <div class="inv-pc-modal-table-wrap">
          <table class="inv-pc-modal-table">
            <thead>
              <tr>
                <th>Item Name</th>
                <th>Category</th>
                <th>Current Qty</th>
                <th>New Qty</th>
              </tr>
            </thead>
            <tbody id="modalTableBody">
              @foreach($items as $item)
                <tr class="modal-item-row" data-name="{{ strtolower($item->name) }}">
                  <td>{{ $item->name }}</td>
                  <td>{{ $item->category }}</td>
                  <td>{{ $item->total_pieces }}</td>
                  <td>
                    <input type="number" name="items[{{ $item->id }}]" value="{{ $item->total_pieces }}" min="1" class="inv-pc-qty-input">
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </form>
    </div>
    <div class="inv-pc-modal-footer">
      <button type="button" class="inv-pc-btn-outline" onclick="closeBulkEditModal()">Cancel</button>
      <button type="submit" form="bulkEditForm" class="inv-pc-btn-red">Save All Changes</button>
    </div>
  </div>
</div>

<script>
// Single item edit modal
function openEditModal(itemId, itemName, currentQty, category) {
  document.getElementById('editItemId').value = itemId;
  document.getElementById('editItemName').textContent = itemName;
  document.getElementById('editItemCategory').textContent = category;
  document.getElementById('editItemCurrent').textContent = currentQty;
  document.getElementById('editItemQty').value = currentQty;
  document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
  document.getElementById('editModal').style.display = 'none';
}

// Update the form submission to handle single item
document.getElementById('editForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const itemId = document.getElementById('editItemId').value;
  const quantity = document.getElementById('editItemQty').value;

  // Create a hidden form with the correct structure
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '{{ route("inventory.pieces.bulkUpdate") }}';

  const csrfInput = document.createElement('input');
  csrfInput.type = 'hidden';
  csrfInput.name = '_token';
  csrfInput.value = '{{ csrf_token() }}';
  form.appendChild(csrfInput);

  const qtyInput = document.createElement('input');
  qtyInput.type = 'hidden';
  qtyInput.name = 'items[' + itemId + ']';
  qtyInput.value = quantity;
  form.appendChild(qtyInput);

  document.body.appendChild(form);
  form.submit();
});

// Bulk edit modal
function openBulkEditModal() {
  document.getElementById('bulkEditModal').style.display = 'flex';
}

function closeBulkEditModal() {
  document.getElementById('bulkEditModal').style.display = 'none';
}

function filterModalItems() {
  const search = document.getElementById('modalSearch').value.toLowerCase();
  const rows = document.querySelectorAll('.modal-item-row');

  rows.forEach(row => {
    const name = row.getAttribute('data-name');
    row.style.display = name.includes(search) ? '' : 'none';
  });
}

// Close modals on outside click
document.getElementById('editModal').addEventListener('click', function(e) {
  if (e.target === this) closeEditModal();
});

document.getElementById('bulkEditModal').addEventListener('click', function(e) {
  if (e.target === this) closeBulkEditModal();
});

// QR Preview
function previewItemQR(itemId, itemName) {
  fetch(`/api/items/${itemId}/pieces-qr`)
    .then(res => res.json())
    .then(data => {
      const modal = document.getElementById('qrPreviewModal');
      document.getElementById('qrModalTitle').textContent = 'QR Codes Preview';
      document.getElementById('qrModalSubtitle').textContent = `${itemName} - ${data.pieces.length} piece${data.pieces.length === 1 ? '' : 's'}`;

      const grid = data.pieces.map(piece => `
        <div class="qr-preview-card">
          ${piece.qr_svg}
          <div class="qr-preview-code">${piece.unique_code}</div>
          <div class="qr-preview-status">${piece.status}</div>
        </div>
      `).join('');

      document.getElementById('qrModalGrid').innerHTML = grid;
      modal.style.display = 'flex';
    });
}

function closeQRModal() {
  document.getElementById('qrPreviewModal').style.display = 'none';
}

document.getElementById('qrPreviewModal').addEventListener('click', function(e) {
  if (e.target === this) closeQRModal();
});
</script>

{{-- QR PREVIEW MODAL --}}
<div id="qrPreviewModal" class="inv-pc-modal" style="display:none">
  <div style="background:#fff;border-radius:12px;max-width:1000px;width:100%;max-height:90vh;overflow:hidden;display:flex;flex-direction:column;margin:auto">
    {{-- HEADER --}}
    <div style="padding:20px;border-bottom:1px solid #ece8e3;display:flex;align-items:center;justify-content:space-between">
      <div>
        <h3 id="qrModalTitle" style="margin:0;font-size:16px;font-weight:700;color:#0f0f0f"></h3>
        <p id="qrModalSubtitle" style="margin:4px 0 0;font-size:12px;color:#a09890"></p>
      </div>
      <button type="button" onclick="closeQRModal()" class="inv-pc-modal-close">&times;</button>
    </div>

    {{-- BODY --}}
    <div style="padding:24px;overflow-y:auto;flex:1;background:#faf8f6">
      <div id="qrModalGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px"></div>
    </div>

    {{-- FOOTER --}}
    <div style="padding:16px 20px;border-top:1px solid #ece8e3;background:#fff;text-align:right">
      <button type="button" onclick="closeQRModal()" class="inv-pc-btn-outline">Close</button>
    </div>
  </div>
</div>

<style>
.qr-preview-card {
  background:#fff;
  border:1px solid #ece8e3;
  border-radius:10px;
  padding:20px;
  text-align:center;
  transition:all 0.2s;
}
.qr-preview-card:hover {
  border-color:#CC0000;
  box-shadow:0 4px 12px rgba(204,0,0,0.1);
  transform:translateY(-2px);
}
.qr-preview-card svg {
  width:180px;
  height:180px;
  margin:0 auto 16px;
  display:block;
}
.qr-preview-code {
  font-size:14px;
  font-weight:700;
  color:#0f0f0f;
  margin-bottom:6px;
  font-family:'Courier New',monospace;
  letter-spacing:0.5px;
}
.qr-preview-status {
  font-size:11px;
  color:#a09890;
  font-weight:600;
  text-transform:uppercase;
  letter-spacing:0.3px;
}
</style>

@endsection
