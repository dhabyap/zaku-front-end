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
 * @param {string} type - 'success' | 'error' | 'info'
 * @param {string} message 
 */
export const showToast = (type, message) => {
    window.dispatchEvent(new CustomEvent('show-toast', {
        detail: { type, message }
    }));
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
