@extends('layouts.app')

@section('page-title', 'Edit Item')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow border border-gray-200">
    <h3 class="text-lg font-semibold mb-4">Edit Item: {{ $item->name }}</h3>
    <form action="{{ route('inventory.update', $item) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" value="{{ $item->name }}" required class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Category</label>
            <select name="category" required class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ (old('category') ?? $item->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" required class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ $item->status == $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Location</label>
            <select name="location" required class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                @foreach($locations as $loc)
                    <option value="{{ $loc }}" {{ $item->location == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Assigned To (optional)</label>
            <input type="text" name="assigned_to" value="{{ $item->assigned_to }}" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Assigned By (optional)</label>
            <input type="text" name="assigned_by" value="{{ $item->assigned_by }}" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Number of Pieces</label>
            <input type="number" name="total_pieces" value="{{ $item->total_pieces }}" min="1" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
            <small class="text-xs text-gray-500">Each piece will be assigned a unique tracking code automatically. Increasing this will add new pieces; decreasing will only update the count.</small>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Notes</label>
            <textarea name="notes" rows="3" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">{{ $item->notes }}</textarea>
        </div>
        <div class="flex justify-between">
            <a href="{{ route('inventory.index') }}" class="text-gray-600 hover:underline">Cancel</a>
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Update Item</button>
        </div>
    </form>
</div>
@endsection