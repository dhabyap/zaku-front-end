<nav class="bottom-nav" id="bottom-nav" x-data="{ inputMenuOpen: false }" @click.outside="inputMenuOpen = false" @keydown.escape.window="if (inputMenuOpen) { inputMenuOpen = false; $nextTick(() => $refs.inputTrigger.focus()) }">
    <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}" id="nav-dashboard" aria-label="Beranda" @click="inputMenuOpen = false">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        <span>BERANDA</span>
    </a>
    <a href="/transactions" class="nav-item {{ request()->is('transactions*') ? 'active' : '' }}" id="nav-history" aria-label="Riwayat" @click="inputMenuOpen = false">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        <span>RIWAYAT</span>
    </a>
    <a href="/calendar" class="nav-item {{ request()->is('calendar') ? 'active' : '' }}" id="nav-calendar" aria-label="Kalender" @click="inputMenuOpen = false">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span>KALENDER</span>
    </a>
    <a href="/chat" class="nav-item chat-fab-btn {{ request()->is('chat') ? 'active' : '' }}" id="nav-chat" aria-label="AI Chat" @click="inputMenuOpen = false">
        <div class="chat-fab-inner">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </div>
        <span>AI CHAT</span>
    </a>
    <div class="nav-action-wrap">
        <button type="button" class="nav-item nav-action {{ request()->is('manual-input') ? 'active' : '' }}" id="nav-manual" x-ref="inputTrigger" :aria-label="inputMenuOpen ? 'Tutup menu input' : 'Buka menu input'" aria-controls="nav-input-menu" :aria-expanded="inputMenuOpen.toString()" @click="inputMenuOpen = !inputMenuOpen">
            <span class="nav-action-icon" aria-hidden="true">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            </span>
            <span x-text="inputMenuOpen ? 'TUTUP' : 'INPUT'">INPUT</span>
        </button>
        <div class="nav-input-menu" id="nav-input-menu" x-cloak x-show="inputMenuOpen" x-transition.origin.bottom.right role="menu" aria-label="Aksi input transaksi">
            <a href="/manual-input" role="menuitem" class="nav-input-menu-link {{ request()->is('manual-input') ? 'active' : '' }}" @click="inputMenuOpen = false">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                <span>INPUT MANUAL</span>
            </a>
        </div>
    </div>
</nav>
