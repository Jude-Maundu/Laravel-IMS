<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Customer Portal') — Grey Apple Events</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Oswald:wght@300;400;600&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="ptl-body">
  <div class="ptl-container">
    <div class="ptl-header">
      <img src="{{ asset('images/grey-apple-events-logo.png') }}" alt="Grey Apple Events" class="ptl-logo">
      <h1 class="ptl-title">Customer Portal</h1>
    </div>

    @if(session('success'))
    <div class="ev-flash ev-flash-success" style="margin-bottom: 20px;">
      <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
      {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="ev-flash ev-flash-error" style="margin-bottom: 20px;">
      <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      {{ session('error') }}
    </div>
    @endif

    @if(session('info'))
    <div class="ev-flash" style="background: #e6f1fb; color: #185FA5; border-color: #c9e2f8; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 8px; font-size: 13px;">
      <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      {{ session('info') }}
    </div>
    @endif

    @yield('content')
  </div>
</body>
</html>
