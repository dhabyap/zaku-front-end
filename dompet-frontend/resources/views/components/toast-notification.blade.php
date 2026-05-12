<div 
    x-data="{ 
        show: false, 
        type: 'info', 
        message: '',
        lines: [],
        persistent: false,
        timeout: null,
        init() {
            window.addEventListener('show-toast', (e) => {
                this.type = e.detail.type;
                this.message = e.detail.message;
                this.persistent = e.detail.persistent ?? (e.detail.type === 'error');
                // Split message by newline for multi-line support
                this.lines = String(e.detail.message).split('\n').filter(l => l.trim() !== '');
                this.show = true;

                if (this.timeout) clearTimeout(this.timeout);

                // Only auto-hide if not persistent
                if (!this.persistent) {
                    this.timeout = setTimeout(() => {
                        this.show = false;
                    }, 3000);
                }
            });
        }
    }"
    x-show="show"
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-8"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-8"
    class="fixed top-4 left-1/2 -translate-x-1/2 z-[100] w-full max-w-[92%] sm:max-w-md pointer-events-none"
    style="display: none;"
>
    <div 
        :class="{
            'bg-mint': type === 'success',
            'bg-punch': type === 'error',
            'bg-sky': type === 'info'
        }"
        class="border-4 border-ink shadow-bs pointer-events-auto"
    >
        <!-- Header row: icon + close button -->
        <div class="flex items-center gap-3 p-4 pb-2">
            <!-- Icon -->
            <div class="flex-shrink-0">
                <template x-if="type === 'success'">
                    <svg class="h-6 w-6 text-ink" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </template>
                <template x-if="type === 'error'">
                    <svg class="h-6 w-6 text-ink" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </template>
                <template x-if="type === 'info'">
                    <svg class="h-6 w-6 text-ink" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </template>
            </div>

            <!-- Title -->
            <span class="flex-grow font-display font-black text-ink uppercase tracking-tight text-base leading-none"
                x-text="type === 'error' ? 'Ada Kesalahan' : (type === 'success' ? 'Berhasil' : 'Informasi')">
            </span>

            <!-- Close button — always visible on persistent toasts -->
            <button @click="show = false; if(timeout) clearTimeout(timeout);"
                class="flex-shrink-0 text-ink hover:scale-125 transition-transform active:scale-95"
                title="Tutup">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Message body: single line or multi-line list -->
        <div class="px-4 pb-4 pt-1">
            <!-- Single line -->
            <template x-if="lines.length === 1">
                <p class="font-mono text-sm text-ink leading-snug" x-text="lines[0]"></p>
            </template>
            <!-- Multi-line -->
            <template x-if="lines.length > 1">
                <ul class="space-y-1">
                    <template x-for="(line, idx) in lines" :key="idx">
                        <li class="font-mono text-sm text-ink leading-snug" x-text="line"></li>
                    </template>
                </ul>
            </template>
        </div>

        <!-- Persistent indicator: show "Tap X untuk tutup" only for error -->
        <template x-if="persistent">
            <div class="border-t-2 border-ink/30 px-4 py-2">
                <p class="font-mono text-[10px] text-ink/60 uppercase tracking-widest">Tap × untuk menutup notifikasi</p>
            </div>
        </template>
    </div>
</div>
