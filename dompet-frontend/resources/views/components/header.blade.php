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
            <div class="dh-greet" x-text="greeting()"></div>
            <div class="dh-name" x-text="user?.name || 'Teman'"></div>
        </div>
        <a href="/profile" class="dh-avatar" x-text="user?.name?.charAt(0).toUpperCase() || '?'"></a>
    </div>
    <link rel="icon" type="image/svg+xml" href="build/assets/zaku-favicon.svg">
</header>

<script>
    function greeting() {
        const h = new Date().getHours();
        let g = 'SELAMAT MALAM 🌙';
        if (h < 12) g = 'SELAMAT PAGI ☀️';
        else if (h < 15) g = 'SELAMAT SIANG 🌤️';
        else if (h < 18) g = 'SELAMAT SORE 🌅';
        return g;
    }
</script>
