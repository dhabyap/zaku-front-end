<header class="dash-header" x-data="{
    user: window.auth.getUser(),
    dark: document.documentElement.classList.contains('dark'),
    toggleTheme() {
        this.dark = !this.dark;
        document.documentElement.classList.toggle('dark', this.dark);
        localStorage.setItem('zaku_theme', this.dark ? 'dark' : 'light');
    },
    greeting() {
        const h = new Date().getHours();
        if (h < 12) return 'SELAMAT PAGI ☀️';
        if (h < 15) return 'SELAMAT SIANG 🌤️';
        if (h < 18) return 'SELAMAT SORE 🌅';
        return 'SELAMAT MALAM 🌙';
    },
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
        <div style="display:flex;align-items:center;gap:8px;">
            <button @click="toggleTheme()" style="background:none;border:none;font-size:20px;cursor:pointer;padding:4px;line-height:1;" x-text="dark ? '☀️' : '🌙'" :title="dark ? 'Mode Terang' : 'Mode Gelap'"></button>
            <a href="/profile" class="dh-avatar" x-text="user?.name?.charAt(0).toUpperCase() || '?'"></a>
        </div>
    </div>
</header>
