// resources/js/utils.js

/**
 * Format number to Rupiah (Rp 100.000)
 * @param {number} amount
 * @returns {string}
 */
export const formatRupiah = (amount) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
};

/**
 * Trigger toast notification
 * @param {string} type      - 'success' | 'error' | 'info'
 * @param {string} message   - Message text (can contain \n for multiple lines)
 * @param {boolean} persistent - If true, toast does NOT auto-hide (default: true for error, false for others)
 */
export const showToast = (type, message, persistent) => {
    // Error toasts are persistent by default so users can read them
    const isPersistent = persistent !== undefined ? persistent : (type === 'error');
    window.dispatchEvent(new CustomEvent('show-toast', {
        detail: { type, message, persistent: isPersistent }
    }));
};

/**
 * Parse API error response and return a human-readable string.
 * Handles Laravel validation error format { message, errors: { field: [msg] } }
 * @param {Error} error  - Axios error object
 * @param {string} fallback - Default message if parsing fails
 * @returns {string}
 */
export const parseApiError = (error, fallback = 'Terjadi kesalahan. Silakan coba lagi.') => {
    const data = error?.response?.data;

    if (!data) return fallback;

    // Collect all validation field errors
    if (data.errors && typeof data.errors === 'object') {
        const lines = [];
        // Add top-level message if present
        if (data.message) lines.push(data.message);
        // Add each field error
        Object.values(data.errors).forEach(msgs => {
            if (Array.isArray(msgs)) {
                msgs.forEach(m => lines.push('• ' + m));
            } else if (typeof msgs === 'string') {
                lines.push('• ' + msgs);
            }
        });
        return lines.join('\n');
    }

    // Single message from API
    return data.message || fallback;
};

/**
 * Format date to Indonesian format (8 Mei 2026)
 * @param {string|Date} date
 * @returns {string}
 */
export const formatDate = (date) => {
    if (!date) return '-';
    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'long'
    }).format(new Date(date));
};
