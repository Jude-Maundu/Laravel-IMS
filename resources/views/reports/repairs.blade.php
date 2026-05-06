@extends('layouts.app')

@section('page-title', 'Repairs Report')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
    <h1 class="text-2xl font-bold text-gray-800">Repairs Report</h1>
    <div class="mt-4 md:mt-0 flex gap-2">
        <a href="{{ route('reports.repairs.pdf') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Download PDF</a>
        <button onclick="window.print()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Print Report</button>
        <a href="{{ route('reports.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
        <div class="text-sm text-gray-500">Total Repairs</div>
        <div class="text-2xl font-bold">{{ $repairs->count() }}</div>
    </div>
    <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
        <div class="text-sm text-gray-500">Estimated Cost</div>
        <div class="text-2xl font-bold text-yellow-600">${{ number_format($totalEstimated, 2) }}</div>
    </div>
    <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
        <div class="text-sm text-gray-500">Actual Cost</div>
        <div class="text-2xl font-bold text-green-600">${{ number_format($totalActual, 2) }}</div>
    </div>
</div>

<div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Est. Cost</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actual Cost</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($repairs as $repair)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-sm">#{{ $repair->id }}</td>
                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $repair->item->name ?? 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $repair->repair_type ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="status-badge status-{{ strtolower(str_replace(' ', '', $repair->status)) }}">{{ $repair->status }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-700">${{ number_format($repair->estimated_cost ?? 0, 2) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-700">${{ number_format($repair->actual_cost ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No repairs found.</td>
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
