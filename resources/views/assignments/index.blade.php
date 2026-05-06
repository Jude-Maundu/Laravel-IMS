@extends('layouts.app')

@section('page-title', 'Assignments')

@section('content')
<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800">My Assigned Items</h1>
        <div class="flex space-x-3">
            {{-- Filter Tabs --}}
            <div class="flex space-x-2">
                <button class="filter-btn active" data-filter="all">All</button>
                <button class="filter-btn" data-filter="active">Active</button>
                <button class="filter-btn" data-filter="overdue">Overdue</button>
                <button class="filter-btn" data-filter="returned">Returned</button>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-gray-500 text-sm">Total Assigned</div>
            <div class="text-2xl font-bold text-gray-800" id="totalCount">0</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-gray-500 text-sm">Active Items</div>
            <div class="text-2xl font-bold text-blue-600" id="activeCount">0</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-gray-500 text-sm">Overdue</div>
            <div class="text-2xl font-bold text-red-600" id="overdueCount">0</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-gray-500 text-sm">This Month</div>
            <div class="text-2xl font-bold text-gray-800" id="monthlyCount">0</div>
        </div>
    </div>

    {{-- Search and Filter Bar --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" 
                       id="searchInput" 
                       placeholder="Search by item name, ID, or assigned to..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
            </div>
            <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                <option value="">All Statuses</option>
                <option value="available">Available</option>
                <option value="checked out">Checked Out</option>
                <option value="overdue">Overdue</option>
                <option value="maintenance">Maintenance</option>
            </select>
            <select id="locationFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                <option value="">All Locations</option>
                @php
                    $locations = $assignments->pluck('item.location')->unique();
                @endphp
                @foreach($locations as $location)
                    <option value="{{ $location }}">{{ $location }}</option>
                @endforeach
            </select>
            <button id="clearFilters" class="px-4 py-2 text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50">
                Clear Filters
            </button>
        </div>
    </div>

    {{-- Items Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="itemsTableBody" class="bg-white divide-y divide-gray-200">
                    @forelse($assignments as $assignment)
                    <tr class="item-row hover:bg-gray-50 transition-colors"
                        data-name="{{ strtolower($assignment->item->name) }}"
                        data-id="{{ $assignment->item->id }}"
                        data-status="{{ strtolower($assignment->status) }}"
                        data-location="{{ strtolower($assignment->item->location) }}"
                        data-assigned="{{ strtolower($assignment->assigned_to) }}"
                        data-due-date="{{ $assignment->due_date ?? '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $assignment->item->name }}</div>
                            @if($assignment->item->description)
                                <div class="text-xs text-gray-500">{{ Str::limit($assignment->item->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-sm">#{{ $assignment->item->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClass = match(strtolower($assignment->status)) {
                                    'overdue' => 'status-overdue',
                                    'active', 'assigned' => 'status-active',
                                    'maintenance' => 'status-maintenance',
                                    'returned' => 'status-returned',
                                    default => 'status-default'
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                {{ $assignment->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $assignment->item->location }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $assignment->assigned_to }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($assignment->due_date)
                                <span class="text-sm {{ \Carbon\Carbon::parse($assignment->due_date)->isPast() && strtolower($assignment->status) !== 'returned' ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                    {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') }}
                                    @if(\Carbon\Carbon::parse($assignment->due_date)->isPast() && strtolower($assignment->status) !== 'returned')
                                        <span class="ml-1 text-xs">(Overdue)</span>
                                    @endif
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">Not set</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                            @if(strtolower($assignment->status) !== 'returned')
                                <form action="{{ route('assignments.return', $assignment) }}" method="POST" class="inline return-form">
                                    @csrf
                                    @method('POST')
                                    <button type="submit"
                                            class="return-btn px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:outline-none transition-colors">
                                        Return
                                    </button>
                                </form>

                                <button type="button"
                                        onclick="openExtendModal({{ $assignment->id }}, '{{ $assignment->item->name }}', '{{ $assignment->due_date }}')"
                                        class="extend-btn px-3 py-1 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:ring-2 focus:ring-gray-500 transition-colors">
                                    Extend
                                </button>
                            @else
                                <span class="text-gray-400 text-sm italic">Returned</span>
                            @endif

                            <button type="button"
                                    onclick="viewDetails({{ $assignment->id }})"
                                    class="view-btn px-3 py-1 text-gray-600 hover:text-gray-800 underline text-sm">
                                View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="mt-2">No assigned items found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if(method_exists($assignments, 'links'))
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $assignments->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Extend Due Date Modal --}}
<div id="extendModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Extend Due Date</h3>
        </div>
        <div class="px-6 py-4">
            <p class="text-gray-600 mb-4">Extend due date for: <strong id="extendItemName"></strong></p>
            <form id="extendForm" method="POST">
                @csrf
                @method('PATCH')
                <label class="block text-sm font-medium text-gray-700 mb-2">New Due Date</label>
                <input type="date" id="newDueDate" name="due_date" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <div class="mt-4 flex justify-end space-x-3">
                    <button type="button" onclick="closeExtendModal()" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Extend
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Item Details Modal --}}
<div id="detailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Item Details</h3>
            <button onclick="closeDetailsModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <div id="detailsContent" class="px-6 py-4">
            <!-- Dynamic content loaded via JS -->
        </div>
    </div>
</div>

<style>
    .status-badge {
        @apply px-2 py-1 text-xs font-medium rounded-full;
    }
    
    .status-overdue {
        @apply bg-red-100 text-red-800;
    }
    
    .status-active {
        @apply bg-blue-100 text-blue-800;
    }
    
    .status-maintenance {
        @apply bg-yellow-100 text-yellow-800;
    }
    
    .status-returned {
        @apply bg-gray-100 text-gray-600;
    }
    
    .status-default {
        @apply bg-gray-100 text-gray-800;
    }
    
    .filter-btn {
        @apply px-3 py-1 text-sm font-medium rounded-md transition-colors;
        @apply bg-white text-gray-600 border border-gray-300 hover:bg-gray-50;
    }
    
    .filter-btn.active {
        @apply bg-red-600 text-white border-red-600 hover:bg-red-700;
    }
    
    .return-btn, .extend-btn, .view-btn {
        transition: all 0.2s ease;
    }
    
    .return-btn:hover {
        transform: translateY(-1px);
    }
    
    .item-row {
        transition: background-color 0.2s ease;
    }
</style>

<script>
// Filter functionality
let currentFilter = 'all';
let currentStatus = '';
let currentLocation = '';
let currentSearch = '';

function updateStats() {
    const rows = document.querySelectorAll('.item-row');
    const total = rows.length;
    let active = 0;
    let overdue = 0;
    let monthly = 0;
    
    rows.forEach(row => {
        const status = row.dataset.status;
        if (status === 'checked out' || status === 'assigned') active++;
        if (status === 'overdue') overdue++;
        
        // Simple monthly count (you can enhance this with actual date logic)
        monthly++;
    });
    
    document.getElementById('totalCount').textContent = total;
    document.getElementById('activeCount').textContent = active;
    document.getElementById('overdueCount').textContent = overdue;
    document.getElementById('monthlyCount').textContent = monthly;
}

function filterItems() {
    const rows = document.querySelectorAll('.item-row');
    const searchTerm = currentSearch.toLowerCase();
    const statusFilter = currentStatus.toLowerCase();
    const locationFilter = currentLocation.toLowerCase();
    
    rows.forEach(row => {
        let show = true;
        
        // Status filter (tab)
        if (currentFilter !== 'all') {
            const status = row.dataset.status;
            if (currentFilter === 'active' && !['checked out', 'assigned'].includes(status)) show = false;
            if (currentFilter === 'overdue' && status !== 'overdue') show = false;
            if (currentFilter === 'returned' && status !== 'returned') show = false;
        }
        
        // Status dropdown filter
        if (show && statusFilter && row.dataset.status !== statusFilter) show = false;
        
        // Location filter
        if (show && locationFilter && row.dataset.location !== locationFilter) show = false;
        
        // Search filter
        if (show && searchTerm) {
            const nameMatch = row.dataset.name.includes(searchTerm);
            const idMatch = row.dataset.id.toString().includes(searchTerm);
            const assignedMatch = row.dataset.assigned.includes(searchTerm);
            if (!nameMatch && !idMatch && !assignedMatch) show = false;
        }
        
        row.style.display = show ? '' : 'none';
    });
    
    updateStats();
}

// Search input handler
document.getElementById('searchInput')?.addEventListener('input', (e) => {
    currentSearch = e.target.value;
    filterItems();
});

// Status dropdown handler
document.getElementById('statusFilter')?.addEventListener('change', (e) => {
    currentStatus = e.target.value;
    filterItems();
});

// Location dropdown handler
document.getElementById('locationFilter')?.addEventListener('change', (e) => {
    currentLocation = e.target.value;
    filterItems();
});

// Clear filters handler
document.getElementById('clearFilters')?.addEventListener('click', () => {
    currentSearch = '';
    currentStatus = '';
    currentLocation = '';
    currentFilter = 'all';
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('locationFilter').value = '';
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector('.filter-btn[data-filter="all"]').classList.add('active');
    filterItems();
});

// Tab filter handlers
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentFilter = btn.dataset.filter;
        filterItems();
    });
});

