@extends('layouts.guest')

@section('content')
<div x-data="manualVerifyForm()" style="height:100dvh;display:flex;flex-direction:column;background:var(--ink);justify-content:center;padding:24px;">
    <div style="max-width:400px;width:100%;margin:0 auto;background:var(--paper);border:var(--border);box-shadow:var(--bs-xl);padding:28px 24px;">
        <div style="text-align:center;margin-bottom:20px;">
            <div class="reg-title" style="font-size:24px;color:var(--ink);margin-bottom:8px;">Verifikasi Manual</div>
            <div style="font-family:var(--font-mono);font-size:11px;color:rgba(17,16,16,.5);line-height:1.6;">
                Masukkan email Anda dan kode verifikasi yang Anda terima untuk mengaktifkan akun.
            </div>
        </div>

        <form @submit.prevent="submit">
            <div class="field">
                <label>ALAMAT EMAIL</label>
                <input type="email" x-model="formData.email" placeholder="nama@email.com">
            </div>
            <div class="field">
                <label>KODE VERIFIKASI / TOKEN</label>
                <input type="text" x-model="formData.token" placeholder="Masukkan kode..." style="text-transform:uppercase;">
            </div>
            <button type="submit" class="btn-main" :disabled="loading">
                <span x-show="!loading">VERIFIKASI SEKARANG →</span>
                <span x-show="loading" x-cloak>MEMPROSES...</span>
            </button>
        </form>

        <div style="margin-top:20px;padding-top:16px;border-top:var(--border);text-align:center;">
            <div class="auth-link">
                <a href="/verify-email">Kirim ulang email</a>
            </div>
            <div class="auth-link" style="margin-top:8px;">
                <a href="/login">Kembali ke Login</a>
            </div>
        </div>
    </div>
</div>

<script>
    function manualVerifyForm() {
        return {
            formData: {
                email: new URLSearchParams(window.location.search).get('email') || window.auth.getUser()?.email || '',
                token: ''
            },
            loading: false,
            async submit() {
                if (this.loading) return;
                this.loading = true;
                try {
                    await window.apiClient.post('/auth/verify-email', this.formData);
                    window.utils.showToast('success', 'Email berhasil diverifikasi! Silakan login kembali.');
                    setTimeout(() => { window.location.href = '/login'; }, 2000);
                } catch (error) {
                    console.error('Manual verification error:', error);
                    window.utils.showToast('error', window.utils.parseApiError(error, 'Gagal memverifikasi email.'), true);
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endsection
