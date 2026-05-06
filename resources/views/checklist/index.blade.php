@extends('layouts.app')

@section('page-title', 'Checklist')

@section('content')
<div class="mb-6">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow border border-gray-200">
        <h3 class="text-lg font-semibold mb-4">Perform Checklist Action</h3>
        <form action="{{ route('checklist.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Select Item</label>
                <select name="item_id" required class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">Choose an item...</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->status }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Action</label>
                <select name="action" required class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                    @foreach($actions as $action)
                        <option value="{{ $action }}">{{ $action }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Submit</button>
            </div>
        </form>
    </div>
</div>

<div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assignment</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Performed By</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Condition</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($checklists as $checklist)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $checklist->item ? $checklist->item->name : 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-sm">{{ $checklist->assignment ? $checklist->assignment->assigned_to : '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $checklist->action }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $checklist->performed_by ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $checklist->condition ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-sm">{{ $checklist->created_at ? $checklist->created_at->format('Y-m-d H:i') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No checklist records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
