<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receive Session — Grey Apple IMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f7f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .receive-invalid-card {
            background: #ffffff;
            border: 1px solid #ece8e3;
            border-radius: 12px;
            max-width: 420px;
            width: 100%;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .receive-invalid-topbar {
            background: #185FA5;
            color: #ffffff;
            padding: 16px 20px;
            font-size: 11px;
            font-weight: 700;
            text-align: center;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .receive-invalid-body {
            padding: 32px 24px;
            text-align: center;
        }

        .receive-invalid-icon {
            font-size: 56px;
            margin-bottom: 20px;
            line-height: 1;
        }

        .receive-invalid-message {
            font-size: 15px;
            font-weight: 600;
            color: #0f0f0f;
            line-height: 1.5;
            margin-bottom: 12px;
        }

        .receive-invalid-hint {
            font-size: 13px;
            color: #5c5550;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .receive-invalid-contact {
            padding-top: 24px;
            border-top: 1px solid #f5f1ed;
            font-size: 12px;
            color: #a09890;
            line-height: 1.7;
        }

        .receive-invalid-contact strong {
            color: #0f0f0f;
            font-weight: 600;
        }

        @media (max-width: 480px) {
            body {
                padding: 12px;
            }

            .receive-invalid-body {
                padding: 24px 20px;
            }

            .receive-invalid-icon {
                font-size: 48px;
            }

            .receive-invalid-message {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="receive-invalid-card">
        <div class="receive-invalid-topbar">
            GREY APPLE EVENTS LIMITED
        </div>
        <div class="receive-invalid-body">
            <div class="receive-invalid-icon">
                @if($code === 'EXPIRED')
                    ⏰
                @elseif($code === 'COMPLETED')
                    ✅
                @elseif($code === 'CANCELLED')
                    ❌
                @elseif($code === 'UNAUTHORIZED')
                    🔒
                @else
                    ⚠️
                @endif
            </div>
            <div class="receive-invalid-message">{{ $message }}</div>
            @if($code === 'EXPIRED')
                <div class="receive-invalid-hint">
                    Contact your coordinator to extend the session.
                </div>
            @endif
            @if($code === 'COMPLETED' && isset($event))
                <div class="receive-invalid-hint">
                    Event: <strong>{{ $event->name }}</strong>
                </div>
            @endif
            <div class="receive-invalid-contact">
                <strong>Grey Apple Events Limited</strong><br>
                Ruaraka, Nairobi, Kenya<br>
                +254 722 289 648
            </div>
        </div>
    </div>
</body>
</html>
