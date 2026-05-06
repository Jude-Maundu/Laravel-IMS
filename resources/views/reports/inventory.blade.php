@extends('layouts.app')

@section('page-title', 'Inventory Report')

@section('content')
<div class="print-only hidden">
    <h1 class="text-2xl font-bold text-center mb-4">Inventory Report</h1>
    <p class="text-center text-gray-600 mb-6">Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
</div>

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
    <h1 class="text-2xl font-bold text-gray-800">Inventory Report</h1>
    <div class="mt-4 md:mt-0 flex gap-2">
        <a href="{{ route('reports.inventory.pdf') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
            Download PDF
        </a>
        <button onclick="window.print()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Print Report
        </button>
        <a href="{{ route('reports.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Back to Reports
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned To</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($items as $item)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-sm">#{{ $item->id }}</td>
                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $item->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $item->category }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="status-badge status-{{ strtolower(str_replace(' ', '', $item->status)) }}">
                        {{ $item->status }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $item->location }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $item->assigned_to ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No items found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4 text-gray-600">
    <p>Total Items: {{ $items->count() }}</p>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    .print-only { display: block !important; }
    body { background: white !important; }
    .bg-gray-100 { background: white !important; }
    aside { display: none !important; }
    .md\\:ml-64 { margin-left: 0 !important; }
}
</style>
@endsection
