{{-- Receive Wizard Modal --}}
<div id="receive-wizard-modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.85); backdrop-filter:blur(6px); z-index:9999; padding:20px; overflow-y:auto;">
  <div style="max-width:520px; margin:60px auto; background:#fff; border-radius:20px; box-shadow:0 20px 60px rgba(0,0,0,0.4); position:relative;">

    {{-- Header --}}
    <div style="background:linear-gradient(135deg, #CC0000 0%, #aa0000 100%); color:#fff; padding:28px 32px; border-radius:20px 20px 0 0; text-align:center;">
      <div style="font-size:40px; margin-bottom:12px;">📥</div>
      <h2 style="font-size:22px; font-weight:700; margin:0 0 6px 0; letter-spacing:-0.02em;">Receive Session Ready</h2>
      <p style="font-size:13px; opacity:0.95; margin:0;">{{ $event->name }}</p>
    </div>

    {{-- Body --}}
    <div style="padding:32px;">

      {{-- Step 1 --}}
      <div style="margin-bottom:28px;">
        <div style="display:flex; align-items:flex-start; gap:16px;">
          <div style="width:36px; height:36px; border-radius:50%; background:#eaf3de; color:#3B6D11; font-size:16px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0;">1</div>
          <div style="flex:1;">
            <h3 style="font-size:15px; font-weight:700; color:#0f0f0f; margin:0 0 10px 0;">Scan the QR Code</h3>
            <p style="font-size:13px; color:#5c5550; line-height:1.6; margin:0 0 16px 0;">
              Use your phone camera or QR scanner app to scan this code. This will open the mobile receive interface.
            </p>
            <div style="background:#f8f7f5; border:2px solid #ece8e3; border-radius:12px; padding:20px; text-align:center;">
              <div style="max-width:200px; margin:0 auto;">
                {!! $qrCodeSvg !!}
              </div>
              <p style="font-size:11px; color:#a09890; margin:12px 0 0 0; font-family:'Courier New',monospace;">
                {{ $receiveSession->session_token }}
              </p>
            </div>
          </div>
        </div>
      </div>

      {{-- Step 2 --}}
      <div style="margin-bottom:28px;">
        <div style="display:flex; align-items:flex-start; gap:16px;">
          <div style="width:36px; height:36px; border-radius:50%; background:#eaf3de; color:#3B6D11; font-size:16px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0;">2</div>
          <div style="flex:1;">
            <h3 style="font-size:15px; font-weight:700; color:#0f0f0f; margin:0 0 10px 0;">Scan Each Dispatched Piece</h3>
            <p style="font-size:13px; color:#5c5550; line-height:1.6; margin:0;">
              Scan the QR codes of <strong>specific pieces that were dispatched</strong> to this event. For each piece, select condition on return and destination (warehouse, cleaning, or repair).
            </p>
          </div>
        </div>
      </div>

      {{-- Step 3 --}}
      <div style="margin-bottom:28px;">
        <div style="display:flex; align-items:flex-start; gap:16px;">
          <div style="width:36px; height:36px; border-radius:50%; background:#eaf3de; color:#3B6D11; font-size:16px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0;">3</div>
          <div style="flex:1;">
            <h3 style="font-size:15px; font-weight:700; color:#0f0f0f; margin:0 0 10px 0;">Monitor Progress Here</h3>
            <p style="font-size:13px; color:#5c5550; line-height:1.6; margin:0;">
              This dashboard will update in real-time as pieces are received. Track progress per item, verify borrowed/operational items, and confirm when complete.
            </p>
          </div>
        </div>
      </div>

      {{-- Info Banner --}}
      <div style="background:#fff8f8; border-left:4px solid #CC0000; padding:14px 16px; border-radius:8px; margin-bottom:24px;">
        <p style="font-size:12px; color:#7a0000; line-height:1.6; margin:0;">
          <strong>Important:</strong> Only pieces dispatched for this event can be received. Scan the exact QR codes of items sent to {{ $event->venue }}.
        </p>
      </div>

      {{-- Session Info --}}
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:24px;">
        <div style="background:#faf8f6; border-radius:8px; padding:12px;">
          <div style="font-size:10px; color:#a09890; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Session Expires</div>
          <div style="font-size:13px; font-weight:600; color:#0f0f0f;">{{ $receiveSession->expires_at->format('H:i, d M Y') }}</div>
        </div>
        <div style="background:#faf8f6; border-radius:8px; padding:12px;">
          <div style="font-size:10px; color:#a09890; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Total to Receive</div>
          <div style="font-size:13px; font-weight:600; color:#0f0f0f;">{{ $receiveSession->total_pieces ?? 0 }} pieces</div>
        </div>
      </div>

      {{-- Action Buttons --}}
      <div style="display:flex; gap:10px;">
        <button onclick="closeReceiveWizard()" style="flex:1; background:#fff; color:#5c5550; border:1.5px solid #ece8e3; padding:12px 20px; border-radius:10px; font-size:14px; font-weight:600; cursor:pointer;">
          Close
        </button>
        <button onclick="closeReceiveWizard()" style="flex:1; background:#CC0000; color:#fff; border:none; padding:12px 20px; border-radius:10px; font-size:14px; font-weight:700; cursor:pointer;">
          Start Receiving
        </button>
      </div>

    </div>

  </div>
</div>

<script>
// Modal is now disabled - removed auto-show
// Users go directly to monitor dashboard

function closeReceiveWizard() {
  document.getElementById('receive-wizard-modal').style.display = 'none';
}

// ESC key to close
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeReceiveWizard();
  }
});
</script>
