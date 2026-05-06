@extends('layouts.app')

@section('page-title', 'New Assignment')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow border border-gray-200">
    <h3 class="text-lg font-semibold mb-4">Create New Assignment</h3>
    <form action="{{ route('assignments.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Item *</label>
            <select name="item_id" required class="w-full border border-gray-300 rounded px-3 py-2">
                <option value="">Select an item...</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->status }})</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Assigned To *</label>
            <input type="text" name="assigned_to" required class="w-full border border-gray-300 rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Assigned By *</label>
            <input type="text" name="assigned_by" required class="w-full border border-gray-300 rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
            <input type="date" name="due_date" class="w-full border border-gray-300 rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded px-3 py-2"></textarea>
        </div>
        <div class="flex justify-between">
            <a href="{{ route('assignments.index') }}" class="text-gray-600 hover:underline">Cancel</a>
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Create Assignment</button>
        </div>
    </form>
</div>
@endsection
