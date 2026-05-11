<div 
    x-data="{ 
        show: false, 
        type: 'info', 
        message: '',
        timeout: null,
        init() {
            window.addEventListener('show-toast', (e) => {
                this.type = e.detail.type;
                this.message = e.detail.message;
                this.show = true;
                
                if (this.timeout) clearTimeout(this.timeout);
                
                this.timeout = setTimeout(() => { 
                    this.show = false;
                }, 3000);
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
    class="fixed top-8 left-1/2 -translate-x-1/2 z-[100] w-full max-w-[90%] sm:max-w-md pointer-events-none"
    style="display: none;"
>
    <div 
        :class="{
            'bg-mint': type === 'success',
            'bg-punch': type === 'error',
            'bg-sky': type === 'info'
        }"
        class="border-4 border-ink p-4 shadow-bs flex items-center gap-4 pointer-events-auto"
    >
        <!-- Icon based on type -->
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

        <div class="flex-grow">
            <p class="font-display font-black text-ink uppercase tracking-tight leading-none" x-text="message"></p>
        </div>

        <button @click="show = false" class="flex-shrink-0 text-ink hover:scale-125 transition-transform active:scale-95">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>
