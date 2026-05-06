@extends('layouts.app')
@section('title', 'Categories')
@section('page-title', 'Inventory')

@section('content')

{{-- BREADCRUMB --}}
<div class="itd-breadcrumb">
  <a href="{{ route('inventory.index') }}" class="itd-bc-link">
    <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M10 3L6 8l4 5"/></svg>
    Inventory
  </a>
  <span class="itd-bc-sep">/</span>
  <span class="itd-bc-cur">Categories</span>
</div>

{{-- PAGE HEADER --}}
<div class="inv-header">
  <div class="inv-header-left">
    <h1 class="inv-title">Manage Categories</h1>
    <p class="inv-subtitle">Define item classifications for your inventory</p>
  </div>
</div>

{{-- SUCCESS / ERROR FLASH --}}
@if(session('success'))
  <div class="inv-flash inv-flash-success">
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 8l2 2 5-5"/><circle cx="8" cy="8" r="6.5"/></svg>
    {{ session('success') }}
  </div>
@endif

<div style="display:grid;grid-template-columns: 1fr 320px;gap:24px;margin-top:20px;align-items:start">
  
  {{-- CATEGORIES LIST --}}
  <div class="inv-table-wrap" style="margin-top:0">
    <table class="inv-table">
      <thead>
        <tr>
          <th>Category Name</th>
          <th>Item Count</th>
          <th>Created At</th>
          <th style="text-align:right">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($categories as $category)
        @php
          $itemCount = \App\Models\Item::where('category', $category->name)->count();
        @endphp
        <tr>
          <td style="font-weight:600">
            <a href="{{ route('inventory.index', ['category' => $category->name]) }}" style="color:#CC0000; text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
              {{ $category->name }}
            </a>
          </td>
          <td>
            <span style="background:#f5f1ed; color:#5c5550; font-size:10px; font-weight:700; padding:3px 8px; border-radius:12px;">
              {{ $itemCount }} items
            </span>
          </td>
          <td style="color:#a09890; font-size:11px;">{{ $category->created_at->format('M d, Y') }}</td>
          <td style="text-align:right">
            <form method="POST" action="{{ route('categories.destroy', $category) }}" style="display:inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="inv-clear-btn" style="color:#CC0000;padding:4px">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M3 4h10M5 4V3a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v1M6 7v5M10 7v5M4 4l1 10h6l1-10"/></svg>
              </button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="3" style="text-align:center;padding:48px;color:#a09890">No categories defined yet.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- ADD CATEGORY FORM --}}
  <div style="background:#fff;border:1px solid #ece8e3;border-radius:10px;padding:20px">
    <h3 style="font-size:14px;font-weight:700;margin:0 0 16px;color:#0f0f0f">Add New Category</h3>
    <form method="POST" action="{{ route('categories.store') }}">
      @csrf
      <div class="wiz-form-group">
        <label class="wiz-label">Category Name <span class="wiz-req">*</span></label>
        <input type="text" name="name" class="wiz-input" placeholder="e.g. Tents, Lighting..." required>
      </div>
      <button type="submit" class="inv-btn-primary" style="width:100%;margin-top:16px">
        Save Category
      </button>
    </form>
  </div>

</div>

<style>
.inv-table-wrap {
  background: #fff;
  border: 1px solid #ece8e3;
  border-radius: 10px;
  overflow: hidden;
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
.inv-table tr:hover {
  background: #fdf9f8;
}
</style>

@endsection
