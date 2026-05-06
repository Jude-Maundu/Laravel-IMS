@extends('layouts.app')

@section('title', 'Log New Repair')
@section('page-title', 'Repairs')

@section('content')
<div class="wiz-page-header">
    <a href="{{ route('repairs.index') }}" class="wiz-back-link">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Back to Repairs List
    </a>
    <h1 class="wiz-page-title mt-2">Log New Repair Job</h1>
</div>

<form action="{{ route('repairs.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="wiz-layout">
        <!-- Sidebar Summary -->
        <div class="wiz-sidebar">
            <div class="wiz-sidebar-card">
                <div class="wiz-sidebar-title">Process Guide</div>
                <div class="wiz-step-list">
                    <div class="wiz-step-li wiz-step-li-active">
                        <div class="wiz-step-li-dot wiz-dot-active"></div>
                        Damage Assessment
                    </div>
                    <div class="wiz-step-li wiz-step-li-inactive">
                        <div class="wiz-step-li-dot"></div>
                        Cost Estimation
                    </div>
                    <div class="wiz-step-li wiz-step-li-inactive">
                        <div class="wiz-step-li-dot"></div>
                        Job Assignment
                    </div>
                </div>
            </div>
            
            <div class="wiz-sidebar-card">
                <div class="wiz-sidebar-title">Important Note</div>
                <p class="text-xs text-gray-500 mb-0">Items sent for repair will automatically have their status updated to "Under Repair". Uploading a clear damage photo helps with insurance and audit records.</p>
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
                    <div class="wiz-card-title">Damage Assessment</div>
                    <div class="wiz-card-sub">Identify the item and detail the extent of the damage.</div>
                </div>
                <div class="wiz-card-body wiz-form-grid">
                    
                    <div class="wiz-form-group wiz-full">
                        <label class="wiz-label">Select Item to Repair <span class="wiz-req">*</span></label>
                        <select name="item_id" required class="wiz-input">
                            <option value="">-- Choose an item --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                    #ITM-{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }} - {{ $item->name }} ({{ $item->status }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="wiz-form-group">
                        <label class="wiz-label">Repair Type <span class="wiz-req">*</span></label>
                        <select name="repair_type" required class="wiz-input">
                            <option value="Scheduled" {{ old('repair_type') == 'Scheduled' ? 'selected' : '' }}>Scheduled Maintenance</option>
                            <option value="Emergency" {{ old('repair_type') == 'Emergency' ? 'selected' : '' }}>Emergency Repair</option>
                            <option value="Cosmetic" {{ old('repair_type') == 'Cosmetic' ? 'selected' : '' }}>Cosmetic Touch-up</option>
                            <option value="Structural" {{ old('repair_type') == 'Structural' ? 'selected' : '' }}>Structural Damage</option>
                        </select>
                    </div>
                    
                    <div class="wiz-form-group">
                        <label class="wiz-label">Damage Evidence Photo <span class="wiz-optional">(Optional)</span></label>
                        <input type="file" name="damage_image" accept="image/*" class="wiz-input" style="padding-top: 6px; font-size: 11px;">
                    </div>

                    <div class="wiz-form-group wiz-full">
                        <label class="wiz-label">Damage Description <span class="wiz-req">*</span></label>
                        <textarea name="description" rows="3" required class="wiz-textarea" placeholder="Explain the issue in detail...">{{ old('description') }}</textarea>
                    </div>
                    
                </div>
            </div>

            <div class="wiz-card mb-4">
                <div class="wiz-card-head">
                    <div class="wiz-card-title">Cost Estimation & Materials</div>
                    <div class="wiz-card-sub">What is required to fix this item?</div>
                </div>
                <div class="wiz-card-body wiz-form-grid">
                    
                    <div class="wiz-form-group wiz-full">
                        <label class="wiz-label">Materials Required <span class="wiz-optional">(Optional)</span></label>
                        <textarea name="materials_required" rows="2" class="wiz-textarea" placeholder="E.g., 2m of canvas, 4 tent pegs, epoxy glue...">{{ old('materials_required') }}</textarea>
                    </div>
                    
                    <div class="wiz-form-group">
                        <label class="wiz-label">Estimated Cost (KES) <span class="wiz-req">*</span></label>
                        <div class="wiz-input-prefix-wrap">
                            <span class="wiz-input-prefix">KES</span>
                            <input type="number" step="0.01" name="estimated_cost" value="{{ old('estimated_cost') }}" required class="wiz-input wiz-input-prefixed" placeholder="0.00">
                        </div>
                    </div>
                    
                </div>
            </div>

            <div class="wiz-card mb-4">
                <div class="wiz-card-head">
                    <div class="wiz-card-title">Job Assignment</div>
                    <div class="wiz-card-sub">Who is handling this repair?</div>
                </div>
                <div class="wiz-card-body wiz-form-grid">
                    
                    <div class="wiz-form-group">
                        <label class="wiz-label">Assigned Technician <span class="wiz-optional">(Optional)</span></label>
                        <input type="text" name="technician_name" value="{{ old('technician_name') }}" class="wiz-input" placeholder="Technician or company name">
                    </div>

                    <div class="wiz-form-group">
                        <label class="wiz-label">Internal Notes <span class="wiz-optional">(Optional)</span></label>
                        <input type="text" name="notes" value="{{ old('notes') }}" class="wiz-input" placeholder="Any private remarks...">
                    </div>
                    
                </div>
                
                <div class="wiz-card-footer">
                    <div class="wiz-footer-hint">By creating this job, the item status will change to "Under Repair".</div>
                    <div class="wiz-footer-actions">
                        <a href="{{ route('repairs.index') }}" class="wiz-btn-cancel">Cancel</a>
                        <button type="submit" class="wiz-btn-next" onclick="this.disabled=true; this.form.submit();">
                            Create Repair Job
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>
@endsection
