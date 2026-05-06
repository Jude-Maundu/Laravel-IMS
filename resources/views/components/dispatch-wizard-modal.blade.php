{{-- Dispatch Wizard Modal Component --}}
@props(['event', 'scanSession', 'qrCodeSvg'])

<div id="dispatch-wizard-modal" class="dwm-overlay" style="display:none;">
  <div class="dwm-card">

    {{-- Header --}}
    <div class="dwm-header">
      <button type="button" onclick="closeDispatchModal()" class="dwm-close-btn" aria-label="Close modal">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
      <h2 class="dwm-title">Start Dispatch Session</h2>
      <p class="dwm-subtitle">{{ $event->name }}</p>
      <p class="dwm-meta">{{ $event->venue }} • {{ $event->loading_date->format('d M Y') }}</p>
    </div>

    {{-- All Steps in One Row --}}
    <div class="dwm-secondary-section">

      {{-- Step 1: Print Packing List --}}
      <div class="dwm-action-card dwm-action-card-primary">
        <div>
          <div class="dwm-badge-recommended">RECOMMENDED</div>
          <div class="dwm-icon-secondary">📄</div>
          <h4 class="dwm-action-title">Step 1: Print Packing List</h4>
          <p class="dwm-action-description">Download the dispatch checklist with QR code, item images, and checkboxes</p>
        </div>
        <button type="button"
                id="dwm-download-btn"
                class="dwm-btn-primary"
                onclick="downloadPackingList('{{ route('events.packing-list.dispatch', [$event, $scanSession]) }}')">
          <svg id="dwm-download-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="12" y1="18" x2="12" y2="12"></line>
            <line x1="9" y1="15" x2="15" y2="15"></line>
          </svg>
          <svg id="dwm-loading-icon" style="display:none;" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M12 6v6l4 2"></path>
          </svg>
          <svg id="dwm-success-icon" style="display:none;" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <span id="dwm-download-text">Download & Print</span>
        </button>
      </div>

      {{-- Step 2: Scan QR --}}
      <div class="dwm-action-card">
        <div>
          <div class="dwm-icon-secondary">📱</div>
          <h4 class="dwm-action-title">Step 2: Scan QR Code</h4>
          <p class="dwm-action-description">Point phone camera at QR code to start scanning</p>
        </div>
        <button type="button" onclick="toggleQRDisplay()" class="dwm-btn-outline">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="3" y="3" width="7" height="7"></rect>
            <rect x="14" y="3" width="7" height="7"></rect>
            <rect x="14" y="14" width="7" height="7"></rect>
            <rect x="3" y="14" width="7" height="7"></rect>
          </svg>
          Show QR Code
        </button>
        <div id="dwm-qr-display" style="display:none; margin-top:16px; padding-top:16px; border-top:1px solid #ece8e3;">
          <div style="text-align:center;">
            {!! $qrCodeSvg !!}
          </div>
          <p style="font-size:10px; color:#a09890; text-align:center; margin-top:8px;">
            Session expires: {{ $scanSession->expires_at->format('d M Y, H:i') }} EAT
          </p>
        </div>
      </div>

      {{-- Step 3: Share Link --}}
      <div class="dwm-action-card">
        <div>
          <div class="dwm-icon-secondary">💬</div>
          <h4 class="dwm-action-title">Step 3: Share Link</h4>
          <p class="dwm-action-description">Send session link to warehouse staff via WhatsApp</p>
        </div>
        <a href="https://wa.me/?text={{ urlencode('Grey Apple IMS — Dispatch session for ' . $event->name . ' is ready. Scan items here: ' . config('app.url') . '/scan/' . $scanSession->session_token) }}"
           target="_blank"
           class="dwm-btn-whatsapp"
           onclick="markWizardSeen()">
          <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
          </svg>
          Share via WhatsApp
        </a>
      </div>

    </div>

    {{-- Footer --}}
    <div class="dwm-footer">
      <button type="button" onclick="closeWizardGoMonitor()" class="dwm-btn-text">
        Skip to Monitor Dashboard →
      </button>
    </div>

  </div>
</div>

<style>
/* Dispatch Wizard Modal Styles */
.dwm-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.75);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 20px;
}

.dwm-card {
  background: #ffffff;
  border-radius: 16px;
  max-width: 1400px;
  width: 92%;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  overflow: hidden;
  max-height: 90vh;
  overflow-y: auto;
}

/* Header */
.dwm-header {
  text-align: center;
  padding: 16px 32px 12px;
  border-bottom: 1px solid #ece8e3;
  background: linear-gradient(180deg, #fafafa 0%, #ffffff 100%);
  position: relative;
}

.dwm-close-btn {
  position: absolute;
  top: 12px;
  right: 12px;
  background: none;
  border: none;
  color: #5c5550;
  cursor: pointer;
  padding: 6px;
  border-radius: 6px;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
}

.dwm-close-btn:hover {
  background: #f8f6f3;
  color: #CC0000;
}

.dwm-title {
  font-size: 14px;
  font-weight: 700;
  color: #0f0f0f;
  margin-bottom: 5px;
}

.dwm-subtitle {
  font-size: 12px;
  font-weight: 600;
  color: #CC0000;
  margin-bottom: 2px;
}

.dwm-meta {
  font-size: 10px;
  color: #5c5550;
}

.dwm-badge-recommended {
  display: inline-block;
  background: #CC0000;
  color: #ffffff;
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.05em;
  padding: 3px 8px;
  border-radius: 3px;
  margin-bottom: 10px;
  text-transform: uppercase;
}

.dwm-btn-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  background: #CC0000;
  color: #ffffff;
  padding: 11px 20px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s ease;
  border: none;
  cursor: pointer;
  width: 100%;
}

