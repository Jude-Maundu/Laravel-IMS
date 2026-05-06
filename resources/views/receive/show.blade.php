<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="theme-color" content="#CC0000">
    <title>Receive Session — {{ $session->event->name }}</title>
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

        .scan-brand-text {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .scan-event-name {
            font-size: 13px;
            opacity: 0.9;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .scan-topbar-progress {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .scan-progress-text {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            opacity: 0.9;
        }

        .scan-progress-num {
            font-size: 14px;
            font-weight: 700;
            font-family: 'Courier New', monospace;
        }

        .scan-progress-bar {
            height: 3px;
            background: rgba(255,255,255,0.3);
            border-radius: 2px;
            margin-top: 6px;
            overflow: hidden;
        }

        .scan-progress-fill {
            height: 100%;
            background: #ffffff;
            width: 0%;
            transition: width 0.4s ease;
        }

        /* ─── MAIN SCAN AREA ──────────────────────────────── */
        .scan-main {
            position: fixed;
            top: 80px;
            left: 0;
            right: 0;
            bottom: 0;
            background: #000;
        }

        .scan-viewfinder-wrap {
            width: 100%;
            height: 100%;
            position: relative;
        }

        #qr-reader {
            width: 100%;
            height: 100%;
        }

        #qr-reader video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .scan-target-frame {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 180px;
            height: 180px;
            transform: translate(-50%, -50%);
            border: 2px solid rgba(255,255,255,0.6);
            border-radius: 12px;
            pointer-events: none;
        }

        .scan-target-frame::before,
        .scan-target-frame::after {
            content: '';
            position: absolute;
            width: 40px;
            height: 40px;
            border: 3px solid #CC0000;
        }

        .scan-target-frame::before {
            top: -3px;
            left: -3px;
            border-right: none;
            border-bottom: none;
            border-top-left-radius: 12px;
        }

        .scan-target-frame::after {
            bottom: -3px;
            right: -3px;
            border-left: none;
            border-top: none;
            border-bottom-right-radius: 12px;
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
            backdrop-filter: blur(8px);
            z-index: 200;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: fadeIn 0.2s ease;
        }

        .scan-overlay-success {
            animation: slideUp 0.3s ease;
        }

        .scan-overlay-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 28px 24px;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .scan-overlay-check {
            width: 56px;
            height: 56px;
            background: #166534;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
            margin: 0 auto 20px;
        }

        .scan-overlay-img-wrap {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            overflow: hidden;
            background: #f3f4f6;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .scan-overlay-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .scan-overlay-img-placeholder {
            font-size: 40px;
        }

        .scan-overlay-item-name {
            font-size: 18px;
            font-weight: 700;
            color: #0f0f0f;
            margin-bottom: 4px;
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
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .scan-condition-btns {
            display: flex;
            gap: 8px;
            margin-bottom: 8px;
        }

        .scan-cond-btn {
            flex: 1;
            background: #f3f4f6;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px 6px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 700;
            color: #0f0f0f;
            transition: all 0.2s;
        }

        .scan-cond-btn span {
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            color: #9ca3af;
        }

        .scan-cond-btn.active {
            background: #166534;
            border-color: #166534;
            color: #ffffff;
        }

        .scan-cond-btn.active span {
            color: rgba(255,255,255,0.9);
        }

        /* ─── DESTINATION SELECTOR ─────────────────────────── */
        .scan-destination-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            font-weight: 600;
            margin-top: 20px;
            margin-bottom: 12px;
        }

        .scan-dest-btns {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 8px;
        }

        .scan-dest-btn {
            background: #f3f4f6;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #0f0f0f;
            transition: all 0.2s;
            text-align: left;
        }

        .scan-dest-btn .icon {
            font-size: 20px;
        }

        .scan-dest-btn.active {
            background: #CC0000;
            border-color: #CC0000;
            color: #ffffff;
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

        /* ─── COMPLETE OVERLAY ───────────────────────────── */
        .scan-overlay-complete {
            background: linear-gradient(135deg, #166534 0%, #14532d 100%);
        }

        .scan-complete-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .scan-complete-title {
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 12px;
        }

        .scan-complete-msg {
            font-size: 16px;
            color: rgba(255,255,255,0.9);
            margin-bottom: 32px;
        }

        .scan-complete-btn {
            background: #ffffff;
            color: #166534;
            border: none;
            border-radius: 12px;
            padding: 14px 32px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
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

    <!-- TOP BAR -->
    <div class="scan-topbar">
        <div class="scan-topbar-brand">
            <div class="scan-logo-img" style="background:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;">📥</div>
            <div>
                <div class="scan-brand-text">RECEIVE SESSION</div>
                <div class="scan-event-name">{{ $event->name }}</div>
            </div>
        </div>
        <div class="scan-topbar-progress">
            <div class="scan-progress-text">Items Received</div>
            <div class="scan-progress-num">
                <span id="scanned-num">{{ $alreadyScanned }}</span> / {{ $totalRequired }}
            </div>
        </div>
        <div class="scan-progress-bar">
            <div class="scan-progress-fill" id="progress-bar" style="width:{{ $totalRequired > 0 ? ($alreadyScanned / $totalRequired * 100) : 0 }}%"></div>
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
                @if($firstSuggestion)
                    {{ $firstSuggestion['item_name'] }}
                @else
                    Loading...
                @endif
            </div>
            <div class="scan-suggestion-remaining" id="suggestion-remaining">
                @if($firstSuggestion)
                    {{ $firstSuggestion['remaining'] }} remaining
                @endif
            </div>
        </div>
    </div>

    <!-- CONFIRMATION OVERLAY (Success + Condition + Destination) -->
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

            <!-- Condition Selection -->
            <div class="scan-condition-label">Select Condition on Return</div>
            <div class="scan-condition-btns" id="condition-btns">
                <button class="scan-cond-btn" data-val="1">1<span>Poor</span></button>
                <button class="scan-cond-btn" data-val="2">2<span>Fair</span></button>
                <button class="scan-cond-btn" data-val="3">3<span>Good</span></button>
                <button class="scan-cond-btn" data-val="4">4<span>V.Good</span></button>
                <button class="scan-cond-btn" data-val="5">5<span>Excel</span></button>
            </div>

            <!-- Destination Selection -->
            <div class="scan-destination-label">Assign Destination</div>
            <div class="scan-dest-btns" id="destination-btns">
                <button class="scan-dest-btn active" data-dest="warehouse">
                    <span class="icon">🏠</span>
                    <span>Warehouse (Ready)</span>
                </button>
                <button class="scan-dest-btn" data-dest="cleaning">
                    <span class="icon">🧼</span>
                    <span>Cleaning Bay</span>
                </button>
                <button class="scan-dest-btn" data-dest="repair">
                    <span class="icon">🔧</span>
                    <span>Repair Workshop</span>
                </button>
            </div>

            <div class="scan-overlay-hint">Select condition and destination to continue</div>
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

    <!-- COMPLETE OVERLAY -->
    <div class="scan-overlay scan-overlay-complete" id="overlay-complete" style="display:none">
        <div style="text-align:center">
            <div class="scan-complete-icon">🎉</div>
            <h2 class="scan-complete-title">All Items Received!</h2>
            <p class="scan-complete-msg">Ready to complete receive session</p>
            <button class="scan-complete-btn" onclick="completeReceive()">Complete Receive</button>
        </div>
    </div>

    <!-- Audio for error sound -->
    <audio id="error-sound" preload="auto">
        <source src="{{ asset('sounds/error.mp3') }}" type="audio/mpeg">
    </audio>

    <script>
    // ═══════════════════════════════════════════════════════════════
    // RECEIVE SESSION SCAN JAVASCRIPT
    // ═══════════════════════════════════════════════════════════════

    const CSRF = '{{ $csrfToken }}';
    const PROCESS_URL = '{{ $processUrl }}';
    const SUBMIT_URL = '{{ $submitUrl }}';
    const SAVE_PROGRESS_URL = '{{ $saveProgressUrl }}';

    let TOTAL_REQUIRED = {{ $totalRequired }};
    let scannedCount = {{ $alreadyScanned }};
    let scanner = null;
    let isProcessing = false;
    let currentScanData = null;
    let selectedCondition = null;
    let selectedDestination = 'warehouse';

    // ─── VIBRATION ───────────────────────────────────────────────
    function vibrate(pattern) {
        if ('vibrate' in navigator) {
            navigator.vibrate(pattern);
        }
    }

    // ─── PLAY ERROR SOUND ────────────────────────────────────────
    function playErrorSound() {
        const audio = document.getElementById('error-sound');
        if (audio) {
            audio.currentTime = 0;
            audio.play().catch(e => console.log('Audio play failed:', e));
        }
    }

    // ─── INITIALIZE SCANNER ──────────────────────────────────────
    function startScanner() {
        scanner = new Html5Qrcode("qr-reader");

        scanner.start(
            { facingMode: "environment" },
            {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0,
            },
            onScanSuccess,
            onScanFailure
        ).catch(err => {
            console.error("Camera start failed:", err);
            showError('Camera Error', 'Failed to start camera. Please allow camera access.');
        });
    }

    // ─── SCAN SUCCESS ────────────────────────────────────────────
    async function onScanSuccess(decodedText) {
        if (isProcessing) return;

        isProcessing = true;
        vibrate(50);
        pauseScanner();

        // Extract piece code from scanned data
        // QR codes can contain:
        // - Full URL: http://127.0.0.1:8000/item/GA-BIG-001
        // - Just the code: GA-BIG-001
        let uniqueCode = decodedText.trim();

        // Check if it's a URL
        try {
            const url = new URL(uniqueCode);
            // Extract the last segment from the path
            const pathSegments = url.pathname.split('/').filter(s => s.length > 0);
            uniqueCode = pathSegments[pathSegments.length - 1];
        } catch (e) {
            // Not a URL, treat as plain code
            uniqueCode = uniqueCode;
        }

        // Convert to uppercase for consistency
        uniqueCode = uniqueCode.toUpperCase();

        const requestData = {
            unique_code: uniqueCode,
            condition_score: null,
            destination: null,
        };

        try {
            const response = await fetch(PROCESS_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(requestData),
            });

            const data = await response.json();

            if (data.status === 'success' && data.code === 'RECEIVED') {
                vibrate(100);
                currentScanData = data;
                showConfirmation(data);
            } else if (data.code === 'ALREADY_RECEIVED') {
                vibrate([100, 50, 100]);
                playErrorSound();
                showWarning('Already Received', data.message);
            } else if (data.code === 'NOT_DISPATCHED') {
                vibrate([100, 50, 100]);
                playErrorSound();
                showError('Not Dispatched', data.message || 'This specific piece was not dispatched for this event.');
            } else if (data.code === 'INVALID_STATUS') {
                vibrate([100, 50, 100]);
                playErrorSound();
                showError('Invalid Status', data.message);
            } else if (data.code === 'ALREADY_RETURNED') {
                vibrate([100, 50, 100]);
                playErrorSound();
                showWarning('Already Returned', data.message);
            } else {
                vibrate([100, 50, 100]);
                playErrorSound();
                showError(getErrorTitle(data.code), data.message);
            }
        } catch (err) {
            console.error('Process error:', err);
            playErrorSound();
            showError('Network Error', 'Failed to process scan. Please try again.');
        }
    }

    function onScanFailure(error) {
        // Silent - happens frequently during normal scanning
    }

    // ─── SHOW CONFIRMATION OVERLAY ───────────────────────────────
    function showConfirmation(data) {
        document.getElementById('confirm-name').textContent = data.item_name;
        document.getElementById('confirm-code').textContent = data.unique_code;
        document.getElementById('confirm-category').textContent = data.category;

        const img = document.getElementById('confirm-img');
        const placeholder = document.getElementById('confirm-img-placeholder');

        if (data.image_url) {
            img.src = data.image_url;
            img.style.display = 'block';
            placeholder.style.display = 'none';
        } else {
            img.style.display = 'none';
            placeholder.style.display = 'block';
        }

        // Reset selections
        selectedCondition = null;
        selectedDestination = 'warehouse';
        document.querySelectorAll('.scan-cond-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.scan-dest-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelector('.scan-dest-btn[data-dest="warehouse"]').classList.add('active');

        document.getElementById('overlay-success').style.display = 'flex';
    }

    // ─── CONDITION SELECTION ─────────────────────────────────────
    document.querySelectorAll('.scan-cond-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            selectedCondition = parseInt(this.dataset.val);

            document.querySelectorAll('.scan-cond-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Auto-submit if both condition and destination selected
            if (selectedCondition && selectedDestination) {
                submitReceive();
            }
        });
    });

    // ─── DESTINATION SELECTION ───────────────────────────────────
    document.querySelectorAll('.scan-dest-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            selectedDestination = this.dataset.dest;

            document.querySelectorAll('.scan-dest-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Auto-submit if both condition and destination selected
            if (selectedCondition && selectedDestination) {
                submitReceive();
            }
        });
    });

    // ─── SUBMIT RECEIVE ──────────────────────────────────────────
    async function submitReceive() {
        if (!currentScanData || !selectedCondition || !selectedDestination) return;

        try {
            await fetch(PROCESS_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    unique_code: currentScanData.unique_code,
                    condition_score: selectedCondition,
                    destination: selectedDestination,
                    update_details_only: true,
                }),
            });

            dismissConfirmation();
        } catch (e) {
            console.error('Submit error:', e);
        }
    }

    function dismissConfirmation() {
        document.getElementById('overlay-success').style.display = 'none';

        scannedCount++;
        updateProgress(currentScanData);
        currentScanData = null;
        selectedCondition = null;
        selectedDestination = 'warehouse';
        isProcessing = false;

        resumeScanner();
    }

    // ─── UPDATE PROGRESS ─────────────────────────────────────────
    function updateProgress(data) {
        document.getElementById('scanned-num').textContent = scannedCount;

        const pct = TOTAL_REQUIRED > 0 ? (scannedCount / TOTAL_REQUIRED) * 100 : 0;
        document.getElementById('progress-bar').style.width = pct + '%';

        if (data.next_suggestion) {
            document.getElementById('suggestion-name').textContent = data.next_suggestion.item_name;
            document.getElementById('suggestion-remaining').textContent = data.next_suggestion.remaining + ' remaining';
        } else {
            document.getElementById('scan-suggestion').style.display = 'none';
        }

        if (data.all_complete || scannedCount >= TOTAL_REQUIRED) {
            setTimeout(() => {
                vibrate([200, 100, 200, 100, 200]);
                document.getElementById('overlay-complete').style.display = 'flex';
            }, 400);
        }
    }

    // ─── ERROR & WARNING DISPLAY ─────────────────────────────────
    function showError(title, message) {
        document.getElementById('error-icon').textContent = '✕';
        document.getElementById('error-title').textContent = title;
        document.getElementById('error-msg').textContent = message;

        const overlay = document.getElementById('overlay-error');
        overlay.style.display = 'flex';
        overlay.classList.remove('scan-overlay-warning');

        setTimeout(() => dismissError(), 3000);
    }

    function showWarning(title, message) {
        document.getElementById('error-icon').textContent = '⚠';
        document.getElementById('error-title').textContent = title;
        document.getElementById('error-msg').textContent = message;

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
            'PIECE_NOT_FOUND'     : 'Item Not Found',
            'NOT_DISPATCHED'      : 'Not Dispatched',
            'ALREADY_RECEIVED'    : 'Already Received',
            'ALREADY_RETURNED'    : 'Already Returned',
            'INVALID_STATUS'      : 'Invalid Status',
        };
        return titles[code] || 'Scan Error';
    }

    // ─── COMPLETE RECEIVE ────────────────────────────────────────
    async function completeReceive() {
        const btn = event.target;
        btn.disabled = true;
        btn.textContent = 'Completing...';

        try {
            const response = await fetch(SUBMIT_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (data.status === 'success' && data.redirect) {
                window.location.href = data.redirect;
            } else {
                alert(data.message || 'Failed to complete receive.');
                btn.disabled = false;
                btn.textContent = 'Complete Receive';
            }
        } catch (err) {
            alert('Network error. Please try again.');
            btn.disabled = false;
            btn.textContent = 'Complete Receive';
        }
    }

    // ─── SCANNER CONTROL ─────────────────────────────────────────
    function pauseScanner() {
        if (scanner) {
            scanner.pause(true);
        }
    }

    function resumeScanner() {
        if (scanner) {
            scanner.resume();
            isProcessing = false;
        }
    }

    // ─── START ON LOAD ───────────────────────────────────────────
    window.addEventListener('load', startScanner);
    </script>

</body>
</html>
