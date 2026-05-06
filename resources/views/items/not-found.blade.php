<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Item Not Found - Grey Apple Events</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css'])
</head>
<body class="lkp-body">

<div class="lkp-topbar">
  <div class="lkp-topbar-inner">
    @if(file_exists(public_path('images/grey-apple-events-logo.png')))
      <img src="{{ asset('images/grey-apple-events-logo.png') }}" alt="Grey Apple Events" class="lkp-logo-img">
    @endif
    <span class="lkp-company-name">GREY APPLE EVENTS</span>
  </div>
</div>

<div class="lkp-container">
  <div class="lkp-card lkp-not-found">
    <svg width="64" height="64" viewBox="0 0 16 16" fill="none" stroke="#CC0000" stroke-width="1" class="lkp-error-icon">
      <circle cx="8" cy="8" r="6.5"/>
      <path d="M6 6l4 4M10 6l-4 4"/>
    </svg>

    <h1 class="lkp-error-title">Item Not Found</h1>
    <p class="lkp-error-message">
      The item code <strong>{{ $code }}</strong> was not found in our system.
    </p>

    <div class="lkp-divider" style="margin: 24px 0"></div>

    <div class="lkp-section">
      <div class="lkp-label">Need Help?</div>
      <div class="lkp-contact">
        <div class="lkp-contact-item">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
            <path d="M2 3h12v10H2z"/><path d="M2 3l6 5 6-5"/>
          </svg>
          <a href="mailto:info@greyapple.co.ke">info@greyapple.co.ke</a>
        </div>
        <div class="lkp-contact-item">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
            <path d="M3 2h3l1 3-2 2c1 2 2 3 4 4l2-2 3 1v3c0 1-4 2-7-1S1 7 2 4c1-1 1-2 1-2z"/>
          </svg>
          <a href="tel:+254722289648">+254 722 289 648</a>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
