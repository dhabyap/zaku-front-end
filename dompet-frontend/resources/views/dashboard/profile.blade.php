@extends('layouts.app')

@section('content')
<div x-data="profilePage()" class="space-y-8 p-6 pb-24 max-w-md mx-auto">
    <h1 class="text-4xl font-display font-black text-ink uppercase tracking-tighter">Profil Saya</h1>

    <!-- User Info Card -->
    <div class="bg-paper border-4 border-ink p-8 shadow-bs-lg relative overflow-hidden">
        <div class="relative z-10 flex flex-col items-center text-center space-y-4">
            <div class="h-24 w-24 bg-mint border-4 border-ink shadow-bs flex items-center justify-center">
                <span class="text-4xl font-display font-black text-ink" x-text="user?.name?.charAt(0).toUpperCase() || '?'"></span>
            </div>
            
            <div class="space-y-1">
                <h2 class="text-2xl font-display font-black text-ink uppercase leading-none" x-text="user?.name || 'Loading...'"></h2>
                <p class="font-mono text-xs font-bold text-ink/50 uppercase tracking-widest" x-text="user?.email || ''"></p>
            </div>
        </div>

        <!-- Brutalist accents -->
        <div class="absolute -top-4 -right-4 h-12 w-12 bg-punch border-4 border-ink rotate-12"></div>
        <div class="absolute -bottom-4 -left-4 h-12 w-12 bg-sky border-4 border-ink -rotate-12"></div>
    </div>

    <!-- Actions List -->
    <div class="space-y-4">
        <button class="w-full bg-paper border-4 border-ink p-4 flex justify-between items-center shadow-bs hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all group">
            <span class="font-display font-black text-ink uppercase tracking-widest">Edit Profil</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        <button class="w-full bg-paper border-4 border-ink p-4 flex justify-between items-center shadow-bs hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all group">
            <span class="font-display font-black text-ink uppercase tracking-widest">Keamanan</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        <button @click="logout()" class="w-full bg-punch border-4 border-ink p-4 flex justify-between items-center shadow-bs hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all group">
            <span class="font-display font-black text-paper uppercase tracking-widest">Keluar Akun</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-paper group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
        </button>
    </div>

    <div class="pt-8 text-center">
        <p class="font-mono text-[10px] font-bold text-ink/30 uppercase tracking-[0.2em]">Zaku v1.0.0 — Brutalist Finance</p>
    </div>
</div>

<script>
    function profilePage() {
        return {
            user: window.auth.getUser(),
            async init() {
                // Optionally refresh user data from API
                try {
                    const res = await window.apiClient.get('/auth/me');
                    this.user = res.data.data;
                    window.auth.setUser(this.user);
                } catch (e) {
                    console.error('Fetch profile error:', e);
                }
            },
            logout() {
                if (confirm('Apakah Anda yakin ingin keluar?')) {
                    window.auth.clearToken();
                }
            }
        }
    }
</script>
@endsection
