@extends('layouts.app')

@section('page-title', 'Assignments Report')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
    <h1 class="text-2xl font-bold text-gray-800">Assignments Report</h1>
    <div class="mt-4 md:mt-0 flex gap-2">
        <a href="{{ route('reports.assignments.pdf') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Download PDF</a>
        <button onclick="window.print()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Print Report</button>
        <a href="{{ route('reports.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back</a>
    </div>
</div>

<div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned To</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned By</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($assignments as $assignment)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-sm">#{{ $assignment->id }}</td>
                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $assignment->item->name ?? 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $assignment->assigned_to }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $assignment->assigned_by }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $assignment->due_date ? $assignment->due_date->format('Y-m-d') : '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="status-badge status-{{ strtolower($assignment->status) }}">{{ $assignment->status }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No assignments found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<style>
@media print {
    aside, header, .no-print { display: none !important; }
    .md\:ml-64 { margin-left: 0 !important; }
    body { background: white !important; }
}
</style>
@endsection
