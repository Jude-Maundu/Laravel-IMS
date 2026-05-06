@extends('layouts.app')

@section('page-title', 'Activity Log')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
    <h1 class="text-2xl font-bold text-gray-800">Activity Log Report</h1>
    <div class="mt-4 md:mt-0 flex gap-2">
        <a href="{{ route('reports.activity.pdf') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Download PDF</a>
        <button onclick="window.print()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Print Report</button>
        <a href="{{ route('reports.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back</a>
    </div>
</div>

<div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($activities as $activity)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-sm">{{ $activity->created_at->format('Y-m-d H:i') }}</td>
                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $activity->item->name ?? 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 bg-gray-100 rounded text-xs font-medium">{{ $activity->action }}</span>
                </td>
                <td class="px-6 py-4 text-gray-700">{{ $activity->description ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-sm">{{ $activity->user_id }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No activities found.</td>
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