.dwm-btn-primary:hover {
  background: #aa0000;
  transform: translateY(-1px);
  box-shadow: 0 4px 16px rgba(204, 0, 0, 0.3);
}


/* Secondary Section */
.dwm-secondary-section {
  padding: 24px 40px 32px;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  align-items: stretch;
}

.dwm-action-card {
  text-align: center;
  padding: 24px 20px;
  border: 1px solid #ece8e3;
  border-radius: 10px;
  transition: all 0.2s ease;
  background: #ffffff;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.dwm-action-card:hover {
  border-color: #CC0000;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  transform: translateY(-2px);
}

.dwm-icon-secondary {
  font-size: 32px;
  margin-bottom: 10px;
}

.dwm-action-title {
  font-size: 13px;
  font-weight: 700;
  color: #0f0f0f;
  margin-bottom: 6px;
}

.dwm-action-description {
  font-size: 11px;
  color: #a09890;
  margin-bottom: 14px;
  line-height: 1.5;
}

.dwm-action-card-primary {
  background: linear-gradient(135deg, #fff5f5 0%, #ffffff 100%);
  border-color: #CC0000;
  position: relative;
}

.dwm-btn-outline {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  background: #ffffff;
  border: 2px solid #ece8e3;
  color: #0f0f0f;
  padding: 11px 20px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  width: 100%;
}

.dwm-btn-outline:hover {
  border-color: #CC0000;
  color: #CC0000;
  background: #fff5f5;
}

.dwm-btn-whatsapp {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  background: #25D366;
  color: #ffffff;
  padding: 11px 20px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s ease;
  border: none;
  cursor: pointer;
  width: 100%;
}

.dwm-btn-whatsapp:hover {
  background: #20bd5a;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
}

/* Footer */
.dwm-footer {
  text-align: center;
  padding: 20px 32px;
  border-top: 1px solid #ece8e3;
  background: #faf8f6;
}

.dwm-btn-text {
  background: none;
  border: none;
  color: #5c5550;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: color 0.2s ease;
  text-decoration: underline;
  padding: 0;
}

.dwm-btn-text:hover {
  color: #CC0000;
}

/* Animations */
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.dwm-btn-primary:disabled {
  opacity: 0.8;
  cursor: not-allowed;
}

.dwm-btn-primary:disabled:hover {
  transform: none;
  box-shadow: none;
}

/* Responsive */
@media (max-width: 1024px) {
  .dwm-secondary-section {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .dwm-card {
    margin: 10px;
    width: 95%;
  }

  .dwm-header,
  .dwm-primary-section,
  .dwm-divider,
  .dwm-secondary-section,
  .dwm-footer {
    padding-left: 20px;
    padding-right: 20px;
  }
}
</style>

<script>
function toggleQRDisplay() {
  const qrDisplay = document.getElementById('dwm-qr-display');
  qrDisplay.style.display = qrDisplay.style.display === 'none' ? 'block' : 'none';
}

function markWizardSeen() {
  sessionStorage.setItem('dispatch_wizard_seen_{{ $event->id }}', 'true');
}

function closeDispatchModal() {
  document.getElementById('dispatch-wizard-modal').style.display = 'none';
}

function closeWizardGoMonitor() {
  markWizardSeen();
  closeDispatchModal();
}

async function downloadPackingList(url) {
  const btn = document.getElementById('dwm-download-btn');
  const downloadIcon = document.getElementById('dwm-download-icon');
  const loadingIcon = document.getElementById('dwm-loading-icon');
  const successIcon = document.getElementById('dwm-success-icon');
  const btnText = document.getElementById('dwm-download-text');

  // Show loading state
  btn.disabled = true;
  downloadIcon.style.display = 'none';
  successIcon.style.display = 'none';
  loadingIcon.style.display = 'inline';
  btnText.textContent = 'Generating PDF...';
  loadingIcon.style.animation = 'spin 1s linear infinite';

  try {
    // Fetch the PDF
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    if (!response.ok) {
      throw new Error('Failed to download PDF');
    }

    // Get the blob
    const blob = await response.blob();

    // Create download link
    const downloadUrl = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = downloadUrl;
    a.download = 'DISPATCH-PACKING-LIST-FOR-{{ strtoupper(str_replace(" ", "-", $event->name)) }}.pdf';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(downloadUrl);

    // Show success state
    loadingIcon.style.display = 'none';
    successIcon.style.display = 'inline';
    btnText.textContent = 'Downloaded!';

    // Mark wizard as seen
    markWizardSeen();

    // Reset button after 2 seconds
    setTimeout(() => {
      btn.disabled = false;
      successIcon.style.display = 'none';
      downloadIcon.style.display = 'inline';
      btnText.textContent = 'Download & Print';
    }, 2000);

  } catch (error) {
    console.error('Download error:', error);

    // Show error state
    loadingIcon.style.display = 'none';
    downloadIcon.style.display = 'inline';
    btnText.textContent = 'Download Failed';
    btn.style.background = '#CC0000';

    // Reset button after 2 seconds
    setTimeout(() => {
      btn.disabled = false;
      btnText.textContent = 'Download & Print';
      btn.style.background = '';
    }, 2000);
  }
}

// Modal is now disabled - removed auto-show
// Users go directly to monitor dashboard

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    const modal = document.getElementById('dispatch-wizard-modal');
    if (modal && modal.style.display === 'flex') {
      closeDispatchModal();
    }
  }
});

// Close modal when clicking outside
document.getElementById('dispatch-wizard-modal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeDispatchModal();
  }
});
</script>
