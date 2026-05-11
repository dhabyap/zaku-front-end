@extends('layouts.guest')

@section('content')
@include('components.toast-notification')

<div class="min-h-dvh flex flex-col justify-center px-6 py-12" x-data="forgotPasswordForm()">
    <div class="max-w-md w-full mx-auto space-y-8 bg-paper border-4 border-ink p-8 shadow-bs-lg relative overflow-hidden">
        <!-- Abstract background element -->
        <div class="absolute -top-10 -right-10 h-32 w-32 bg-punch opacity-10 rotate-12 pointer-events-none"></div>
        
        <div class="relative z-10">
            <h2 class="text-4xl font-display font-black text-ink uppercase tracking-tighter leading-none mb-2">Atur Ulang Password</h2>
            <p class="font-mono text-sm text-ink/60 italic leading-relaxed">Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.</p>
        </div>

        <form class="space-y-6 relative z-10" @submit.prevent="submit">
            <div class="space-y-2">
                <label for="email" class="block font-display font-bold uppercase text-ink text-xs tracking-[0.2em]">Registered Email</label>
                <input id="email" x-model="email" type="email" required 
                    class="w-full bg-cream border-4 border-ink p-4 font-mono text-ink placeholder:text-ink/30 focus:outline-none focus:bg-white transition-colors"
                    placeholder="nama@email.com">
            </div>

            <button type="submit" :disabled="loading"
                class="w-full bg-punch text-paper border-4 border-ink p-4 font-display font-black uppercase text-xl shadow-bs hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all disabled:opacity-50">
                <span x-show="!loading">Kirim Tautan Reset</span>
                <span x-show="loading" x-cloak class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses...
                </span>
            </button>
        </form>

        <div class="pt-6 border-t-4 border-ink text-center relative z-10">
            <a href="/login" class="inline-flex items-center gap-2 font-mono text-sm text-ink hover:text-punch transition-colors group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Halaman Login</span>
            </a>
        </div>
    </div>
</div>

<script>
    function forgotPasswordForm() {
        return {
            email: '',
            loading: false,
            async submit() {
                if (this.loading) return;
                
                this.loading = true;
                try {
                    await window.apiClient.post('/auth/forgot-password', { email: this.email });
                    window.utils.showToast('success', 'Instruksi reset password telah dikirim ke email Anda.');
                    this.email = '';
                } catch (error) {
                    console.error('Forgot password error:', error);
                    const detailedMsg = window.utils.parseApiError(error, 'Alamat email tidak ditemukan atau terjadi kesalahan.');
                    window.utils.showToast('error', detailedMsg, true);
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endsection
