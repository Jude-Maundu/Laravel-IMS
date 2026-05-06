@extends('layouts.app')
@section('title', 'Add New Item')
@section('page-title', 'Inventory')

@section('content')

{{-- BREADCRUMB --}}
<div class="itd-breadcrumb">
  <a href="{{ route('inventory.index') }}" class="itd-bc-link">
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M10 3L6 8l4 5"/></svg>
    Inventory
  </a>
  <span class="itd-bc-sep">/</span>
  <span class="itd-bc-cur">Add New Item</span>
</div>

<div class="wiz-container">
    
    {{-- WIZARD STEPS INDICATOR --}}
    <div class="wiz-steps">
        <div class="wiz-step active" id="step-indicator-1">
            <div class="wiz-step-num">1</div>
            <div class="wiz-step-label">Identity</div>
        </div>
        <div class="wiz-step-line"></div>
        <div class="wiz-step" id="step-indicator-2">
            <div class="wiz-step-num">2</div>
            <div class="wiz-step-label">Technical</div>
        </div>
        <div class="wiz-step-line"></div>
        <div class="wiz-step" id="step-indicator-3">
            <div class="wiz-step-num">3</div>
            <div class="wiz-step-label">Media & Status</div>
        </div>
    </div>

    <form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data" id="item-wizard-form">
        @csrf

        {{-- STEP 1: IDENTITY --}}
        <div class="wiz-card active" id="step-1">
            <div class="wiz-card-head">
                <h3 class="wiz-card-title">Item Identity</h3>
                <p class="wiz-card-sub">Basic naming and categorization of the asset</p>
            </div>
            <div class="wiz-card-body">
                <div class="wiz-form-grid">
                    <div class="wiz-form-group full">
                        <label class="wiz-label">Item Name <span class="wiz-req">*</span></label>
                        <input type="text" name="name" class="wiz-input" placeholder="e.g. 30 Span Tent Main Beam" required autofocus>
                    </div>
                    <div class="wiz-form-group">
                        <label class="wiz-label">Category <span class="wiz-req">*</span></label>
                        <select name="category" class="wiz-input" required>
                            <option value="">Select Category...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="wiz-form-group">
                        <label class="wiz-label">Brand</label>
                        <input type="text" name="brand" class="wiz-input" placeholder="e.g. EuroTents, Sony...">
                    </div>
                    <div class="wiz-form-group">
                        <label class="wiz-label">Model Number</label>
                        <input type="text" name="model_number" class="wiz-input" placeholder="e.g. ET-30S-2024">
                    </div>
                    <div class="wiz-form-group">
                        <label class="wiz-label">Serial Number</label>
                        <input type="text" name="serial_number" class="wiz-input" placeholder="e.g. SN-992031-B">
                    </div>
                </div>
            </div>
            <div class="wiz-card-foot">
                <a href="{{ route('inventory.index') }}" class="wiz-btn-outline">Cancel</a>
                <button type="button" class="wiz-btn-red" onclick="goToStep(2)">Next Step: Technical</button>
            </div>
        </div>

        {{-- STEP 2: TECHNICAL & LOGISTICS --}}
        <div class="wiz-card" id="step-2" style="display:none">
            <div class="wiz-card-head">
                <h3 class="wiz-card-title">Technical Specifications</h3>
                <p class="wiz-card-sub">Detailed features, dimensions and purchase info</p>
            </div>
            <div class="wiz-card-body">
                <div class="wiz-form-grid">
                    <div class="wiz-form-group">
                        <label class="wiz-label">Dimensions</label>
                        <input type="text" name="dimensions" class="wiz-input" placeholder="e.g. 300cm x 50cm x 10cm">
                    </div>
                    <div class="wiz-form-group">
                        <label class="wiz-label">Weight</label>
                        <input type="text" name="weight" class="wiz-input" placeholder="e.g. 45kg">
                    </div>
                    <div class="wiz-form-group">
                        <label class="wiz-label">Purchase Date</label>
                        <input type="date" name="purchase_date" class="wiz-input">
                    </div>
                    <div class="wiz-form-group">
                        <label class="wiz-label">Purchase Cost (KES)</label>
                        <input type="number" name="purchase_cost" class="wiz-input" placeholder="0.00">
                    </div>
                    <div class="wiz-form-group full">
                        <label class="wiz-label">Technical Specifications</label>
                        <textarea name="specifications" class="wiz-textarea" rows="4" placeholder="List key features, power ratings, material types..."></textarea>
                    </div>
                </div>
            </div>
            <div class="wiz-card-foot">
                <button type="button" class="wiz-btn-outline" onclick="goToStep(1)">Back</button>
                <button type="button" class="wiz-btn-red" onclick="goToStep(3)">Next Step: Finalize</button>
            </div>
        </div>

        {{-- STEP 3: MEDIA & STATUS --}}
        <div class="wiz-card" id="step-3" style="display:none">
            <div class="wiz-card-head">
                <h3 class="wiz-card-title">Media & Initial Status</h3>
                <p class="wiz-card-sub">Set the starting location and upload primary image</p>
            </div>
            <div class="wiz-card-body">
                <div class="wiz-form-grid">
                    
                    <div class="wiz-form-group full">
                        <label class="wiz-label">Primary Item Image</label>
                        <div class="wiz-file-upload">
                            <input type="file" name="image" id="image-input" class="wiz-file-input" accept="image/*" onchange="previewImage(this)">
                            <div class="wiz-file-placeholder" id="image-placeholder">
                                <svg width="32" height="32" viewBox="0 0 16 16" fill="none" stroke="#d0c8c0" stroke-width="1"><rect x="1" y="3" width="14" height="10" rx="2"/><circle cx="6" cy="8" r="1.5"/><path d="M1 11l3.5-3.5 2.5 2.5 2-2 4 4"/></svg>
                                <span>Click to upload or drag image here</span>
                                <small>JPG, PNG or WEBP (max 4MB)</small>
                            </div>
                            <div class="wiz-file-preview" id="image-preview" style="display:none">
                                <img src="" alt="Preview" id="preview-img">
                                <button type="button" class="wiz-file-remove" onclick="removeImage()">Remove</button>
                            </div>
                        </div>
                    </div>

                    <div class="wiz-form-group">
                        <label class="wiz-label">Initial Status <span class="wiz-req">*</span></label>
                        <select name="status" class="wiz-input" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="wiz-form-group">
                        <label class="wiz-label">Warehouse Location <span class="wiz-req">*</span></label>
                        <select name="location" class="wiz-input" required>
                            @foreach($locations as $loc)
                                <option value="{{ $loc }}">{{ $loc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="wiz-form-group full">
                        <label class="wiz-label">Number of Pieces</label>
                        <input type="number" name="total_pieces" class="wiz-input" value="1" min="1" placeholder="1">
                        <small style="display:block;font-size:10px;color:#a09890;margin-top:4px">Each piece will be assigned a unique tracking code automatically</small>
                    </div>
                    <div class="wiz-form-group full">
                        <label class="wiz-label">Internal Notes</label>
                        <textarea name="notes" class="wiz-textarea" rows="3" placeholder="Any additional internal comments..."></textarea>
                    </div>
                </div>
            </div>
            <div class="wiz-card-foot">
                <button type="button" class="wiz-btn-outline" onclick="goToStep(2)">Back</button>
                <button type="submit" class="wiz-btn-red" id="submit-btn">Create Item & Finish</button>
            </div>
        </div>

    </form>
</div>

<style>
.wiz-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px 0 60px;
}

/* Steps Indicator */
.wiz-steps {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 30px;
    padding: 0 10px;
}
.wiz-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    position: relative;
    z-index: 2;
}
.wiz-step-num {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #fff;
    border: 2px solid #ece8e3;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 700;
    color: #b0a8a0;
    transition: all 0.3s;
}
.wiz-step.active .wiz-step-num {
    background: #CC0000;
    border-color: #CC0000;
    color: #fff;
    box-shadow: 0 4px 10px rgba(204,0,0,0.2);
}
.wiz-step.completed .wiz-step-num {
    background: #3B6D11;
    border-color: #3B6D11;
    color: #fff;
}
.wiz-step-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #a09890;
}
.wiz-step.active .wiz-step-label {
    color: #0f0f0f;
}
.wiz-step-line {
    flex: 1;
    height: 2px;
    background: #ece8e3;
    margin: 0 15px;
    margin-top: -20px;
    position: relative;
    z-index: 1;
}

