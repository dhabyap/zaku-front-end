@extends('layouts.guest')

@section('content')
@include('components.toast-notification')

<div class="min-h-dvh flex flex-col justify-center px-6 py-12" x-data="verifyEmail()">
    <div class="max-w-md w-full mx-auto space-y-8 bg-paper border-4 border-ink p-8 shadow-bs-lg">
        <div class="text-center">
            <div class="inline-flex items-center justify-center h-24 w-24 bg-sky border-4 border-ink mb-6 shadow-bs rotate-3">
                <svg class="h-12 w-12 text-ink" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h2 class="text-4xl font-display font-black text-ink uppercase tracking-tighter leading-none mb-4">Verifikasi Email</h2>
            <div class="p-4 bg-cream border-2 border-ink font-mono text-sm text-ink leading-relaxed text-left italic">
                Terima kasih telah mendaftar! Sebelum memulai, harap verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan.
            </div>
        </div>

        <div class="space-y-4">
            <button @click="resend" :disabled="loading"
                class="w-full bg-punch text-paper border-4 border-ink p-4 font-display font-black uppercase text-xl shadow-bs hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all disabled:opacity-50">
                <span x-show="!loading">Kirim Ulang Email</span>
                <span x-show="loading" x-cloak class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Mengirim...
                </span>
            </button>

            <button @click="logout"
                class="w-full bg-ink text-paper border-4 border-ink p-4 font-display font-black uppercase text-lg shadow-bs hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all active:scale-95">
                Keluar Sesi
            </button>

            <div class="pt-4 text-center">
                <a href="/verify-manual" class="font-mono text-xs text-ink underline decoration-2 underline-offset-4 hover:text-punch transition-colors">
                    Punya kode verifikasi? Masukkan secara manual di sini
                </a>
            </div>
        </div>

        <div class="text-center">
            <p class="font-mono text-[10px] text-ink/40 uppercase tracking-widest">Zaku &bull; Secure Wallet Interface</p>
        </div>
    </div>
</div>

<script>
    function verifyEmail() {
        return {
            loading: false,
            // Try to get email from URL or Session
            userEmail: new URLSearchParams(window.location.search).get('email') || window.auth.getUser()?.email,
            
            async resend() {
                if (this.loading) return;
                
                if (!this.userEmail) {
                    window.utils.showToast('error', 'Alamat email tidak ditemukan. Silakan login kembali.');
                    return;
                }

                this.loading = true;
                try {
                    // Endpoint /auth/resend-verification (baseURL already contains /api)
                    await window.apiClient.post('/auth/resend-verification', {
                        email: this.userEmail
                    });
                    
                    window.utils.showToast('success', 'Email verifikasi baru telah dikirim ke alamat Anda.');
                } catch (error) {
                    console.error('Resend error:', error);
                    // Use detailed error message from backend if available
                    const detailedMsg = window.utils.parseApiError(error, 'Gagal mengirim ulang email. Silakan coba lagi beberapa saat lagi.');
                    window.utils.showToast('error', detailedMsg, true);
                } finally {
                    this.loading = false;
                }
            },
            logout() {
                window.auth.clearToken();
            }
        }
    }
</script>
@endsection
