{{-- Custom Confirmation Modal Component --}}
<div id="confirm-modal" class="custom-confirm-modal" style="display:none">
  <div class="custom-confirm-overlay" onclick="closeConfirmModal()"></div>
  <div class="custom-confirm-box">
    <div class="custom-confirm-icon" id="confirm-icon">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="8" x2="12" y2="12"></line>
        <line x1="12" y1="16" x2="12.01" y2="16"></line>
      </svg>
    </div>
    <h3 class="custom-confirm-title" id="confirm-title">Confirm Action</h3>
    <p class="custom-confirm-message" id="confirm-message">Are you sure you want to proceed?</p>
    <div class="custom-confirm-actions">
      <button type="button" class="custom-confirm-cancel" onclick="closeConfirmModal()">Cancel</button>
      <button type="button" class="custom-confirm-ok" id="confirm-ok-btn">Confirm</button>
    </div>
  </div>
</div>

<style>
.custom-confirm-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 99999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  animation: fadeIn 0.2s ease;
}

.custom-confirm-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(4px);
}

.custom-confirm-box {
  position: relative;
  background: #ffffff;
  border-radius: 16px;
  padding: 32px 28px;
  max-width: 440px;
  width: 100%;
  text-align: center;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: slideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.custom-confirm-icon {
  width: 72px;
  height: 72px;
  margin: 0 auto 20px;
  background: linear-gradient(135deg, #fff0f0 0%, #fff8f8 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 3px solid #f5c0c0;
}

.custom-confirm-icon svg {
  color: #CC0000;
}

.custom-confirm-icon.success {
  background: linear-gradient(135deg, #eaf3de 0%, #f0f7e8 100%);
  border-color: #d1e7b8;
}

.custom-confirm-icon.success svg {
  color: #3B6D11;
}

.custom-confirm-icon.warning {
  background: linear-gradient(135deg, #fef3c7 0%, #fefce8 100%);
  border-color: #fde68a;
}

.custom-confirm-icon.warning svg {
  color: #854F0B;
}

.custom-confirm-title {
  font-size: 20px;
  font-weight: 700;
  color: #0f0f0f;
  margin: 0 0 12px 0;
  line-height: 1.3;
}

.custom-confirm-message {
  font-size: 14px;
  color: #5c5550;
  line-height: 1.6;
  margin: 0 0 28px 0;
  white-space: pre-line;
}

.custom-confirm-actions {
  display: flex;
  gap: 12px;
  justify-content: center;
}

.custom-confirm-cancel,
.custom-confirm-ok {
  flex: 1;
  padding: 12px 24px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  font-family: inherit;
}

.custom-confirm-cancel {
  background: #f8f7f5;
  color: #5c5550;
  border: 1px solid #ece8e3;
}

.custom-confirm-cancel:hover {
  background: #f0ece8;
  border-color: #d0c8c0;
}

.custom-confirm-ok {
  background: #CC0000;
  color: #ffffff;
}

.custom-confirm-ok:hover {
  background: #aa0000;
  box-shadow: 0 4px 12px rgba(204, 0, 0, 0.3);
}

.custom-confirm-ok.success {
  background: #3B6D11;
}

.custom-confirm-ok.success:hover {
  background: #2d5409;
  box-shadow: 0 4px 12px rgba(59, 109, 17, 0.3);
}

.custom-confirm-ok.warning {
  background: #854F0B;
}

.custom-confirm-ok.warning:hover {
  background: #6d4009;
  box-shadow: 0 4px 12px rgba(133, 79, 11, 0.3);
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}
</style>

<script>
// Global confirmation modal system
window.confirmModalCallback = null;

window.showConfirmModal = function(options) {
  const modal = document.getElementById('confirm-modal');
  const icon = document.getElementById('confirm-icon');
  const title = document.getElementById('confirm-title');
  const message = document.getElementById('confirm-message');
  const okBtn = document.getElementById('confirm-ok-btn');

  // Set content
  title.textContent = options.title || 'Confirm Action';
  message.textContent = options.message || 'Are you sure you want to proceed?';
  okBtn.textContent = options.confirmText || 'Confirm';

  // Set type (default, success, warning)
  icon.className = 'custom-confirm-icon';
  okBtn.className = 'custom-confirm-ok';

  if (options.type === 'success') {
    icon.classList.add('success');
    okBtn.classList.add('success');
    icon.innerHTML = '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';
  } else if (options.type === 'warning') {
    icon.classList.add('warning');
    okBtn.classList.add('warning');
    icon.innerHTML = '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
  } else {
    icon.innerHTML = '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>';
  }

  // Store callback
  window.confirmModalCallback = options.onConfirm;

  // Show modal
  modal.style.display = 'flex';

  // Focus confirm button
  setTimeout(() => okBtn.focus(), 100);
};

window.closeConfirmModal = function() {
  const modal = document.getElementById('confirm-modal');
  modal.style.display = 'none';
  window.confirmModalCallback = null;
};

// Confirm button click
document.addEventListener('DOMContentLoaded', function() {
  const okBtn = document.getElementById('confirm-ok-btn');
  if (okBtn) {
    okBtn.addEventListener('click', function() {
      if (window.confirmModalCallback) {
        window.confirmModalCallback();
      }
      closeConfirmModal();
    });
  }

  // ESC key to close
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      const modal = document.getElementById('confirm-modal');
      if (modal && modal.style.display === 'flex') {
        closeConfirmModal();
      }
    }
  });

  // Enter key to confirm
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      const modal = document.getElementById('confirm-modal');
      if (modal && modal.style.display === 'flex') {
        const okBtn = document.getElementById('confirm-ok-btn');
        if (okBtn && document.activeElement !== document.querySelector('.custom-confirm-cancel')) {
          okBtn.click();
        }
      }
    }
  });
});
</script>
