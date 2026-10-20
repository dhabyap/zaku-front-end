@extends('layouts.guest')

@section('content')
<div x-data="verifyEmail()" style="height:100dvh;display:flex;flex-direction:column;background:var(--ink);justify-content:center;padding:24px;">
    <div style="max-width:400px;width:100%;margin:0 auto;background:var(--paper);border:var(--border);box-shadow:var(--bs-xl);padding:28px 24px;">
        <div style="text-align:center;margin-bottom:20px;">
            <div style="width:64px;height:64px;background:var(--sky);border:var(--border);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--ink)" stroke-width="2.5">
                    <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="reg-title" style="font-size:24px;color:var(--ink);margin-bottom:8px;">Verifikasi Email</div>
            <div style="font-family:var(--font-mono);font-size:11px;color:rgba(17,16,16,.5);line-height:1.6;">
                Terima kasih telah mendaftar! Sebelum memulai, harap verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan.
            </div>
        </div>

        <button @click="resend" class="btn-main" :disabled="loading" style="margin-top:8px;">
            <span x-show="!loading">KIRIM ULANG EMAIL →</span>
            <span x-show="loading" x-cloak>MENGIRIM...</span>
        </button>

        <button @click="logout" class="btn-main alt">KELUAR SESI</button>

        <div class="auth-link" style="margin-top:16px;">
            <a href="/verify-manual">Punya kode verifikasi? Masukkan secara manual di sini</a>
        </div>
    </div>
</div>

<script>
    function verifyEmail() {
        return {
            loading: false,
            userEmail: new URLSearchParams(window.location.search).get('email') || window.auth.getUser()?.email,
            async resend() {
                if (this.loading) return;
                if (!this.userEmail) {
                    window.utils.showToast('error', 'Alamat email tidak ditemukan. Silakan login kembali.');
                    return;
                }
                this.loading = true;
                try {
                    await window.apiClient.post('/v1/auth/resend-verification', { email: this.userEmail });
                    window.utils.showToast('success', 'Email verifikasi baru telah dikirim ke alamat Anda.');
                } catch (error) {
                    console.error('Resend error:', error);
                    window.utils.showToast('error', window.utils.parseApiError(error, 'Gagal mengirim ulang email.'), true);
                } finally {
                    this.loading = false;
                }
            },
            logout() {
                window.auth.clearToken();
                window.location.href = '/login';
            }
        }
    }
</script>
@endsection
