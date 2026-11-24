<aside class="desktop-navigation">
    <a class="desktop-nav-brand" href="/dashboard" aria-label="Zaku, Beranda">ZAKU<span>.</span></a>

    <nav aria-label="Navigasi utama">
        <section class="desktop-nav-group" aria-labelledby="desktop-nav-main">
            <h2 id="desktop-nav-main">UTAMA</h2>
            <a href="/dashboard" class="desktop-nav-link {{ request()->is('dashboard') ? 'active' : '' }}" @if(request()->is('dashboard')) aria-current="page" @endif>Beranda</a>
            <a href="/transactions" class="desktop-nav-link {{ request()->is('transactions*') ? 'active' : '' }}" @if(request()->is('transactions*')) aria-current="page" @endif>Riwayat</a>
            <a href="/calendar" class="desktop-nav-link {{ request()->is('calendar') ? 'active' : '' }}" @if(request()->is('calendar')) aria-current="page" @endif>Kalender</a>
            <a href="/chat" class="desktop-nav-link {{ request()->is('chat') ? 'active' : '' }}" @if(request()->is('chat')) aria-current="page" @endif>AI Chat</a>
        </section>

        <section class="desktop-nav-group" aria-labelledby="desktop-nav-finance">
            <h2 id="desktop-nav-finance">KEUANGAN</h2>
            <a href="/budgets" class="desktop-nav-link {{ request()->is('budgets') ? 'active' : '' }}" @if(request()->is('budgets')) aria-current="page" @endif>Budget</a>
            <a href="/recurring" class="desktop-nav-link {{ request()->is('recurring') ? 'active' : '' }}" @if(request()->is('recurring')) aria-current="page" @endif>Transaksi Berulang</a>
            <a href="/insights" class="desktop-nav-link {{ request()->is('insights') ? 'active' : '' }}" @if(request()->is('insights')) aria-current="page" @endif>Insights</a>
            <a href="/categories" class="desktop-nav-link {{ request()->is('categories') ? 'active' : '' }}" @if(request()->is('categories')) aria-current="page" @endif>Kategori</a>
            <a href="/monthly-recap" class="desktop-nav-link {{ request()->is('monthly-recap') ? 'active' : '' }}" @if(request()->is('monthly-recap')) aria-current="page" @endif>Rekap Bulanan</a>
            <a href="/manual-input" class="desktop-nav-link {{ request()->is('manual-input') ? 'active' : '' }}" @if(request()->is('manual-input')) aria-current="page" @endif>Input Manual</a>
        </section>

        <section class="desktop-nav-group" aria-labelledby="desktop-nav-account">
            <h2 id="desktop-nav-account">AKUN</h2>
            <a href="/profile" class="desktop-nav-link {{ request()->is('profile') ? 'active' : '' }}" @if(request()->is('profile')) aria-current="page" @endif>Profil</a>
            <a href="/settings" class="desktop-nav-link {{ request()->is('settings') ? 'active' : '' }}" @if(request()->is('settings')) aria-current="page" @endif>Settings</a>
            <a href="/changelogs" class="desktop-nav-link {{ request()->is('changelogs') ? 'active' : '' }}" @if(request()->is('changelogs')) aria-current="page" @endif>Changelog</a>
        </section>
        <section class="desktop-nav-group" aria-labelledby="desktop-nav-theme">
            <h2 id="desktop-nav-theme">TEMA</h2>
            <button type="button" onclick="(function(){
                const cur = localStorage.getItem('zaku_theme') === 'dark' ? 'dark' : 'light';
                const next = cur === 'dark' ? 'light' : 'dark';
                localStorage.setItem('zaku_theme', next);
                if (next === 'dark') {
                    document.documentElement.setAttribute('data-theme', 'dark');
                } else {
                    document.documentElement.removeAttribute('data-theme');
                }
                const lbl = document.getElementById('desktop-theme-label');
                if (lbl) lbl.textContent = next === 'dark' ? '☀️ Ganti ke Light' : '🌙 Ganti ke Dark';
                if (window.utils && window.utils.showToast) {
                    window.utils.showToast('Tema diubah ke ' + (next === 'dark' ? 'Cyber Dark 🌙' : 'Light ☀️'), 'success');
                }
            })()" style="background:var(--cream);border:1.5px solid var(--ink);width:100%;padding:8px 12px;font-family:var(--font-mono);font-size:11px;font-weight:700;text-align:left;cursor:pointer;display:flex;align-items:center;gap:8px;">
                <span id="desktop-theme-label">🌙 / ☀️ Toggle Tema</span>
            </button>
        </section>
    </nav>
</aside>
