/**
 * Grey Apple IMS Toast Notification System
 *
 * Usage:
 * toast.success('Item created successfully!');
 * toast.error('Failed to delete item');
 * toast.warning('This action cannot be undone');
 * toast.info('Event status updated');
 * toast.custom({ type: 'success', title: 'Custom Title', message: 'Custom message', sound: true });
 */

const toast = {
  /**
   * Show a success toast notification
   * @param {string} message - The message to display
   * @param {boolean} sound - Whether to play success sound (default: false)
   */
  success(message, sound = false) {
    this.show({
      type: 'success',
      title: 'Success',
      message: message,
      duration: 5000,
      sound: sound
    });
  },

  /**
   * Show an error toast notification
   * @param {string} message - The message to display
   */
  error(message) {
    this.show({
      type: 'error',
      title: 'Error',
      message: message,
      duration: 6000,
      sound: false
    });
  },

  /**
   * Show a warning toast notification
   * @param {string} message - The message to display
   */
  warning(message) {
    this.show({
      type: 'warning',
      title: 'Warning',
      message: message,
      duration: 5500,
      sound: false
    });
  },

  /**
   * Show an info toast notification
   * @param {string} message - The message to display
   */
  info(message) {
    this.show({
      type: 'info',
      title: 'Information',
      message: message,
      duration: 4500,
      sound: false
    });
  },

  /**
   * Show a custom toast notification
   * @param {Object} config - Toast configuration
   * @param {string} config.type - Toast type (success, error, warning, info, login)
   * @param {string} config.title - Toast title
   * @param {string} config.message - Toast message
   * @param {number} config.duration - Duration in ms (default: 5000)
   * @param {boolean} config.sound - Whether to play sound (default: false)
   */
  custom(config) {
    this.show(config);
  },

  /**
   * Internal method to show toast
   * @param {Object} config - Toast configuration
   */
  show(config) {
    if (typeof window.gaShowToast === 'function') {
      window.gaShowToast(config);
    } else {
      console.warn('Toast system not loaded yet');
    }
  }
};

// Make toast available globally
if (typeof window !== 'undefined') {
  window.toast = toast;
}

export default toast;
