@extends('layouts.guest')

@section('content')
<div style="height:100dvh;display:flex;flex-direction:column;background:var(--ink);">
    <div class="reg-top">
        <div class="reg-top-row">
            <button class="back-btn" onclick="window.location.href='/login'">←</button>
            <div style="font-family:var(--font-mono);font-size:10px;letter-spacing:2px;color:rgba(245,240,232,.4)">DOMPET / DAFTAR</div>
        </div>
        <div class="reg-title">Buat Akun.</div>
        <div class="reg-sub">MULAI PERJALANAN FINANSIALMU</div>
    </div>
    <div class="reg-body" x-data="registerForm()">
        <form @submit.prevent="submit">
            <div class="field">
                <label>NAMA LENGKAP</label>
                <input type="text" placeholder="Nama kamu" x-model="formData.name">
            </div>
            <div class="field">
                <label>EMAIL</label>
                <input type="email" placeholder="kamu@email.com" x-model="formData.email">
            </div>
            <div class="field">
                <label>PASSWORD</label>
                <div class="pw-row">
                    <input type="password" placeholder="Min. 8 karakter" id="rg-pw" x-model="formData.password">
                    <button type="button" class="pw-eye" onclick="togglePw('rg-pw',this)">LIHAT</button>
                </div>
            </div>
            <div class="field">
                <label>KONFIRMASI PASSWORD</label>
                <input type="password" placeholder="Ulangi password" x-model="formData.password_confirmation">
            </div>
            <button type="submit" class="btn-main" :disabled="loading" style="margin-top:4px">
                <span x-show="!loading">BUAT AKUN →</span>
                <span x-show="loading" x-cloak>MEMPROSES...</span>
            </button>
        </form>
        <div class="auth-link" style="margin-top:18px">Sudah punya akun? <a onclick="window.location.href='/login'">Masuk</a></div>
    </div>
</div>

<script>
    function togglePw(id, btn) {
        const inp = document.getElementById(id);
        if (inp.type === 'password') { inp.type = 'text'; btn.textContent = 'SEMBUNYIKAN'; }
        else { inp.type = 'password'; btn.textContent = 'LIHAT'; }
    }

    function registerForm() {
        return {
            formData: {
                name: '',
                email: '',
                password: '',
                password_confirmation: ''
            },
            loading: false,
            async submit() {
                if (this.loading) return;
                if (this.formData.password !== this.formData.password_confirmation) {
                    window.utils.showToast('error', 'Konfirmasi password tidak sesuai.');
                    return;
                }
                if (this.formData.password.length < 8) {
                    window.utils.showToast('error', 'Password harus minimal 8 karakter.');
                    return;
                }
                this.loading = true;
                try {
                    await window.apiClient.post('/auth/register', this.formData);
                    window.utils.showToast('success', 'Akun berhasil dibuat! Silakan cek email Anda.');
                    setTimeout(() => {
                        window.location.href = '/verify-email';
                    }, 2000);
                } catch (error) {
                    console.error('Registration error:', error);
                    const detailedMsg = window.utils.parseApiError(error, 'Gagal membuat akun. Silakan periksa kembali data Anda.');
                    window.utils.showToast('error', detailedMsg, true);
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endsection
