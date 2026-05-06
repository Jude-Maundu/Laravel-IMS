<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#166534">
    <title>Dispatch Complete — Grey Apple IMS</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: #f0fdf4;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .complete-card {
            background: #fff;
            border-radius: 16px;
            padding: 40px 32px;
            text-align: center;
            max-width: 380px;
            width: 100%;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .complete-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        .complete-title {
            font-size: 22px;
            font-weight: 800;
            color: #166534;
            margin-bottom: 8px;
        }
        .complete-event {
            font-size: 15px;
            color: #374151;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .complete-venue {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 24px;
        }
        .complete-stats {
            background: #f0fdf4;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 24px;
        }
        .complete-stat-num {
            font-size: 32px;
            font-weight: 800;
            color: #166534;
        }
        .complete-stat-label {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
        }
        .complete-brand {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 11px;
            color: #9ca3af;
        }
        .complete-brand strong {
            color: #CC0000;
            display: block;
            font-size: 13px;
            margin-bottom: 2px;
        }
    </style>
</head>
<body>
    <div class="complete-card">
        <div class="complete-icon">✅</div>
        <div class="complete-title">Dispatch Complete</div>
        <div class="complete-event">{{ $event->name }}</div>
        <div class="complete-venue">{{ $event->venue }}</div>
        <div class="complete-stats">
            <div class="complete-stat-num">{{ $session->scanned_count }}</div>
            <div class="complete-stat-label">items dispatched</div>
        </div>
        <div class="complete-brand">
            <strong>GREY APPLE EVENTS</strong>
            Powered by GAIMS · joseasoftwares.co.ke
        </div>
    </div>
</body>
</html>
