<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — Grey Apple IMS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Oswald:wght@300;400;600&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script>
    (function(){
      var collapsed = localStorage.getItem('ga_sb_collapsed') === '1';
      if(collapsed){
        document.documentElement.style.setProperty('--sb-width', '64px');
      } else {
        document.documentElement.style.setProperty('--sb-width', '260px');
      }
    })();
  </script>
</head>
<body>
  <x-sidebar />
  <x-topbar />
  <x-toast />
  <main id="ga-main-content">
    <div style="padding:24px;">
      @yield('content')
    </div>
  </main>
  <script>
    (function(){
      var collapsed = localStorage.getItem('ga_sb_collapsed') === '1';
      if(collapsed){
        var sb = document.getElementById('ga-sidebar');
        var tb = document.getElementById('ga-topbar');
        var main = document.getElementById('ga-main-content');
        if(sb) sb.classList.add('ga-collapsed');
        if(tb) tb.style.left = '64px';
        if(main) main.style.marginLeft = '64px';
      }
      if(localStorage.getItem('ga_sb_theme') === 'dark'){
        var sb = document.getElementById('ga-sidebar');
        if(sb) sb.classList.add('ga-dark');
      }
    })();
  </script>
</body>
</html>
