{{-- Toast Component --}}
<div id="ga-toast-container" aria-live="polite" aria-atomic="true"></div>

{{-- Login Success Toast (Special Case) --}}
@if(session('login_success'))
@php
  $userName = session('login_user', 'User');
  $nameParts = explode(' ', trim($userName));
  $firstName = $nameParts[0];
@endphp

<script>
(function() {
  document.addEventListener('DOMContentLoaded', function() {
    window.gaShowToast({
      type: 'login',
      title: 'Authentication Successful',
      message: 'Welcome back, {{ $firstName }}. You are now signed in.',
      duration: 5500,
      sound: true
    });
  });
})();
</script>
@endif

{{-- Standard Success Toast --}}
@if(session('success'))
<script>
(function() {
  document.addEventListener('DOMContentLoaded', function() {
    window.gaShowToast({
      type: 'success',
      title: 'Success',
      message: @json(session('success')),
      duration: 5000,
      sound: {{ session('toast_sound', 'false') }}
    });
  });
})();
</script>
@endif

{{-- Error Toast --}}
@if(session('error'))
<script>
(function() {
  document.addEventListener('DOMContentLoaded', function() {
    window.gaShowToast({
      type: 'error',
      title: 'Error',
      message: @json(session('error')),
      duration: 6000,
      sound: false
    });
  });
})();
</script>
@endif

{{-- Warning Toast --}}
@if(session('warning'))
<script>
(function() {
  document.addEventListener('DOMContentLoaded', function() {
    window.gaShowToast({
      type: 'warning',
      title: 'Warning',
      message: @json(session('warning')),
      duration: 5500,
      sound: false
    });
  });
})();
</script>
@endif

{{-- Info Toast --}}
@if(session('info'))
<script>
(function() {
  document.addEventListener('DOMContentLoaded', function() {
    window.gaShowToast({
      type: 'info',
      title: 'Information',
      message: @json(session('info')),
      duration: 4500,
      sound: false
    });
  });
})();
</script>
@endif

{{-- Validation Errors Toast --}}
@if($errors->any())
<script>
(function() {
  document.addEventListener('DOMContentLoaded', function() {
    var errorMessages = @json($errors->all());
    var message = errorMessages.length === 1
      ? errorMessages[0]
      : errorMessages.length + ' validation errors occurred';

    window.gaShowToast({
      type: 'error',
      title: 'Validation Error',
      message: message,
      duration: 6000,
      sound: false
    });
  });
})();
</script>
@endif

{{-- Toast JavaScript Core --}}
<script>
(function() {
  var toastQueue = [];
  var activeToast = null;
  var toastCount = 0;

  // Sound player
  function gaPlaySound() {
    try {
      var audio = new Audio('{{ asset("sounds/success.mp3") }}');
      audio.volume = 0.5;
      audio.play().catch(function(){});
    } catch(e) {}
  }

  // Get icon based on type
  function getToastIcon(type) {
    switch(type) {
      case 'success':
        return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
      case 'error':
        return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
      case 'warning':
        return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
      case 'info':
        return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
      case 'login':
        return '<img src="{{ asset('images/grey-apple-events-logo.png') }}" alt="Grey Apple" class="ga-toast-logo">';
      default:
        return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"></circle></svg>';
    }
  }

  // Create toast element
  function createToastElement(config) {
    toastCount++;
    var id = 'ga-toast-' + toastCount;
    var type = config.type || 'info';
    var title = config.title || 'Notification';
    var message = config.message || '';
    var duration = config.duration || 5000;

    var toast = document.createElement('div');
    toast.id = id;
    toast.className = 'ga-toast ga-toast-' + type;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');

    var iconHtml = getToastIcon(type);
    var progressBar = duration > 0 ? '<div class="ga-toast-bar"><div class="ga-toast-progress"></div></div>' : '';

    toast.innerHTML =
      '<div class="ga-toast-inner">' +
        '<div class="ga-toast-top">' +
          '<div class="ga-toast-icon-wrap">' + iconHtml + '</div>' +
          '<div class="ga-toast-text">' +
            '<div class="ga-toast-title">' + escapeHtml(title) + '</div>' +
            '<div class="ga-toast-msg">' + escapeHtml(message) + '</div>' +
          '</div>' +
          '<button class="ga-toast-close" onclick="gaCloseToast(\'' + id + '\')" type="button" aria-label="Close">' +
            '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>' +
          '</button>' +
        '</div>' +
        (type === 'login' ? '<div class="ga-toast-sub"><svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M8 1a4.5 4.5 0 0 1 4.5 4.5V9l1.5 2.5H2L3.5 9V5.5A4.5 4.5 0 0 1 8 1z"/><path d="M6.5 13.5a1.5 1.5 0 0 0 3 0"/></svg>Grey Apple Events Limited &middot; Inventory Management System</div>' : '') +
        progressBar +
      '</div>';

    return { element: toast, id: id, duration: duration };
  }

  // Escape HTML
  function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  // Close toast
  window.gaCloseToast = function(id) {
    var toast = document.getElementById(id);
    if (!toast || toast.classList.contains('ga-toast-closing')) return;

    toast.classList.add('ga-toast-closing');
    setTimeout(function() {
      if (toast && toast.parentNode) {
        toast.parentNode.removeChild(toast);
        if (activeToast && activeToast.id === id) {
          activeToast = null;
          processQueue();
        }
      }
    }, 300);
  };

  // Show toast
  window.gaShowToast = function(config) {
    if (!config || !config.message) return;

    // Play sound if requested
    if (config.sound === true || config.sound === 'true') {
      gaPlaySound();
    }

    // Add to queue
    toastQueue.push(config);
    processQueue();
  };

  // Process queue
  function processQueue() {
    if (activeToast !== null || toastQueue.length === 0) return;

    var config = toastQueue.shift();
    var container = document.getElementById('ga-toast-container');
    if (!container) return;

    var toastData = createToastElement(config);
    activeToast = toastData;

    container.appendChild(toastData.element);

    // Auto-close after duration
    if (toastData.duration > 0) {
      setTimeout(function() {
        gaCloseToast(toastData.id);
      }, toastData.duration);
    }
  }

  // Initialize
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', processQueue);
  } else {
    processQueue();
  }
})();
</script>
