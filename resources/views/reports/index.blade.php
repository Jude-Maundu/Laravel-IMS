@extends('layouts.app')

@section('title', 'Reports Dashboard')
@section('page-title', 'Reports')

@section('content')
<div class="rpt-header">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Reports Dashboard</h1>
        <p class="text-sm text-gray-500">Generate professional PDF reports for inventory, events, and repairs. Reports download automatically.</p>
    </div>
</div>

<div class="rpt-stats-grid">
    <div class="rpt-stat-card">
        <div class="rpt-stat-label">Total Inventory</div>
        <div class="rpt-stat-value">{{ number_format(array_sum($itemsByStatus)) }}</div>
    </div>
    <div class="rpt-stat-card">
        <div class="rpt-stat-label">Active Events</div>
        <div class="rpt-stat-value">{{ $eventsCount }}</div>
    </div>
    <div class="rpt-stat-card">
        <div class="rpt-stat-label">In Repair</div>
        <div class="rpt-stat-value text-yellow-600">{{ $underRepairItems->count() }}</div>
    </div>
    <div class="rpt-stat-card">
        <div class="rpt-stat-label">Damaged Items</div>
        <div class="rpt-stat-value text-red-600">{{ $damagedItems->count() }}</div>
    </div>
</div>

<div class="rpt-grid">
    <!-- Section 1: Global Reports (Full Width) -->
    <div class="rpt-section rpt-section-full">
        <div class="rpt-section-title" style="margin-bottom: 24px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#CC0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            Global System Reports
        </div>
        <p class="text-sm text-gray-500 mb-6">Click any card below to instantly download a comprehensive system-wide report in PDF format.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- 1. Inventory Audit -->
            <a href="{{ route('reports.inventory.pdf') }}" class="group block bg-white border border-gray-200 rounded-xl p-6 hover:border-red-500 hover:shadow-lg transition-all duration-200 relative overflow-hidden" style="text-decoration: none;">
                <div class="absolute -right-4 -top-4 p-4 opacity-[0.03] group-hover:opacity-10 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="#CC0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                </div>
                <div class="flex items-center gap-4 mb-4 relative z-10">
                    <div class="w-12 h-12 rounded-lg bg-red-50 flex items-center justify-center text-red-600 group-hover:bg-red-600 group-hover:text-white transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 group-hover:text-red-700 transition-colors" style="margin: 0; font-size: 16px; line-height: 1.2;">Full Inventory<br>Audit</h3>
                </div>
                <p class="text-sm text-gray-500 leading-relaxed relative z-10" style="margin: 0;">Download a complete list of all assets, their current status, and images.</p>
            </a>

            <!-- 2. Event Dispatch Log -->
            <a href="{{ route('reports.assignments.pdf') }}" class="group block bg-white border border-gray-200 rounded-xl p-6 hover:border-red-500 hover:shadow-lg transition-all duration-200 relative overflow-hidden" style="text-decoration: none;">
                <div class="absolute -right-4 -top-4 p-4 opacity-[0.03] group-hover:opacity-10 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="#CC0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="flex items-center gap-4 mb-4 relative z-10">
                    <div class="w-12 h-12 rounded-lg bg-red-50 flex items-center justify-center text-red-600 group-hover:bg-red-600 group-hover:text-white transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 group-hover:text-red-700 transition-colors" style="margin: 0; font-size: 16px; line-height: 1.2;">Event Dispatch<br>Log</h3>
                </div>
                <p class="text-sm text-gray-500 leading-relaxed relative z-10" style="margin: 0;">Download a comprehensive log of all events, their assigned teams, and item checklists.</p>
            </a>

            <!-- 3. Operational Log -->
            <a href="{{ route('reports.activity.pdf') }}" class="group block bg-white border border-gray-200 rounded-xl p-6 hover:border-red-500 hover:shadow-lg transition-all duration-200 relative overflow-hidden" style="text-decoration: none;">
                <div class="absolute -right-4 -top-4 p-4 opacity-[0.03] group-hover:opacity-10 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="#CC0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
                <div class="flex items-center gap-4 mb-4 relative z-10">
                    <div class="w-12 h-12 rounded-lg bg-red-50 flex items-center justify-center text-red-600 group-hover:bg-red-600 group-hover:text-white transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 group-hover:text-red-700 transition-colors" style="margin: 0; font-size: 16px; line-height: 1.2;">System<br>Operational Log</h3>
                </div>
                <p class="text-sm text-gray-500 leading-relaxed relative z-10" style="margin: 0;">Download the full audit trail of recent status changes and system activity.</p>
            </a>

            <!-- 4. Maintenance Summary -->
            <a href="{{ route('reports.repairs.pdf') }}" class="group block bg-white border border-gray-200 rounded-xl p-6 hover:border-red-500 hover:shadow-lg transition-all duration-200 relative overflow-hidden" style="text-decoration: none;">
                <div class="absolute -right-4 -top-4 p-4 opacity-[0.03] group-hover:opacity-10 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="#CC0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                </div>
                <div class="flex items-center gap-4 mb-4 relative z-10">
                    <div class="w-12 h-12 rounded-lg bg-red-50 flex items-center justify-center text-red-600 group-hover:bg-red-600 group-hover:text-white transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 group-hover:text-red-700 transition-colors" style="margin: 0; font-size: 16px; line-height: 1.2;">Maintenance<br>Summary</h3>
                </div>
                <p class="text-sm text-gray-500 leading-relaxed relative z-10" style="margin: 0;">Download a comprehensive report of repair costs and item health trends.</p>
            </a>

        </div>
    </div>

    <!-- Section 2: Item-Specific Report -->
    <div class="rpt-section">
        <div class="rpt-section-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#CC0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
            Item Audit
        </div>
        <p class="text-sm text-gray-500 mb-6">Generate full history & health score.</p>
        
        <form onsubmit="event.preventDefault(); generateItemReport();">
            <input type="text" id="itemSearch" class="rpt-search-input" placeholder="Search by name or #ITM-001..." onkeyup="filterList('itemSearch', 'item_id')">
            
            <div class="rpt-form-group">
                <select id="item_id" class="rpt-select-list" size="5">
                    <option value="" disabled>-- Select an item --</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}">#ITM-{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }} - {{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <button type="button" onclick="generateItemReport()" class="rpt-submit-btn" style="margin-top: 15px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Generate Item Report
            </button>
        </form>
    </div>

    <!-- Section 3: Event Reports -->
    <div class="rpt-section">
        <div class="rpt-section-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#CC0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Event Documentation
        </div>
        <p class="text-sm text-gray-500 mb-6">Select an event and the document type.</p>
        
        <form onsubmit="event.preventDefault(); generateEventReport();">
            <input type="text" id="eventSearch" class="rpt-search-input" placeholder="Search by event name or client..." onkeyup="filterList('eventSearch', 'event_id')">
            
            <div class="rpt-form-group">
                <select id="event_id" class="rpt-select-list" size="5">
                    <option value="" disabled>-- Select an event --</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}">{{ $event->event_date->format('M d, Y') }} - {{ $event->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="rpt-form-group" style="margin-top: 15px;">
                <label class="rpt-label">Document Type Selection</label>
                <select id="report_type" class="rpt-select" style="height: 38px;">
                    <option value="general">📄 General Event Summary</option>
                    <option value="checklist">📋 Dispatch Checklist</option>
                    <option value="receive">🔄 Return & Triage Report</option>
                </select>
            </div>
            
            <button type="button" onclick="generateEventReport()" class="rpt-submit-btn" style="margin-top: 15px; background: #0f0f0f;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Generate Event Document
            </button>
        </form>
    </div>

    <!-- Section 4: Specific Repair Report -->
    <div class="rpt-section">
        <div class="rpt-section-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#CC0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            Repair Job Audit
        </div>
        <p class="text-sm text-gray-500 mb-6">Select a repair job for a comprehensive damage and cost report.</p>
        
        <form onsubmit="event.preventDefault(); generateRepairReport();">
            <input type="text" id="repairSearch" class="rpt-search-input" placeholder="Search by item name or repair type..." onkeyup="filterList('repairSearch', 'repair_id')">
            
            <div class="rpt-form-group">
                <select id="repair_id" class="rpt-select-list" size="5">
                    <option value="" disabled>-- Select a repair job --</option>
                    @foreach($repairs as $repair)
                        <option value="{{ $repair->id }}">{{ $repair->item->name }} - {{ $repair->repair_type }} ({{ $repair->status }})</option>
                    @endforeach
                </select>
            </div>
            
            <button type="button" onclick="generateRepairReport()" class="rpt-submit-btn" style="margin-top: 15px; background: #0f0f0f;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Generate Repair Report
            </button>
        </form>
    </div>

    <!-- Section 5: Cleaning Bay Report -->
    <div class="rpt-section flex flex-col justify-between">
        <div>
            <div class="rpt-section-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#CC0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>
                Cleaning Bay Status
            </div>
            <p class="text-sm text-gray-500 mb-6">Generate a complete tracking manifest of all items currently undergoing cleaning.</p>
            
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 mb-6 flex items-center justify-between">
                <div>
                    <div class="text-xs text-gray-500 uppercase tracking-wider font-bold mb-1">Current Backlog</div>
                    <div class="text-3xl font-bold text-gray-900" style="color: #0F6E56;">{{ $itemsByStatus['Cleaning'] ?? 0 }} Items</div>
                </div>
                <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center shadow-sm" style="color: #0F6E56;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>
                </div>
            </div>
        </div>
        
        <a href="{{ route('reports.cleaning.pdf') }}" class="rpt-submit-btn flex items-center justify-center gap-2" style="margin-top: 15px; text-decoration: none; text-align: center;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download Cleaning Manifest
        </a>
    </div>
