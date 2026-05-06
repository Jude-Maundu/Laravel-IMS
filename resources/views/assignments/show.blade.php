@extends('layouts.app')

@section('page-title', 'Assignment Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">Assignment #{{ $assignment->id }}</h3>
                <p class="text-gray-500">Item: {{ $assignment->item ? $assignment->item->name : 'N/A' }}</p>
            </div>
            <div class="mt-4 md:mt-0">
                <span class="status-badge status-{{ strtolower($assignment->status) }} text-lg px-4 py-2">
                    {{ $assignment->status }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h4 class="text-sm font-medium text-gray-500">Assigned To</h4>
                <p class="text-gray-900">{{ $assignment->assigned_to }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500">Assigned By</h4>
                <p class="text-gray-900">{{ $assignment->assigned_by }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500">Due Date</h4>
                <p class="text-gray-900">{{ $assignment->due_date ? $assignment->due_date->format('Y-m-d') : 'No due date' }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500">Created At</h4>
                <p class="text-gray-900">{{ $assignment->created_at ? $assignment->created_at->format('Y-m-d H:i') : 'N/A' }}</p>
            </div>
        </div>

        @if($assignment->notes)
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500">Notes</h4>
            <p class="text-gray-900 whitespace-pre-wrap">{{ $assignment->notes }}</p>
        </div>
        @endif

        <div class="flex flex-wrap gap-3 mt-6">
            <a href="{{ route('assignments.edit', $assignment) }}" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Edit</a>
            <a href="{{ route('assignments.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back to List</a>
        </div>
    </div>
</div>
@endsection
