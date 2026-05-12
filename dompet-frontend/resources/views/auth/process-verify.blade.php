@extends('layouts.guest')

@section('content')
@include('components.toast-notification')

<div class="min-h-dvh flex flex-col justify-center px-6 py-12" x-data="processVerification()">
    <div class="max-w-md w-full mx-auto space-y-8 bg-paper border-4 border-ink p-8 shadow-bs-lg text-center">
        
        <div x-show="status === 'loading'" class="space-y-6">
            <div class="inline-flex items-center justify-center h-24 w-24 bg-sky border-4 border-ink shadow-bs animate-pulse">
                <svg class="h-10 w-10 text-ink animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-display font-black text-ink uppercase tracking-tighter leading-none">Memverifikasi...</h2>
            <p class="font-mono text-sm text-ink/60 italic">Sedang memverifikasi email Anda, mohon tunggu sebentar.</p>
        </div>

        <div x-show="status === 'success'" style="display: none;" class="space-y-6">
            <div class="inline-flex items-center justify-center h-24 w-24 bg-mint border-4 border-ink shadow-bs">
                <svg class="h-12 w-12 text-ink" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="text-3xl font-display font-black text-ink uppercase tracking-tighter leading-none">Berhasil!</h2>
            <p class="font-mono text-sm text-ink/80">Email Anda telah berhasil diverifikasi. Mengalihkan ke halaman utama...</p>
        </div>

        <div x-show="status === 'error'" style="display: none;" class="space-y-6">
            <div class="inline-flex items-center justify-center h-24 w-24 bg-punch border-4 border-ink shadow-bs">
                <svg class="h-12 w-12 text-paper" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <h2 class="text-3xl font-display font-black text-ink uppercase tracking-tighter leading-none">Verifikasi Gagal</h2>
            <p class="font-mono text-sm text-ink/80" x-text="errorMessage"></p>
            
            <a href="/verify-email" class="inline-block w-full bg-ink text-paper border-4 border-ink p-4 font-display font-black uppercase text-lg shadow-bs hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all active:scale-95">
                Kirim Ulang Tautan
            </a>
        </div>

    </div>
</div>

<script>
    function processVerification() {
        return {
            status: 'loading', // loading, success, error
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
                    // Call backend to verify email
                    await window.apiClient.post('/auth/verify-email', { token: token });
                    
                    this.status = 'success';
                    window.utils.showToast('success', 'Email berhasil diverifikasi!');
                    
                    // Check if user is logged in
                    setTimeout(() => {
                        if (window.auth.isLoggedIn()) {
                            window.location.href = '/dashboard';
                        } else {
                            window.location.href = '/login';
                        }
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
