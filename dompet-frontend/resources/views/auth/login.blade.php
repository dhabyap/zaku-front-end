@extends('layouts.guest')

@section('content')
<div class="min-h-dvh flex flex-col justify-center px-6 py-12" x-data="loginForm()">
    <div class="max-w-md w-full mx-auto space-y-8 bg-paper border-4 border-ink p-8 shadow-bs-lg">
        <div>
            <h2 class="text-4xl font-display font-black text-ink uppercase tracking-tighter">Login</h2>
            <p class="mt-2 font-mono text-sm text-ink/60 italic">Masukkan kredensial Anda untuk melanjutkan ke dashboard.</p>
        </div>

        <form class="space-y-6" @submit.prevent="submit">
            <div class="space-y-2">
                <label for="email" class="block font-display font-bold uppercase text-ink text-sm">Email Address</label>
                <input id="email" x-model="formData.email" type="email" required 
                    class="w-full bg-cream border-4 border-ink p-4 font-mono text-ink placeholder:text-ink/30 focus:outline-none focus:bg-white transition-colors"
                    placeholder="nama@email.com">
            </div>

            <div class="space-y-2">
                <div class="flex justify-between items-end">
                    <label for="password" class="block font-display font-bold uppercase text-ink text-sm">Password</label>
                </div>
                <input id="password" x-model="formData.password" type="password" required 
                    class="w-full bg-cream border-4 border-ink p-4 font-mono text-ink placeholder:text-ink/30 focus:outline-none focus:bg-white transition-colors"
                    placeholder="••••••••">
            </div>

            <div class="flex items-center">
                <div class="relative flex items-center">
                    <input id="remember" x-model="formData.remember" type="checkbox" 
                        class="h-6 w-6 border-4 border-ink bg-cream checked:bg-punch appearance-none cursor-pointer relative checked:after:content-['✓'] checked:after:absolute checked:after:inset-0 checked:after:flex checked:after:items-center checked:after:justify-center checked:after:text-paper checked:after:font-bold">
                </div>
                <label for="remember" class="ml-3 font-mono text-sm text-ink cursor-pointer select-none">Ingat saya di perangkat ini</label>
            </div>

            <button type="submit" :disabled="loading"
                class="w-full bg-punch text-paper border-4 border-ink p-4 font-display font-black uppercase text-xl shadow-bs hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!loading">Masuk Sekarang</span>
                <span x-show="loading" x-cloak class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses...
                </span>
            </button>
        </form>

        <div class="pt-4 border-t-4 border-ink space-y-3">
            <p class="font-mono text-sm text-ink">
                Belum punya akun? 
                <a href="/register" class="text-punch font-bold underline decoration-2 underline-offset-4 hover:bg-punch hover:text-paper transition-colors px-1">Daftar di sini</a>
            </p>
            <p class="font-mono text-sm">
                <a href="/forgot-password" class="text-ink/40 hover:text-ink transition-colors underline decoration-1 underline-offset-4">Lupa password Anda?</a>
            </p>
        </div>
    </div>
</div>

<script>
    function loginForm() {
        return {
            formData: {
                email: '',
                password: '',
                remember: false
            },
            loading: false,
            async submit() {
                if (this.loading) return;
                
                this.loading = true;
                try {
                    const response = await window.apiClient.post('/auth/login', this.formData);
                    
                    const { access_token, refresh_token, user } = response.data.data;
                    
                    // Save to local storage
                    window.auth.setToken(access_token, refresh_token);
                    window.auth.setUser(user);
                    
                    window.utils.showToast('success', 'Login berhasil! Sedang mengalihkan...');
                    
                    setTimeout(() => {
                        window.location.href = '/dashboard';
                    }, 1500);
                } catch (error) {
                    console.error('Login error:', error);
                    // Parse detailed error messages from backend
                    const detailedMsg = window.utils.parseApiError(error, 'Email atau password salah. Silakan coba lagi.');
                    // Show persistent error toast so user can read details
                    window.utils.showToast('error', detailedMsg, true);
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endsection
