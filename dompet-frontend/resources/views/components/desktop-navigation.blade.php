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
    </nav>
</aside>
