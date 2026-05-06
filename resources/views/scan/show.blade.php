<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="theme-color" content="#CC0000">
    <title>Scan Session — {{ $session->event->name }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: #0f0f0f;
            overflow: hidden;
            position: fixed;
            width: 100%;
            height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ─── TOP BAR ─────────────────────────────────────── */
        .scan-topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 80px;
            background: #CC0000;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 0 16px;
            z-index: 100;
            padding-top: env(safe-area-inset-top);
        }

        .scan-topbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .scan-logo-img {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .scan-company-name {
            font-size: 11px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .scan-topbar-event {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .scan-topbar-left {
            flex: 1;
            min-width: 0;
        }

        .scan-event-name {
            font-size: 13px;
            font-weight: 600;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 2px;
        }

        .scan-event-venue {
            font-size: 10px;
            color: rgba(255,255,255,0.75);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .scan-topbar-right {
            text-align: right;
            flex-shrink: 0;
            margin-left: 12px;
        }

        .scan-count {
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1;
            margin-bottom: 4px;
        }

        .scan-count-sep {
            opacity: 0.6;
            margin: 0 2px;
        }

        .scan-count-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(255,255,255,0.75);
        }

        /* ─── PROGRESS BAR ───────────────────────────────── */
        .scan-progress-wrap {
            position: fixed;
            top: calc(80px + env(safe-area-inset-top));
            left: 0;
            right: 0;
            height: 4px;
            background: rgba(255,255,255,0.2);
            z-index: 99;
        }

        .scan-progress-bar {
            height: 100%;
            background: #166534;
            transition: width 0.4s ease;
        }

        /* ─── MAIN SCAN AREA ─────────────────────────────── */
        .scan-main {
            position: fixed;
            top: calc(84px + env(safe-area-inset-top));
            left: 0;
            right: 0;
            bottom: calc(72px + env(safe-area-inset-bottom));
            background: #000000;
        }

        .scan-viewfinder-wrap {
            width: 100%;
            height: calc(100% - 80px);
            position: relative;
            overflow: hidden;
        }

        #qr-reader {
            width: 100% !important;
            height: 100% !important;
            border: none !important;
        }

        #qr-reader > div,
        #qr-reader video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }

        .scan-target-frame {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 220px;
            height: 220px;
            pointer-events: none;
        }

        .scan-target-frame::before,
        .scan-target-frame::after {
            content: '';
            position: absolute;
            width: 40px;
            height: 40px;
            border: 3px solid #ffffff;
        }

        .scan-target-frame::before {
            top: 0;
            left: 0;
            border-right: none;
            border-bottom: none;
        }

        .scan-target-frame::after {
            top: 0;
            right: 0;
            border-left: none;
            border-bottom: none;
        }

        .scan-target-frame:has(+ *) {
            /* Additional corner brackets via pseudo-siblings */
        }

        /* Bottom corners */
        .scan-viewfinder-wrap::before,
        .scan-viewfinder-wrap::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 220px;
            height: 220px;
            pointer-events: none;
        }

        .scan-viewfinder-wrap::before {
            border: 3px solid #ffffff;
            border-top: none;
            border-right: none;
            width: 40px;
            height: 40px;
            transform: translate(calc(-50% - 90px), calc(-50% + 90px));
        }

        .scan-viewfinder-wrap::after {
            border: 3px solid #ffffff;
            border-top: none;
            border-left: none;
            width: 40px;
            height: 40px;
            transform: translate(calc(-50% + 90px), calc(-50% + 90px));
        }

        .scan-suggestion {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.85);
            padding: 12px 16px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .scan-suggestion-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(255,255,255,0.6);
            margin-bottom: 4px;
        }

        .scan-suggestion-item {
            font-size: 15px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 2px;
        }

        .scan-suggestion-remaining {
            font-size: 12px;
            color: rgba(255,255,255,0.75);
        }

        /* ─── OVERLAYS ───────────────────────────────────── */
        .scan-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.92);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 200;
            padding: 20px;
        }

        .scan-overlay-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 32px 24px;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .scan-overlay-check {
            width: 64px;
            height: 64px;
            background: #166534;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin: 0 auto 20px;
        }

        .scan-overlay-img-wrap {
            margin-bottom: 16px;
        }

        .scan-overlay-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e5e7eb;
        }

        .scan-overlay-img-placeholder {
            width: 120px;
            height: 120px;
            background: #f3f4f6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            margin: 0 auto;
        }

        .scan-overlay-item-name {
            font-size: 18px;
            font-weight: 700;
            color: #0f0f0f;
            margin-bottom: 6px;
        }

        .scan-overlay-code {
            font-size: 13px;
            font-weight: 600;
            color: #CC0000;
            font-family: 'Courier New', monospace;
            margin-bottom: 4px;
        }

        .scan-overlay-category {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 24px;
        }

        .scan-condition-label {
            font-size: 12px;
            font-weight: 600;
            color: #0f0f0f;
            margin-bottom: 12px;
        }

        .scan-condition-btns {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-bottom: 8px;
        }

        .scan-cond-btn {
            flex: 1;
            max-width: 60px;
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px 4px;
            font-size: 16px;
            font-weight: 700;
            color: #0f0f0f;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
        }

        .scan-cond-btn span {
            font-size: 9px;
            font-weight: 500;
            color: #6b7280;
        }

        .scan-cond-btn:active,
        .scan-cond-btn.active {
            background: #166534;
            border-color: #166534;
            color: #ffffff;
        }

        .scan-cond-btn.active span {
            color: rgba(255,255,255,0.9);
        }

        .scan-overlay-hint {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 12px;
        }

        /* ─── ERROR OVERLAY ──────────────────────────────── */
        .scan-overlay-card-error {
            background: #fee2e2;
        }

        .scan-overlay-error-icon {
            width: 64px;
            height: 64px;
            background: #991b1b;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin: 0 auto 20px;
        }

        .scan-overlay-error-title {
            font-size: 18px;
            font-weight: 700;
            color: #991b1b;
            margin-bottom: 8px;
        }

        .scan-overlay-error-msg {
            font-size: 14px;
            color: #7f1d1d;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .scan-overlay-dismiss {
            background: #991b1b;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .scan-overlay-warning .scan-overlay-card-error {
            background: #fef9c3;
        }

        .scan-overlay-warning .scan-overlay-error-icon {
            background: #854d0e;
        }

        .scan-overlay-warning .scan-overlay-error-title {
            color: #854d0e;
        }

        .scan-overlay-warning .scan-overlay-error-msg {
            color: #713f12;
        }

        .scan-overlay-warning .scan-overlay-dismiss {
            background: #854d0e;
        }

        /* ─── MANUAL ENTRY ───────────────────────────────── */
        .scan-manual-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f0f0f;
            margin-bottom: 8px;
        }

        .scan-manual-hint {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 20px;
        }

        .scan-manual-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            font-family: 'Courier New', monospace;
            text-transform: uppercase;
            color: #0f0f0f;
            margin-bottom: 12px;
        }

        .scan-manual-input:focus {
            outline: none;
            border-color: #CC0000;
        }

        .scan-manual-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px 12px;
            border-radius: 6px;
            font-size: 12px;
            margin-bottom: 12px;
        }

        .scan-manual-actions {
            display: flex;
            gap: 12px;
        }

        .scan-manual-cancel {
            flex: 1;
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 600;
            color: #0f0f0f;
            cursor: pointer;
        }

        .scan-manual-submit {
            flex: 1;
            background: #CC0000;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
            cursor: pointer;
        }

        /* ─── COMPLETE OVERLAY ───────────────────────────── */
        .scan-overlay-card-complete {
            background: #dcfce7;
        }

        .scan-complete-icon {
            font-size: 64px;
            margin-bottom: 16px;
        }

        .scan-complete-title {
            font-size: 22px;
            font-weight: 700;
            color: #166534;
            margin-bottom: 8px;
        }

        .scan-complete-sub {
            font-size: 14px;
            color: #15803d;
            margin-bottom: 4px;
        }

        .scan-complete-event {
            font-size: 13px;
            color: #15803d;
            font-weight: 600;
            margin-bottom: 24px;
        }

        .scan-complete-btn {
            width: 100%;
            background: #166534;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 16px 24px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
        }

        .scan-complete-loading {
            font-size: 14px;
            color: #15803d;
            padding: 16px;
        }

        /* ─── BOTTOM BAR ─────────────────────────────────── */
        .scan-bottombar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: calc(72px + env(safe-area-inset-bottom));
            background: #ffffff;
            border-top: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            padding-bottom: calc(12px + env(safe-area-inset-bottom));
            z-index: 100;
        }

        .scan-bottom-btn {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 600;
            color: #0f0f0f;
            cursor: pointer;
        }

        .scan-bottom-btn-exit {
            background: #fff1f2;
            border-color: #fecdd3;
            color: #991b1b;
        }

        .scan-session-ref {
            font-size: 10px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Custom Confirm Modal for Mobile */
        .custom-confirm-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: fadeIn 0.2s ease;
        }
        .custom-confirm-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(4px);
        }
        .custom-confirm-box {
            position: relative;
            background: #ffffff;
            border-radius: 16px;
            padding: 28px 24px;
            max-width: 360px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            animation: slideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .custom-confirm-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            background: linear-gradient(135deg, #fef3c7 0%, #fefce8 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #fde68a;
        }
        .custom-confirm-icon svg {
            color: #854F0B;
        }
        .custom-confirm-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f0f0f;
            margin: 0 0 10px 0;
        }
        .custom-confirm-message {
            font-size: 13px;
            color: #5c5550;
            line-height: 1.5;
            margin: 0 0 24px 0;
            white-space: pre-line;
        }
        .custom-confirm-actions {
            display: flex;
            gap: 10px;
        }
        .custom-confirm-cancel, .custom-confirm-ok {
            flex: 1;
            padding: 14px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        .custom-confirm-cancel {
            background: #f3f4f6;
            color: #5c5550;
        }
        .custom-confirm-ok {
            background: #854F0B;
            color: #ffffff;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
    </style>
</head>
<body>

    <!-- Custom Confirmation Modal for Mobile -->
    <div id="confirm-modal" class="custom-confirm-modal">
        <div class="custom-confirm-overlay" onclick="closeConfirmModal()"></div>
        <div class="custom-confirm-box">
            <div class="custom-confirm-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
            <h3 class="custom-confirm-title" id="confirm-title">Confirm Action</h3>
            <p class="custom-confirm-message" id="confirm-message">Are you sure?</p>
            <div class="custom-confirm-actions">
                <button class="custom-confirm-cancel" onclick="closeConfirmModal()">Cancel</button>
                <button class="custom-confirm-ok" id="confirm-ok-btn">Confirm</button>
            </div>
        </div>
    </div>

    <!-- TOP BAR -->
    <div class="scan-topbar">
        <div class="scan-topbar-brand">
            <img src="{{ asset('images/grey-apple-events-logo.png') }}" alt="Grey Apple Events" class="scan-logo-img">
            <div class="scan-company-name">Grey Apple Events</div>
        </div>
        <div class="scan-topbar-event">
            <div class="scan-topbar-left">
                <div class="scan-event-name">{{ Str::limit($session->event->name, 24) }}</div>
                <div class="scan-event-venue">{{ Str::limit($session->event->venue, 28) }}</div>
            </div>
            <div class="scan-topbar-right">
                <div class="scan-count" id="scan-count">
                    <span id="scanned-num">{{ $alreadyScanned }}</span>
                    <span class="scan-count-sep">/</span>
                    <span>{{ $totalRequired }}</span>
                </div>
                <div class="scan-count-label">scanned</div>
            </div>
        </div>
    </div>

    <!-- PROGRESS BAR -->
    <div class="scan-progress-wrap">
        <div class="scan-progress-bar" id="progress-bar"
             style="width: {{ $totalRequired > 0 ? round(($alreadyScanned / $totalRequired) * 100) : 0 }}%">
        </div>
    </div>

    <!-- MAIN SCAN AREA -->
    <div class="scan-main" id="scan-main">
        <!-- Camera viewfinder -->
        <div class="scan-viewfinder-wrap" id="viewfinder-wrap">
            <div id="qr-reader"></div>
            <div class="scan-target-frame"></div>
        </div>

        <!-- Suggested next item -->
        <div class="scan-suggestion" id="scan-suggestion">
            <div class="scan-suggestion-label">Suggested next</div>
            <div class="scan-suggestion-item" id="suggestion-name">
                Loading...
            </div>
            <div class="scan-suggestion-remaining" id="suggestion-remaining"></div>
        </div>
    </div>

    <!-- CONFIRMATION OVERLAY -->
    <div class="scan-overlay scan-overlay-success" id="overlay-success" style="display:none">
        <div class="scan-overlay-card">
            <div class="scan-overlay-check">✓</div>
            <div class="scan-overlay-img-wrap">
                <img id="confirm-img" src="" alt="" class="scan-overlay-img" style="display:none">
                <div class="scan-overlay-img-placeholder" id="confirm-img-placeholder">📦</div>
            </div>
            <div class="scan-overlay-item-name" id="confirm-name"></div>
            <div class="scan-overlay-code" id="confirm-code"></div>
            <div class="scan-overlay-category" id="confirm-category"></div>
            <div class="scan-condition-label">Select Condition</div>
            <div class="scan-condition-btns" id="condition-btns">
                <button class="scan-cond-btn" data-val="1">1<span>Poor</span></button>
                <button class="scan-cond-btn" data-val="2">2<span>Fair</span></button>
                <button class="scan-cond-btn" data-val="3">3<span>Good</span></button>
                <button class="scan-cond-btn" data-val="4">4<span>V.Good</span></button>
                <button class="scan-cond-btn" data-val="5">5<span>Excel</span></button>
            </div>
            <div class="scan-overlay-hint">Tap a condition to continue</div>
        </div>
    </div>

    <!-- ERROR OVERLAY -->
    <div class="scan-overlay scan-overlay-error" id="overlay-error" style="display:none">
        <div class="scan-overlay-card scan-overlay-card-error">
            <div class="scan-overlay-error-icon" id="error-icon">✕</div>
            <div class="scan-overlay-error-title" id="error-title"></div>
            <div class="scan-overlay-error-msg" id="error-msg"></div>
            <button class="scan-overlay-dismiss" onclick="dismissError()">
                Tap to Continue Scanning
            </button>
        </div>
    </div>

    <!-- MANUAL ENTRY OVERLAY -->
    <div class="scan-overlay" id="overlay-manual" style="display:none">
        <div class="scan-overlay-card">
            <div class="scan-manual-title">Enter Code Manually</div>
            <div class="scan-manual-hint">Type the unique code from the item sticker</div>
            <input type="text"
                   id="manual-code-input"
                   class="scan-manual-input"
                   placeholder="e.g. GA-FOG-023"
                   autocomplete="off"
                   autocorrect="off"
                   autocapitalize="characters"
                   spellcheck="false">
            <div class="scan-manual-error" id="manual-error" style="display:none"></div>
            <div class="scan-manual-actions">
                <button onclick="closeManual()" class="scan-manual-cancel">Cancel</button>
                <button onclick="submitManual()" class="scan-manual-submit">Submit</button>
            </div>
        </div>
    </div>

    <!-- COMPLETE OVERLAY -->
    <div class="scan-overlay" id="overlay-complete" style="display:none">
        <div class="scan-overlay-card scan-overlay-card-complete">
            <div class="scan-complete-icon">🎉</div>
            <div class="scan-complete-title">All Items Scanned!</div>
            <div class="scan-complete-sub">
                {{ $totalRequired }} items ready for dispatch
            </div>
            <div class="scan-complete-event">{{ $session->event->name }}</div>
            <button onclick="submitDispatch()" class="scan-complete-btn" id="submit-btn">
                Confirm Dispatch
            </button>
            <div class="scan-complete-loading" id="submit-loading" style="display:none">
                Submitting...
            </div>
        </div>
    </div>

    <!-- BOTTOM BAR -->
    <div class="scan-bottombar">
        <div class="scan-bottombar-left">
            <button onclick="openManual()" class="scan-bottom-btn">
                ✏️ Manual Entry
            </button>
        </div>
        <div class="scan-bottombar-center">
            <div class="scan-session-ref">Session Active</div>
        </div>
        <div class="scan-bottombar-right">
            <button onclick="saveAndExit()" class="scan-bottom-btn scan-bottom-btn-exit">
                Save & Exit
            </button>
        </div>
    </div>

    <script>
        // ── CONFIG ────────────────────────────────────────────────────
        const PROCESS_URL    = "{{ $processUrl }}";
        const SUBMIT_URL     = "{{ $submitUrl }}";
        const SAVE_PROG_URL  = "{{ $saveProgressUrl }}";
        const CSRF           = "{{ $csrfToken }}";
        const TOTAL_REQUIRED = {{ $totalRequired }};
        let   scannedCount   = {{ $alreadyScanned }};
        let   isProcessing   = false;
        let   scanner        = null;
        let   currentScanData = null;

        // Sound effects
        const successSound = new Audio("{{ asset('sounds/success.mp3') }}");
        const errorSound   = new Audio("{{ asset('sounds/error.mp3') }}");

        // Vibration helper
        function vibrate(pattern) {
            if ('vibrate' in navigator) {
                navigator.vibrate(pattern);
            }
        }

        // ── INITIALISE CAMERA ────────────────────────────────────────
        function initScanner() {
            scanner = new Html5Qrcode("qr-reader");

            const config = {
                fps: 10,
                qrbox: { width: 220, height: 220 },
                aspectRatio: window.innerHeight / window.innerWidth,
                rememberLastUsedCamera: true,
            };

            scanner.start(
                { facingMode: "environment" },
                config,
                onScanSuccess,
                onScanError
            ).catch(err => {
                showCameraError(err);
            });
        }

        // ── ON SCAN SUCCESS ──────────────────────────────────────────
        async function onScanSuccess(decodedText) {
            if (isProcessing) return;
            isProcessing = true;

            await scanner.pause(true);

            let uniqueCode = decodedText;
            try {
                const url = new URL(decodedText);
                const parts = url.pathname.split('/').filter(Boolean);
                uniqueCode = parts[parts.length - 1].toUpperCase();
            } catch(e) {
                uniqueCode = decodedText.toUpperCase();
            }

            await processScan(uniqueCode);
        }

        function onScanError(error) {
            // Silent
        }

        // ── PROCESS SCAN ─────────────────────────────────────────────
        async function processScan(uniqueCode) {
            try {
                const response = await fetch(PROCESS_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        unique_code: uniqueCode,
                    }),
                });

                const data = await response.json();

                // Check for session expiry or access issues
                if (response.status === 410) {
                    showSessionExpired(data.message || 'Your scan session has expired.');
                    return;
                }

                if (response.status === 403) {
                    showSessionExpired('You no longer have access to this session.');
                    return;
                }

                handleScanResponse(data, uniqueCode);

            } catch(err) {
                showError('Connection Error', 'Could not reach the server. Check your internet connection.');
                isProcessing = false;
                resumeScanner();
            }
        }

        // ── HANDLE SCAN RESPONSE ─────────────────────────────────────
        function handleScanResponse(data, uniqueCode) {
            if (data.status === 'success') {
                currentScanData = data;
                successSound.play().catch(() => {});
                vibrate([50, 50, 50]); // Triple short vibration
                showConfirmation(data);

            } else if (data.code === 'ALREADY_SCANNED') {
                errorSound.play().catch(() => {});
                vibrate([200]); // Long warning vibration
                showWarning('Already Scanned', data.message);
                isProcessing = false;
                resumeScanner();

            } else {
                errorSound.play().catch(() => {});
                vibrate([100, 50, 100]); // Error pattern
                showError(getErrorTitle(data.code), data.message);
                isProcessing = false;
                resumeScanner();
            }
        }

        // ── SHOW CONFIRMATION ────────────────────────────────────────
        function showConfirmation(data) {
            document.getElementById('confirm-name').textContent     = data.item_name;
            document.getElementById('confirm-code').textContent     = data.unique_code;
            document.getElementById('confirm-category').textContent = data.category;

            const img         = document.getElementById('confirm-img');
            const placeholder = document.getElementById('confirm-img-placeholder');

            if (data.image_url) {
                img.src = data.image_url;
                img.style.display = 'block';
                placeholder.style.display = 'none';
            } else {
                img.style.display = 'none';
                placeholder.style.display = 'block';
            }

            document.querySelectorAll('.scan-cond-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            document.getElementById('overlay-success').style.display = 'flex';
        }

        // ── CONDITION SELECTION ──────────────────────────────────────
        document.querySelectorAll('.scan-cond-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                if (!currentScanData) return;

                const conditionScore = parseInt(this.dataset.val);

                document.querySelectorAll('.scan-cond-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                await submitCondition(currentScanData.unique_code, conditionScore);
            });
        });

        async function submitCondition(uniqueCode, conditionScore) {
            try {
                await fetch(PROCESS_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        unique_code: uniqueCode,
                        condition_score: conditionScore,
                    }),
                });
            } catch(e) {
                // Non-critical
            }

            dismissConfirmation();
        }

        function dismissConfirmation() {
            document.getElementById('overlay-success').style.display = 'none';

            scannedCount++;
            updateProgress(currentScanData);
            currentScanData = null;
            isProcessing = false;

            resumeScanner();
        }

        // ── UPDATE PROGRESS ──────────────────────────────────────────
        function updateProgress(data) {
            document.getElementById('scanned-num').textContent = scannedCount;

            const pct = TOTAL_REQUIRED > 0 ? (scannedCount / TOTAL_REQUIRED) * 100 : 0;
            document.getElementById('progress-bar').style.width = pct + '%';

            if (data.next_suggestion) {
                document.getElementById('suggestion-name').textContent =
                    data.next_suggestion.item_name;
                document.getElementById('suggestion-remaining').textContent =
                    data.next_suggestion.remaining + ' remaining';
            } else {
                document.getElementById('scan-suggestion').style.display = 'none';
            }

            if (data.all_complete || scannedCount >= TOTAL_REQUIRED) {
                setTimeout(() => {
                    vibrate([200, 100, 200, 100, 200]); // Celebration pattern
                    document.getElementById('overlay-complete').style.display = 'flex';
                }, 400);
            }
        }

        // ── ERROR & WARNING DISPLAY ──────────────────────────────────
        function showError(title, message) {
            document.getElementById('error-icon').textContent  = '✕';
            document.getElementById('error-title').textContent = title;
            document.getElementById('error-msg').textContent   = message;

            const overlay = document.getElementById('overlay-error');
            overlay.style.display = 'flex';
            overlay.classList.remove('scan-overlay-warning');

            setTimeout(() => dismissError(), 3000);
        }

        function showWarning(title, message) {
            document.getElementById('error-icon').textContent  = '⚠';
            document.getElementById('error-title').textContent = title;
            document.getElementById('error-msg').textContent   = message;

            const overlay = document.getElementById('overlay-error');
            overlay.classList.add('scan-overlay-warning');
            overlay.style.display = 'flex';

            setTimeout(() => dismissError(), 2500);
        }

        function dismissError() {
            document.getElementById('overlay-error').style.display = 'none';
            resumeScanner();
        }

        function getErrorTitle(code) {
            const titles = {
                'PIECE_NOT_FOUND'      : 'Item Not Found',
                'NOT_ON_LIST'          : 'Not on Packing List',
                'QUANTITY_MET'         : 'Quantity Complete',
                'NOT_AVAILABLE'        : 'Item Unavailable',
                'ALREADY_SCANNED'      : 'Already Scanned',
                'ALREADY_DISPATCHED'   : 'Already Dispatched',
                'ASSIGNED_ELSEWHERE'   : 'Assigned to Another Event',
            };
            return titles[code] || 'Scan Error';
        }

        // ── MANUAL ENTRY ─────────────────────────────────────────────
        function openManual() {
            scanner.pause(true);
            document.getElementById('manual-code-input').value = '';
            document.getElementById('manual-error').style.display = 'none';
            document.getElementById('overlay-manual').style.display = 'flex';
            setTimeout(() => document.getElementById('manual-code-input').focus(), 300);
        }

        function closeManual() {
            document.getElementById('overlay-manual').style.display = 'none';
            isProcessing = false;
            resumeScanner();
        }

        async function submitManual() {
            const input = document.getElementById('manual-code-input');
            const code  = input.value.trim().toUpperCase();

            if (!code) {
                document.getElementById('manual-error').textContent = 'Please enter a code.';
                document.getElementById('manual-error').style.display = 'block';
                return;
            }

            document.getElementById('overlay-manual').style.display = 'none';
            isProcessing = true;
            await processScan(code);
        }

        document.getElementById('manual-code-input').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') submitManual();
        });

        // ── SUBMIT COMPLETE DISPATCH ─────────────────────────────────
        async function submitDispatch() {
            document.getElementById('submit-btn').style.display    = 'none';
            document.getElementById('submit-loading').style.display = 'block';

            try {
                const response = await fetch(SUBMIT_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({}),
                });

                // Check if response is not OK (401, 403, 500, etc)
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Submit failed:', response.status, errorText);

                    if (response.status === 401 || response.status === 419) {
                        alert('Session expired. Please refresh the page and try again.');
                    } else if (response.status === 403) {
                        alert('Permission denied. Please check your account permissions.');
                    } else if (response.status === 422) {
                        try {
                            const errorData = JSON.parse(errorText);
                            alert(errorData.message || 'Validation error occurred.');
                        } catch (e) {
                            alert('Validation error occurred. Please try again.');
                        }
                    } else {
                        alert(`Error ${response.status}: Could not complete dispatch. Please try again.`);
                    }

                    document.getElementById('submit-btn').style.display    = 'block';
                    document.getElementById('submit-loading').style.display = 'none';
                    return;
                }

                const data = await response.json();

                if (data.status === 'success') {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Could not complete dispatch. Please try again.');
                    document.getElementById('submit-btn').style.display    = 'block';
                    document.getElementById('submit-loading').style.display = 'none';
                }
            } catch(err) {
                console.error('Submit error:', err);
                alert('Connection error: ' + err.message + '. Please check your internet and try again.');
                document.getElementById('submit-btn').style.display    = 'block';
                document.getElementById('submit-loading').style.display = 'none';
            }
        }

        // ── CONFIRM MODAL FUNCTIONS ──────────────────────────────────
        window.confirmModalCallback = null;

        window.showConfirmModal = function(title, message, onConfirm) {
            document.getElementById('confirm-title').textContent = title;
            document.getElementById('confirm-message').textContent = message;
            window.confirmModalCallback = onConfirm;
            document.getElementById('confirm-modal').style.display = 'flex';
        };

        window.closeConfirmModal = function() {
            document.getElementById('confirm-modal').style.display = 'none';
            window.confirmModalCallback = null;
        };

        document.getElementById('confirm-ok-btn').addEventListener('click', function() {
            if (window.confirmModalCallback) {
                window.confirmModalCallback();
            }
            closeConfirmModal();
        });

        // ── SAVE AND EXIT ────────────────────────────────────────────
        async function saveAndExit() {
            showConfirmModal(
                'Save & Exit?',
                'Your progress will be saved.\nYou can resume by scanning the QR code again.',
                async function() {
                    try {
                        await fetch(SAVE_PROG_URL, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                        });
                    } catch(e) {}
                    window.location.href = '/';
                }
            );
        }

        // ── RESUME SCANNER ───────────────────────────────────────────
        function resumeScanner() {
            if (scanner) {
                try { scanner.resume(); } catch(e) {}
            }
        }

        // ── SESSION EXPIRED ──────────────────────────────────────────
        function showSessionExpired(message) {
            // Stop the camera
            if (scanner) {
                try { scanner.stop(); } catch(e) {}
            }

            // Replace the entire scan area with an expiry message
            document.getElementById('scan-main').innerHTML = `
                <div style="
                    display:flex;flex-direction:column;align-items:center;
                    justify-content:center;height:100%;padding:40px;text-align:center;
                    color:#ffffff;
                ">
                    <div style="font-size:56px;margin-bottom:20px">⏰</div>
                    <div style="font-size:20px;font-weight:700;margin-bottom:12px">
                        Session Expired
                    </div>
                    <div style="font-size:14px;opacity:0.85;margin-bottom:32px;line-height:1.6">
                        ${message}
                    </div>
                    <div style="font-size:13px;opacity:0.7;margin-bottom:8px">
                        Contact your coordinator to extend or restart the session.
                    </div>
                    <div style="
                        background:rgba(255,255,255,0.15);
                        border-radius:12px;padding:16px 24px;margin-top:16px;
                    ">
                        <div style="font-size:12px;opacity:0.8">Grey Apple Events</div>
                        <div style="font-size:16px;font-weight:600">+254 722 289 648</div>
                    </div>
                </div>
            `;

            // Update top bar to show expired state
            document.getElementById('scan-count').innerHTML =
                '<span style="color:#fca5a5;font-size:12px">Session Expired</span>';
        }

        // ── CAMERA PERMISSION ERROR ──────────────────────────────────
        function showCameraError(err) {
            document.getElementById('viewfinder-wrap').innerHTML = `
                <div style="padding:40px;text-align:center;color:#fff">
                    <div style="font-size:48px;margin-bottom:16px">📷</div>
                    <div style="font-size:16px;font-weight:600;margin-bottom:8px">Camera Access Required</div>
                    <div style="font-size:13px;opacity:0.8;margin-bottom:24px">
                        Allow camera access in your browser settings to scan items.
                    </div>
                    <button onclick="openManual()"
                            style="background:#CC0000;color:#fff;border:none;padding:12px 24px;border-radius:8px;font-size:14px;cursor:pointer">
                        Use Manual Entry Instead
                    </button>
                </div>
            `;
        }

        // ── INIT ─────────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            initScanner();

            @if(isset($firstSuggestion))
            document.getElementById('suggestion-name').textContent =
                "{{ $firstSuggestion['item_name'] }}";
            document.getElementById('suggestion-remaining').textContent =
                "{{ $firstSuggestion['remaining'] }} remaining";
            @endif
        });
    </script>
</body>
</html>
