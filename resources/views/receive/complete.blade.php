<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#3B6D11">
    <title>Receive Complete — {{ $event->name }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #3B6D11 0%, #2a4f0c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #ffffff;
        }

        .complete-container {
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .complete-icon {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 40px;
        }

        .complete-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }

        .complete-subtitle {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 32px;
        }

        .complete-stats {
            background: rgba(255,255,255,0.15);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
            backdrop-filter: blur(10px);
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .stat-row:last-child {
            border-bottom: none;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.8;
        }

        .stat-value {
            font-size: 18px;
            font-weight: 700;
        }

        .complete-btn {
            display: block;
            width: 100%;
            background: #ffffff;
            color: #3B6D11;
            border: none;
            border-radius: 12px;
            padding: 16px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            margin-bottom: 12px;
        }

        .complete-btn:hover {
            background: #f5f5f5;
        }

        .complete-btn-secondary {
            background: rgba(255,255,255,0.2);
            color: #ffffff;
        }

        .complete-btn-secondary:hover {
            background: rgba(255,255,255,0.3);
        }

        .complete-note {
            font-size: 12px;
            opacity: 0.7;
            margin-top: 24px;
        }
    </style>
</head>
<body>

    <div class="complete-container">
        <div class="complete-icon">✅</div>
        <h1 class="complete-title">Receive Complete!</h1>
        <p class="complete-subtitle">{{ $event->name }}</p>

        <div class="complete-stats">
            <div class="stat-row">
                <div class="stat-label">Total Pieces Received</div>
                <div class="stat-value">{{ $session->received_count }}</div>
            </div>
            <div class="stat-row">
                <div class="stat-label">Session Duration</div>
                <div class="stat-value">{{ $session->created_at->diffForHumans($session->completed_at, true) }}</div>
            </div>
            <div class="stat-row">
                <div class="stat-label">Completed By</div>
                <div class="stat-value">{{ auth()->user()->name ?? 'System' }}</div>
            </div>
        </div>

        <a href="{{ route('events.show', $event) }}" class="complete-btn">
            📋 View Event Details
        </a>

        <a href="{{ route('dashboard.index') }}" class="complete-btn complete-btn-secondary">
            🏠 Back to Dashboard
        </a>

        <p class="complete-note">
            All items have been processed and inventory has been updated.
        </p>
    </div>

</body>
</html>