</div>

<script>
    /**
     * Reusable list filtering function
     */
    function filterList(searchId, selectId) {
        const input = document.getElementById(searchId);
        const filter = input.value.toLowerCase();
        const select = document.getElementById(selectId);
        const options = select.getElementsByTagName('option');

        for (let i = 0; i < options.length; i++) {
            if (options[i].value === "") continue;
            const text = options[i].textContent || options[i].innerText;
            if (text.toLowerCase().indexOf(filter) > -1) {
                options[i].style.display = "";
            } else {
                options[i].style.display = "none";
            }
        }
    }

    function generateItemReport() {
        const itemId = document.getElementById('item_id').value;
        if (!itemId) {
            alert('Please select an item from the list first.');
            return;
        }
        window.location.href = `/reports/item/${itemId}/pdf`;
    }

    function generateEventReport() {
        const eventId = document.getElementById('event_id').value;
        const type = document.getElementById('report_type').value;
        if (!eventId) {
            alert('Please select an event from the list first.');
            return;
        }
        window.location.href = `/reports/event/${eventId}/${type}/pdf`;
    }

    function generateRepairReport() {
        const repairId = document.getElementById('repair_id').value;
        if (!repairId) {
            alert('Please select a repair job from the list first.');
            return;
        }
        window.location.href = `/reports/repair/${repairId}/pdf`;
    }
</script>
@endsection
