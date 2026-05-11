@extends('layouts.guest')

@section('content')
@include('components.toast-notification')

<div class="min-h-dvh flex flex-col justify-center px-6 py-12" x-data="registerForm()">
    <div class="max-w-md w-full mx-auto space-y-8 bg-paper border-4 border-ink p-8 shadow-bs-lg">
        <div>
            <div class="h-12 w-12 bg-mint border-4 border-ink mb-4 shadow-bs"></div>
            <h2 class="text-4xl font-display font-black text-ink uppercase tracking-tighter">Daftar Akun</h2>
            <p class="mt-2 font-mono text-sm text-ink/60 italic">Bergabunglah dengan Zaku untuk mulai mengelola dompet digital Anda secara modern.</p>
        </div>

        <form class="space-y-5" @submit.prevent="submit">
            <div class="space-y-1">
                <label for="name" class="block font-display font-bold uppercase text-ink text-xs tracking-widest">Nama Lengkap</label>
                <input id="name" x-model="formData.name" type="text" required 
                    class="w-full bg-cream border-4 border-ink p-4 font-mono text-ink placeholder:text-ink/30 focus:outline-none focus:bg-white transition-colors"
                    placeholder="Contoh: Dhaby AP">
            </div>

            <div class="space-y-1">
                <label for="email" class="block font-display font-bold uppercase text-ink text-xs tracking-widest">Email</label>
                <input id="email" x-model="formData.email" type="email" required 
                    class="w-full bg-cream border-4 border-ink p-4 font-mono text-ink placeholder:text-ink/30 focus:outline-none focus:bg-white transition-colors"
                    placeholder="nama@email.com">
            </div>

            <div class="space-y-1">
                <label for="password" class="block font-display font-bold uppercase text-ink text-xs tracking-widest">Password</label>
                <input id="password" x-model="formData.password" type="password" required 
                    class="w-full bg-cream border-4 border-ink p-4 font-mono text-ink placeholder:text-ink/30 focus:outline-none focus:bg-white transition-colors"
                    placeholder="Minimal 8 karakter">
            </div>

            <div class="space-y-1">
                <label for="password_confirmation" class="block font-display font-bold uppercase text-ink text-xs tracking-widest">Konfirmasi Password</label>
                <input id="password_confirmation" x-model="formData.password_confirmation" type="password" required 
                    class="w-full bg-cream border-4 border-ink p-4 font-mono text-ink placeholder:text-ink/30 focus:outline-none focus:bg-white transition-colors"
                    placeholder="Ulangi password">
            </div>

            <div class="pt-2">
                <button type="submit" :disabled="loading"
                    class="w-full bg-mint text-ink border-4 border-ink p-4 font-display font-black uppercase text-xl shadow-bs hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!loading">Buat Akun Sekarang</span>
                    <span x-show="loading" x-cloak class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-ink" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Mendaftarkan...
                    </span>
                </button>
            </div>
        </form>

        <div class="pt-4 border-t-4 border-ink">
            <p class="font-mono text-sm text-ink text-center">
                Sudah punya akun? 
                <a href="/login" class="text-punch font-bold underline decoration-2 underline-offset-4 hover:bg-punch hover:text-paper transition-colors px-1">Login di sini</a>
            </p>
        </div>
    </div>
</div>

<script>
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
                    const message = error.response?.data?.message || 'Gagal membuat akun. Silakan periksa kembali data Anda.';
                    window.utils.showToast('error', message);
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endsection
