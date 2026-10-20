@extends('layouts.guest')

@section('content')
<div x-data="processVerification()" style="height:100dvh;display:flex;flex-direction:column;background:var(--paper);justify-content:center;padding:24px;">
    <div style="max-width:400px;width:100%;margin:0 auto;background:var(--paper);border:var(--border);box-shadow:var(--bs-xl);padding:28px 24px;text-align:center;">

        <div x-show="status === 'loading'" style="display:flex;flex-direction:column;align-items:center;gap:16px;">
            <div style="width:64px;height:64px;background:var(--sky);border:var(--border);display:flex;align-items:center;justify-content:center;">
                <svg class="animate-spin" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--ink)" stroke-width="2.5">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <div class="reg-title" style="font-size:24px;color:var(--ink);">Memverifikasi...</div>
            <div style="font-family:var(--font-mono);font-size:11px;color:rgba(17,16,16,.5);">Sedang memverifikasi email Anda, mohon tunggu sebentar.</div>
        </div>

        <div x-show="status === 'success'" style="display:none;flex-direction:column;align-items:center;gap:16px;">
            <div style="width:64px;height:64px;background:var(--mint);border:var(--border);display:flex;align-items:center;justify-content:center;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--ink)" stroke-width="3">
                    <path d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="reg-title" style="font-size:24px;color:var(--ink);">Berhasil!</div>
            <div style="font-family:var(--font-mono);font-size:11px;color:rgba(17,16,16,.5);">Email Anda telah berhasil diverifikasi. Mengalihkan ke halaman utama...</div>
        </div>

        <div x-show="status === 'error'" style="display:none;flex-direction:column;align-items:center;gap:16px;">
            <div style="width:64px;height:64px;background:var(--punch);border:var(--border);display:flex;align-items:center;justify-content:center;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--paper)" stroke-width="3">
                    <path d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div class="reg-title" style="font-size:24px;color:var(--ink);">Verifikasi Gagal</div>
            <div style="font-family:var(--font-mono);font-size:11px;color:rgba(17,16,16,.5);" x-text="errorMessage"></div>
            <a href="/verify-email" class="btn-main alt" style="margin-top:8px;">KIRIM ULANG TAUTAN →</a>
        </div>

    </div>
</div>

<script>
    function processVerification() {
        return {
            status: 'loading',
            errorMessage: 'Tautan verifikasi tidak valid atau sudah kadaluarsa.',
            async init() {
                const urlParams = new URLSearchParams(window.location.search);
                const token = urlParams.get('token');
                if (!token) {
                    this.status = 'error';
                    this.errorMessage = 'Token verifikasi tidak ditemukan di URL.';
                    window.utils.showToast('error', this.errorMessage, true);
                    return;
                }
                try {
                    await window.apiClient.post('/v1/auth/verify-email', { email: '', code: token });
                    this.status = 'success';
                    window.utils.showToast('success', 'Email berhasil diverifikasi!');
                    setTimeout(() => {
                        window.location.href = window.auth.isLoggedIn() ? '/dashboard' : '/login';
                    }, 2000);
                } catch (error) {
                    this.status = 'error';
                    console.error('Verification error:', error);
                    this.errorMessage = window.utils.parseApiError(error, 'Gagal memverifikasi email. Tautan mungkin sudah kadaluarsa.');
                    window.utils.showToast('error', this.errorMessage, true);
                }
            }
        }
    }
</script>
@endsection
