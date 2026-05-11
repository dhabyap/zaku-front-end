<header 
    x-data="{ 
        user: window.auth.getUser(),
        init() {
            // Re-sync if user data changes
            window.addEventListener('storage', () => {
                this.user = window.auth.getUser();
            });
        }
    }" 
    class="bg-paper border-b-4 border-ink p-6 pt-8 sticky top-0 z-40"
>
    <div class="max-w-md mx-auto flex justify-between items-start">
        <div class="flex-grow">
            <p class="text-[10px] font-mono font-bold uppercase tracking-[0.2em] text-ink/60 mb-1">
                Overview &bull; <span x-text="new Date().toLocaleDateString('id-ID', { weekday: 'long' })"></span>
            </p>
            <h1 class="text-3xl font-display font-black text-ink leading-none">
                Halo, <span x-text="user?.name?.split(' ')[0] || 'Teman'"></span>!
            </h1>
        </div>
        
        <!-- Profile Avatar Placeholder -->
        <a href="/profile" class="group relative">
            <div class="h-12 w-12 bg-punch-2 border-4 border-ink shadow-bs group-hover:translate-x-0.5 group-hover:translate-y-0.5 group-hover:shadow-none transition-all"></div>
            <div class="absolute -top-1 -right-1 h-4 w-4 bg-mint border-2 border-ink rounded-full"></div>
        </a>
    </div>
</header>
