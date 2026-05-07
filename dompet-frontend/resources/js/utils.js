// resources/js/utils.js

export const formatRupiah = (amount) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
};

export const showToast = (type, message) => {
    // Placeholder for toast logic
    console.log(`[${type.toUpperCase()}] ${message}`);
};

export const formatDate = (date) => {
    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'long'
    }).format(new Date(date));
};
