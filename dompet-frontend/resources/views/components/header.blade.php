<header class="dash-header" x-data="{
    user: window.auth.getUser(),
    init() {
        window.addEventListener('storage', () => {
            this.user = window.auth.getUser();
        });
    }
}">
    <div class="dh-row">
        <div>
            <div class="dh-greet" x-text="window.utils.greeting()"></div>
            <div class="dh-name" x-text="user?.name || 'Teman'"></div>
        </div>
        <a href="/profile" class="dh-avatar" aria-label="Buka Profil" title="Profil" x-text="user?.name?.charAt(0).toUpperCase() || '?'"></a>
    </div>
    <link rel="icon" type="image/svg+xml" href="build/assets/zaku-favicon.svg">
</header>