/* Wizard Card */
.wiz-card {
    background: #fff;
    border: 1px solid #ece8e3;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
}
.wiz-card-head {
    padding: 24px 30px;
    border-bottom: 1px solid #f5f1ed;
}
.wiz-card-title {
    font-size: 18px;
    font-weight: 700;
    color: #0f0f0f;
    margin: 0;
}
.wiz-card-sub {
    font-size: 12px;
    color: #a09890;
    margin: 4px 0 0;
}
.wiz-card-body {
    padding: 30px;
}
.wiz-card-foot {
    padding: 20px 30px;
    background: #fafafa;
    border-top: 1px solid #f5f1ed;
    display: flex;
    justify-content: space-between;
}

/* Form Styling */
.wiz-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
.wiz-form-group.full {
    grid-column: 1 / -1;
}
.wiz-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #5c5550;
    margin-bottom: 8px;
}
.wiz-req { color: #CC0000; }
.wiz-optional { color: #b0a8a0; font-weight: 400; font-style: italic; }
.wiz-input, .wiz-textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #ece8e3;
    border-radius: 8px;
    font-size: 13px;
    font-family: inherit;
    color: #0f0f0f;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.wiz-input:focus, .wiz-textarea:focus {
    outline: none;
    border-color: #CC0000;
    box-shadow: 0 0 0 3px rgba(204,0,0,0.05);
}
.wiz-textarea { resize: vertical; }

/* File Upload */
.wiz-file-upload {
    position: relative;
    border: 2px dashed #ece8e3;
    border-radius: 10px;
    padding: 30px;
    text-align: center;
    transition: background 0.2s, border-color 0.2s;
    cursor: pointer;
}
.wiz-file-upload:hover {
    background: #fdf9f8;
    border-color: #CC0000;
}
.wiz-file-input {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}
.wiz-file-placeholder svg { margin-bottom: 12px; }
.wiz-file-placeholder span {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #5c5550;
    margin-bottom: 4px;
}
.wiz-file-placeholder small {
    font-size: 11px;
    color: #a09890;
}
.wiz-file-preview img {
    max-width: 100%;
    max-height: 200px;
    border-radius: 6px;
    display: block;
    margin: 0 auto 15px;
}
.wiz-file-remove {
    background: #fcebeb;
    color: #A32D2D;
    border: none;
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
}

/* Buttons */
.wiz-btn-red {
    background: #CC0000;
    color: #fff;
    border: none;
    padding: 10px 24px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}
.wiz-btn-red:hover { background: #aa0000; }
.wiz-btn-outline {
    background: #fff;
    color: #5c5550;
    border: 1px solid #ece8e3;
    padding: 10px 24px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}
.wiz-btn-outline:hover {
    border-color: #CC0000;
    color: #CC0000;
}
</style>

<script>
function goToStep(step) {
    // Hide all steps
    document.querySelectorAll('.wiz-card').forEach(card => card.style.display = 'none');
    
    // Show target step
    document.getElementById('step-' + step).style.display = 'block';
    
    // Update indicators
    document.querySelectorAll('.wiz-step').forEach((indicator, index) => {
        const iStep = index + 1;
        if (iStep < step) {
            indicator.classList.remove('active');
            indicator.classList.add('completed');
        } else if (iStep === step) {
            indicator.classList.remove('completed');
            indicator.classList.add('active');
        } else {
            indicator.classList.remove('active', 'completed');
        }
    });

    // Scroll to top of wizard
    window.scrollTo({ top: document.querySelector('.wiz-steps').offsetTop - 100, behavior: 'smooth' });
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-placeholder').style.display = 'none';
            document.getElementById('image-preview').style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    document.getElementById('image-input').value = '';
    document.getElementById('image-placeholder').style.display = 'block';
    document.getElementById('image-preview').style.display = 'none';
}

// Form validation before moving to next step
document.getElementById('item-wizard-form').addEventListener('submit', function() {
    document.getElementById('submit-btn').innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24">...</svg> Creating...';
    document.getElementById('submit-btn').disabled = true;
});
</script>

@endsection
