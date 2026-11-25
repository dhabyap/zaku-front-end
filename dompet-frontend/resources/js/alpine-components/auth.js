// resources/js/alpine-components/auth.js
// Alpine components: loginForm, registerForm, forgotPasswordForm, verifyEmail, manualVerifyForm, processVerification

export function registerAuth(Alpine) {

    // ── Auth: Login ──
    Alpine.data('loginForm', () => ({
        formData: { email: '', password: '', remember: false },
        loading: false,
        errors: { email: '', password: '' },
        showPassword: false,
        validate() {
            this.errors = { email: '', password: '' };
            let ok = true;
            if (!this.formData.email) { this.errors.email = 'Email wajib diisi'; ok = false; }
            else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.formData.email)) { this.errors.email = 'Format email tidak valid'; ok = false; }
            if (!this.formData.password) { this.errors.password = 'Password wajib diisi'; ok = false; }
            return ok;
        },
        init() {
            const params = new URLSearchParams(window.location.search);
            if (params.get('session') === 'expired') {
                window.utils.showToast('error', 'Sesi Anda telah berakhir. Silakan login kembali.', true);
            }

        },
        async submit() {
            if (this.loading || !this.validate()) return;
            this.loading = true;
            try {
                const response = await window.apiClient.post('/v1/auth/login', this.formData);
                const { user } = response.data.data;
                window.auth.setUser(user, this.formData.remember);
                window.utils.showToast('success', 'Anda berhasil masuk!');
                window.location.assign('/dashboard');
            } catch (error) {
                const msg = window.utils.parseApiError(error, 'Email atau kata sandi belum benar. Periksa kembali, lalu coba lagi.');
                window.utils.showToast('error', msg, true);
            } finally { this.loading = false; }
        },
        demoLogin() {
            window.auth.setUser({ name: 'Budi Santoso', email: 'budi@email.com' }, true);
            window.location.href = '/dashboard';
        }
    }));

    // ── Auth: Register ──
    Alpine.data('registerForm', () => ({
        formData: { name: '', email: '', password: '', password_confirmation: '' },
        loading: false,
        showPassword: false,
        async submit() {
            if (this.loading) return;
            if (this.formData.password !== this.formData.password_confirmation) {
                window.utils.showToast('error', 'Konfirmasi kata sandi tidak sesuai.'); return;
            }
            if (this.formData.password.length < 8) {
                window.utils.showToast('error', 'Kata sandi harus minimal 8 karakter.'); return;
            }
            this.loading = true;
            try {
                await window.apiClient.post('/v1/auth/register', this.formData);
                window.utils.showToast('success', 'Akun berhasil dibuat! Silakan masuk.');
                setTimeout(() => { window.location.href = '/login'; }, 1500);
            } catch (error) {
                const msg = window.utils.parseApiError(error, 'Gagal membuat akun. Periksa kembali data Anda, lalu coba lagi.');
                window.utils.showToast('error', msg, true);
            } finally { this.loading = false; }
        }
    }));

    // ── Auth: Forgot Password ──
    Alpine.data('forgotPasswordForm', () => ({
        email: '',
        loading: false,
        sent: false,
        async submit() {
            if (this.loading) return;
            this.loading = true;
            try {
                await window.apiClient.post('/v1/auth/forgot-password', { email: this.email });
                this.sent = true;
                window.utils.showToast('success', 'Tautan ubah kata sandi sudah dikirim ke email Anda.');
            } catch (error) {
                const msg = window.utils.parseApiError(error, 'Gagal mengirim tautan ubah kata sandi. Coba lagi.');
                window.utils.showToast('error', msg, true);
            } finally { this.loading = false; }
        }
    }));

    // ── Auth: Verify Email ──
    Alpine.data('verifyEmail', () => ({
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
                window.utils.showToast('success', 'Email verifikasi baru sudah dikirim!');
            } catch (error) {
                const msg = window.utils.parseApiError(error, 'Gagal mengirim kode verifikasi. Coba lagi sebentar lagi.');
                window.utils.showToast('error', msg, true);
            } finally { this.loading = false; }
        },
        logout() {
            window.auth.clearToken();
        }
    }));

    // ── Auth: Manual Verify ──
    Alpine.data('manualVerifyForm', () => ({
        formData: {
            email: new URLSearchParams(window.location.search).get('email') || window.auth.getUser()?.email || '',
            code: new URLSearchParams(window.location.search).get('code') || ''
        },
        loading: false,
        async submit() {
            if (this.loading) return;
            if (!this.formData.email) {
                window.utils.showToast('error', 'Alamat email wajib diisi.'); return;
            }
            if (!this.formData.code) {
                window.utils.showToast('error', 'Kode verifikasi wajib diisi.'); return;
            }
            this.loading = true;
            try {
                const response = await window.apiClient.post('/v1/auth/verify-email', this.formData);
                window.utils.showToast('success', 'Email berhasil diverifikasi! Silakan login.');
                setTimeout(() => { window.location.href = '/login'; }, 2000);
            } catch (error) {
                const msg = window.utils.parseApiError(error, 'Gagal memverifikasi email.');
                window.utils.showToast('error', msg, true);
            } finally { this.loading = false; }
        }
    }));

    // ── Auth: Process Verify ──
    Alpine.data('processVerification', () => ({
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
    }));

}