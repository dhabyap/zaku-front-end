@extends('layouts.guest')

@section('content')
<div x-data="loginForm()" style="height:100dvh;display:flex;flex-direction:column;background:var(--punch);justify-content:flex-end;position:relative;">
    <div style="position:absolute;inset:0;overflow:hidden;pointer-events:none;">
        <div style="position:absolute;inset:0;background-image:linear-gradient(var(--ink) 1px, transparent 1px),linear-gradient(90deg, var(--ink) 1px, transparent 1px);background-size:40px 40px;opacity:.08;"></div>
    </div>

    <div style="position:absolute;top:0;left:0;right:0;padding:52px 28px 0;display:flex;flex-direction:column;gap:10px;">
        <div style="display:flex;align-items:center;gap:14px;">
            <div class="wordmark-icon">💸</div>
            <div class="wordmark-text">DOMPET</div>
        </div>
        <div style="font-family:var(--font-mono);font-size:12px;letter-spacing:2px;color:rgba(17,16,16,.55);padding-left:2px;">TRACK · CONTROL · THRIVE</div>
    </div>

    <div style="font-family:var(--font-serif);font-size:96px;font-weight:300;font-style:italic;color:rgba(17,16,16,.12);line-height:1;letter-spacing:-4px;position:absolute;right:20px;top:44px;pointer-events:none;">Rp</div>

    <div class="login-sheet">
        <div class="sheet-label">// MASUK KE AKUN</div>

        <form @submit.prevent="submit">
            <div class="field">
                <label>EMAIL</label>
                <input type="email" placeholder="kamu@email.com" x-model="formData.email">
            </div>
            <div class="field">
                <label>PASSWORD</label>
                <div class="pw-row">
                    <input type="password" placeholder="••••••••" id="li-pw" x-model="formData.password">
                    <button type="button" class="pw-eye" onclick="togglePw('li-pw',this)">LIHAT</button>
                </div>
            </div>

            <button type="submit" class="btn-main" :disabled="loading">
                <span x-show="!loading">MASUK SEKARANG →</span>
                <span x-show="loading" x-cloak>MEMPROSES...</span>
            </button>
        </form>

        <button class="btn-main alt" onclick="window.location.href='/register'">BUAT AKUN BARU</button>

        <div class="auth-link">Demo? <a onclick="demoLogin()">Langsung masuk →</a></div>
    </div>
</div>

<script>
    function togglePw(id, btn) {
        const inp = document.getElementById(id);
        if (inp.type === 'password') { inp.type = 'text'; btn.textContent = 'SEMBUNYIKAN'; }
        else { inp.type = 'password'; btn.textContent = 'LIHAT'; }
    }

    function loginForm() {
        return {
            formData: {
                email: '',
                password: '',
                remember: false
            },
            loading: false,
            init() {
                const params = new URLSearchParams(window.location.search);
                if (params.get('session') === 'expired') {
                    window.utils.showToast('error', 'Sesi Anda telah berakhir. Silakan login kembali.', true);
                }
            },
            async submit() {
                if (this.loading) return;
                this.loading = true;
                try {
                    const response = await window.apiClient.post('/auth/login', this.formData);
                    const { access_token, refresh_token, user } = response.data.data;
                    window.auth.setToken(access_token, refresh_token);
                    window.auth.setUser(user);
                    window.utils.showToast('success', 'Login berhasil! Sedang mengalihkan...');
                    setTimeout(() => {
                        window.location.href = '/dashboard';
                    }, 1500);
                } catch (error) {
                    console.error('Login error:', error);
                    const detailedMsg = window.utils.parseApiError(error, 'Email atau password salah. Silakan coba lagi.');
                    window.utils.showToast('error', detailedMsg, true);
                } finally {
                    this.loading = false;
                }
            }
        }
    }

    function demoLogin() {
        window.auth.setToken('demo-token', 'demo-refresh');
        window.auth.setUser({ name: 'Budi Santoso', email: 'budi@email.com' });
        window.location.href = '/dashboard';
    }
</script>
@endsection
