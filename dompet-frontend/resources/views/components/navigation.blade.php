<nav class="bottom-nav" id="bottom-nav">
    <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}" id="nav-dashboard">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        <span>BERANDA</span>
    </a>
    <a href="/transactions" class="nav-item {{ request()->is('transactions*') ? 'active' : '' }}" id="nav-history">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        <span>RIWAYAT</span>
    </a>
    <a href="/chat" class="nav-item chat-fab-btn {{ request()->is('chat') ? 'active' : '' }}" id="nav-chat">
        <div class="chat-fab-inner">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </div>
        <span>AI CHAT</span>
    </a>
    <a href="/profile" class="nav-item {{ request()->is('profile') ? 'active' : '' }}" id="nav-profile">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span>PROFIL</span>
    </a>
</nav>
