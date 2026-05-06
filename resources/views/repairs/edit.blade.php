@extends('layouts.app')

@section('title', 'Edit Repair Job')
@section('page-title', 'Repairs')

@section('content')
<div class="wiz-page-header">
    <a href="{{ route('repairs.show', $repair) }}" class="wiz-back-link">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Back to Repair Details
    </a>
    <h1 class="wiz-page-title mt-2">Update Repair Job #RPR-{{ str_pad($repair->id, 4, '0', STR_PAD_LEFT) }}</h1>
</div>

<form action="{{ route('repairs.update', $repair) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="wiz-layout">
        <!-- Sidebar Summary -->
        <div class="wiz-sidebar">
            <div class="wiz-sidebar-card">
                <div class="wiz-sidebar-title">Item Summary</div>
                <div class="wiz-summary-item">
                    <span class="wiz-sum-label">Item</span>
                    <span class="wiz-sum-val">{{ $repair->item->name }}</span>
                </div>
                <div class="wiz-summary-item">
                    <span class="wiz-sum-label">Current Status</span>
                    <span class="wiz-sum-val">
                        <span class="wiz-tag {{ $repair->status === 'Completed' ? 'wiz-tag-avail' : 'wiz-tag-unavail' }}">
                            {{ $repair->status }}
                        </span>
                    </span>
                </div>
            </div>
            
            <div class="wiz-sidebar-card">
                <div class="wiz-sidebar-title">Important Note</div>
                <p class="text-xs text-gray-500 mb-0">Updating the status to "Completed" will automatically return the item's status to "Available" in the main inventory.</p>
            </div>
        </div>

        <!-- Main Form Content -->
        <div class="wiz-main">
            @if($errors->any())
                <div class="wiz-error-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div>
                        <div class="text-xs font-bold text-red-800">Please correct the following errors:</div>
                        <ul class="text-xs text-red-700 list-disc ml-4 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="wiz-card mb-4">
                <div class="wiz-card-head">
                    <div class="wiz-card-title">Job Status & Details</div>
                    <div class="wiz-card-sub">Update the progress of this repair.</div>
                </div>
                <div class="wiz-card-body wiz-form-grid">
                    
                    <div class="wiz-form-group">
                        <label class="wiz-label">Job Status <span class="wiz-req">*</span></label>
                        <select name="status" required class="wiz-input" onchange="toggleCompletedFields(this.value)">
                            <option value="Pending" {{ old('status', $repair->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="In Progress" {{ old('status', $repair->status) == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Completed" {{ old('status', $repair->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Cancelled" {{ old('status', $repair->status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="wiz-form-group">
                        <label class="wiz-label">Repair Type <span class="wiz-req">*</span></label>
                        <select name="repair_type" required class="wiz-input">
                            <option value="Scheduled" {{ old('repair_type', $repair->repair_type) == 'Scheduled' ? 'selected' : '' }}>Scheduled Maintenance</option>
                            <option value="Emergency" {{ old('repair_type', $repair->repair_type) == 'Emergency' ? 'selected' : '' }}>Emergency Repair</option>
                            <option value="Cosmetic" {{ old('repair_type', $repair->repair_type) == 'Cosmetic' ? 'selected' : '' }}>Cosmetic Touch-up</option>
                            <option value="Structural" {{ old('repair_type', $repair->repair_type) == 'Structural' ? 'selected' : '' }}>Structural Damage</option>
                        </select>
                    </div>

                    <div class="wiz-form-group wiz-full">
                        <label class="wiz-label">Damage Description <span class="wiz-req">*</span></label>
                        <textarea name="description" rows="3" required class="wiz-textarea">{{ old('description', $repair->description) }}</textarea>
                    </div>
                    
                    <div class="wiz-form-group wiz-full">
                        <label class="wiz-label">Damage Evidence Photo <span class="wiz-optional">(Optional)</span></label>
                        @if($repair->damage_image_path)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $repair->damage_image_path) }}" alt="Damage Evidence" class="h-24 w-auto rounded border border-gray-300">
                                <span class="text-xs text-gray-500 block mt-1">Current image (uploading a new one will replace it).</span>
                            </div>
                        @endif
                        <input type="file" name="damage_image" accept="image/*" class="wiz-input" style="padding-top: 6px; font-size: 11px;">
                    </div>
                    
                </div>
            </div>

            <div class="wiz-card mb-4">
                <div class="wiz-card-head">
                    <div class="wiz-card-title">Cost & Materials Tracker</div>
                    <div class="wiz-card-sub">Log materials used and actual costs incurred.</div>
                </div>
                <div class="wiz-card-body wiz-form-grid">
                    
                    <div class="wiz-form-group wiz-full">
                        <label class="wiz-label">Materials Required/Used <span class="wiz-optional">(Optional)</span></label>
                        <textarea name="materials_required" rows="2" class="wiz-textarea">{{ old('materials_required', $repair->materials_required) }}</textarea>
                    </div>
                    
                    <div class="wiz-form-group">
                        <label class="wiz-label">Estimated Cost (KES) <span class="wiz-req">*</span></label>
                        <div class="wiz-input-prefix-wrap">
                            <span class="wiz-input-prefix">KES</span>
                            <input type="number" step="0.01" name="estimated_cost" value="{{ old('estimated_cost', $repair->estimated_cost) }}" required class="wiz-input wiz-input-prefixed">
                        </div>
                    </div>

                    <div class="wiz-form-group" id="actual_cost_group" style="{{ old('status', $repair->status) === 'Completed' ? '' : 'opacity: 0.5;' }}">
                        <label class="wiz-label">Actual Final Cost (KES) <span class="wiz-optional">(Required on completion)</span></label>
                        <div class="wiz-input-prefix-wrap">
                            <span class="wiz-input-prefix">KES</span>
                            <input type="number" step="0.01" name="actual_cost" id="actual_cost" value="{{ old('actual_cost', $repair->actual_cost) }}" class="wiz-input wiz-input-prefixed">
                        </div>
                    </div>
                    
                </div>
            </div>

            <div class="wiz-card mb-4">
                <div class="wiz-card-head">
                    <div class="wiz-card-title">Job Assignment & Dates</div>
                    <div class="wiz-card-sub">Log who worked on it and when it was completed.</div>
                </div>
                <div class="wiz-card-body wiz-form-grid">
                    
                    <div class="wiz-form-group">
                        <label class="wiz-label">Assigned Technician <span class="wiz-optional">(Optional)</span></label>
                        <input type="text" name="technician_name" value="{{ old('technician_name', $repair->technician_name) }}" class="wiz-input">
                    </div>
                    
                    <div class="wiz-form-group">
                        <label class="wiz-label">Date Completed <span class="wiz-optional">(Required on completion)</span></label>
                        <input type="date" name="completed_at" id="completed_at" value="{{ old('completed_at', $repair->completed_at ? $repair->completed_at->format('Y-m-d') : '') }}" class="wiz-input">
                    </div>

                    <div class="wiz-form-group wiz-full">
                        <label class="wiz-label">Internal Notes <span class="wiz-optional">(Optional)</span></label>
                        <input type="text" name="notes" value="{{ old('notes', $repair->notes) }}" class="wiz-input">
                    </div>
                    
                </div>
                
                <div class="wiz-card-footer">
                    <div class="wiz-footer-hint">Review all details before saving.</div>
                    <div class="wiz-footer-actions">
                        <a href="{{ route('repairs.show', $repair) }}" class="wiz-btn-cancel">Cancel</a>
                        <button type="submit" class="wiz-btn-next" onclick="this.disabled=true; this.form.submit();">
                            Update Repair Job
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

<script>
function toggleCompletedFields(status) {
    const actualCostGroup = document.getElementById('actual_cost_group');
    const actualCostInput = document.getElementById('actual_cost');
    const completedAtInput = document.getElementById('completed_at');
    
    if (status === 'Completed') {
        actualCostGroup.style.opacity = '1';
        actualCostInput.required = true;
        completedAtInput.required = true;
        
        if (!completedAtInput.value) {
            completedAtInput.value = new Date().toISOString().split('T')[0];
        }
    } else {
        actualCostGroup.style.opacity = '0.5';
        actualCostInput.required = false;
        completedAtInput.required = false;
    }
}
</script>
@endsection