// Return form confirmation with loading state
document.querySelectorAll('.return-form').forEach(form => {
    form.addEventListener('submit', (e) => {
        if (!confirm('Are you sure you want to return this item?\n\nPlease ensure the item is in good condition before returning.')) {
            e.preventDefault();
            return;
        }
        
        const button = form.querySelector('button');
        const originalText = button.textContent;
        button.textContent = 'Processing...';
        button.disabled = true;
        
        setTimeout(() => {
            button.textContent = originalText;
            button.disabled = false;
        }, 3000);
    });
});

// Extend modal functions
function openExtendModal(itemId, itemName, currentDueDate) {
    const modal = document.getElementById('extendModal');
    const form = document.getElementById('extendForm');
    const itemNameSpan = document.getElementById('extendItemName');
    const dateInput = document.getElementById('newDueDate');
    
    itemNameSpan.textContent = itemName;
    form.action = `/inventory/${itemId}/extend`;
    
    // Set minimum date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    dateInput.min = tomorrow.toISOString().split('T')[0];
    
    // Set default to 7 days from now
    const defaultDate = new Date();
    defaultDate.setDate(defaultDate.getDate() + 7);
    dateInput.value = defaultDate.toISOString().split('T')[0];
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeExtendModal() {
    const modal = document.getElementById('extendModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// View details function
function viewDetails(itemId) {
    const modal = document.getElementById('detailsModal');
    const content = document.getElementById('detailsContent');
    
    // You can fetch item details via AJAX
    content.innerHTML = '<div class="text-center py-4">Loading...</div>';
    
    fetch(`/inventory/${itemId}/details`)
        .then(response => response.json())
        .then(data => {
            content.innerHTML = `
                <div class="space-y-3">
                    <div><strong class="text-gray-700">Item Name:</strong> <span class="text-gray-900">${data.name}</span></div>
                    <div><strong class="text-gray-700">ID:</strong> <span class="text-gray-900">#${data.id}</span></div>
                    <div><strong class="text-gray-700">Status:</strong> <span class="text-gray-900">${data.status}</span></div>
                    <div><strong class="text-gray-700">Location:</strong> <span class="text-gray-900">${data.location}</span></div>
                    <div><strong class="text-gray-700">Assigned To:</strong> <span class="text-gray-900">${data.assigned_to}</span></div>
                    <div><strong class="text-gray-700">Due Date:</strong> <span class="text-gray-900">${data.due_date || 'Not set'}</span></div>
                    <div><strong class="text-gray-700">Description:</strong> <span class="text-gray-900">${data.description || 'No description'}</span></div>
                </div>
            `;
        })
        .catch(error => {
            content.innerHTML = '<div class="text-red-600 text-center py-4">Error loading details.</div>';
        });
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeDetailsModal() {
    const modal = document.getElementById('detailsModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Close modals when clicking outside
window.addEventListener('click', (e) => {
    const extendModal = document.getElementById('extendModal');
    const detailsModal = document.getElementById('detailsModal');
    
    if (e.target === extendModal) closeExtendModal();
    if (e.target === detailsModal) closeDetailsModal();
});

// Initialize on load
document.addEventListener('DOMContentLoaded', () => {
    updateStats();
    filterItems();
});
</script>

{{-- Add this to your controller for the extend route --}}
@php
/*
// Add to your controller:
public function extendDueDate(Request $request, $id)
{
    $item = InventoryItem::findOrFail($id);
    $request->validate(['due_date' => 'required|date|after:today']);
    
    $item->due_date = $request->due_date;
    $item->save();
    
    return redirect()->back()->with('success', 'Due date extended successfully!');
}

// Add to web.php:
Route::patch('/inventory/{id}/extend', [InventoryController::class, 'extendDueDate'])->name('inventory.extend');
Route::get('/inventory/{id}/details', [InventoryController::class, 'getDetails'])->name('inventory.details');
*/
@endphp
@endsection