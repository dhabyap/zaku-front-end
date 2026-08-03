export function formatRupiah(value) {
    if (!value && value !== 0) return 'Rp 0';
    return 'Rp ' + Number(value).toLocaleString('id-ID');
}

export function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

export function handleApiError(error, fallback = 'Terjadi kesalahan. Silakan coba lagi.') {
    const msg = parseApiError(error, fallback);
    showToast('error', msg, true);
    return msg;
}

export function showToast(type, message, persistent = false) {
    const el = document.getElementById('toast-el');
    if (!el) return;

    el.textContent = message;
    el.classList.add('show');

    if (el._toastTimeout) clearTimeout(el._toastTimeout);

    if (!persistent) {
        el._toastTimeout = setTimeout(() => {
            el.classList.remove('show');
        }, 3000);
    }
}

export function confirmDialog({ title = 'Yakin?', message = '', okLabel = 'YA, LANJUTKAN', danger = false } = {}) {
    const titleEl = document.getElementById('confirm-modal-title');
    const msgEl = document.getElementById('confirm-modal-message');
    const okBtn = document.getElementById('confirm-modal-ok');

    if (!titleEl || !msgEl || !okBtn) {
        return Promise.resolve(window.confirm(`${title}\n\n${message}`));
    }

    titleEl.textContent = title;
    msgEl.textContent = message;
    okBtn.textContent = okLabel;
    okBtn.style.background = danger ? '#E53E3E' : 'var(--ink,#111010)';
    okBtn.style.borderColor = danger ? '#E53E3E' : 'var(--ink,#111010)';
    okBtn.style.color = 'var(--paper,#FFFDF7)';

    if (typeof window.__openConfirmModal === 'function') {
        window.__openConfirmModal();
    }

    return new Promise(resolve => {
        window.__confirmResolve = resolve;
    });
}
