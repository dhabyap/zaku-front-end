<nav class="fixed bottom-0 left-0 right-0 bg-ink text-paper z-50 border-t-4 border-ink">
    <div class="max-w-md mx-auto flex justify-around items-center h-20 px-4">
        <!-- Home -->
        <a href="/dashboard" class="flex flex-col items-center justify-center flex-1 h-full transition-colors {{ request()->is('dashboard') ? 'text-punch' : 'text-paper hover:text-punch-2' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="text-[10px] font-bold uppercase tracking-widest mt-1">Home</span>
        </a>

        <!-- Transaksi -->
        <a href="/transactions" class="flex flex-col items-center justify-center flex-1 h-full transition-colors {{ request()->is('transactions*') ? 'text-punch' : 'text-paper hover:text-punch-2' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <span class="text-[10px] font-bold uppercase tracking-widest mt-1">Transaksi</span>
        </a>

        <!-- Wallet -->
        <a href="/wallet/topup" class="flex flex-col items-center justify-center flex-1 h-full transition-colors {{ request()->is('wallet*') ? 'text-punch' : 'text-paper hover:text-punch-2' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
            <span class="text-[10px] font-bold uppercase tracking-widest mt-1">Wallet</span>
        </a>

        <!-- Profil -->
        <a href="/profile" class="flex flex-col items-center justify-center flex-1 h-full transition-colors {{ request()->is('profile*') ? 'text-punch' : 'text-paper hover:text-punch-2' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="text-[10px] font-bold uppercase tracking-widest mt-1">Profil</span>
        </a>
    </div>
</nav>
