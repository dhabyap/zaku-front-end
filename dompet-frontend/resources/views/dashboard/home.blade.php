@extends('layouts.app')

@section('content')
<div x-data="dashboardHome()" class="space-y-8 p-6 pb-24 max-w-md mx-auto">
    <!-- Balance Card -->
    <div class="bg-punch border-4 border-ink p-8 shadow-bs-lg relative overflow-hidden group">
        <p class="font-mono text-paper/80 uppercase text-xs font-bold tracking-[0.2em] mb-2">Total Saldo Zaku</p>
        
        <div class="relative z-10">
            <template x-if="loading.balance">
                <div class="h-12 w-2/3 bg-paper/20 animate-pulse border-2 border-paper/10"></div>
            </template>
            <template x-if="!loading.balance">
                <h2 class="text-5xl font-display font-black text-paper leading-none tracking-tighter" 
                    x-text="window.utils.formatRupiah(balance)">
                </h2>
            </template>
        </div>
        
        <!-- Abstract Brutalist shape -->
        <div class="absolute -bottom-6 -right-6 h-32 w-32 bg-ink opacity-10 rotate-12 group-hover:rotate-45 transition-transform duration-700"></div>
        <div class="absolute top-0 right-0 h-16 w-1 bg-paper/20"></div>
    </div>

    <!-- Quick Action Grid -->
    <div class="grid grid-cols-3 gap-4">
        <a href="/wallet/topup" class="bg-mint border-4 border-ink p-4 flex flex-col items-center justify-center gap-2 shadow-bs hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all active:scale-95">
            <div class="h-10 w-10 border-2 border-ink bg-paper/30 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-ink" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <span class="font-display font-black text-[10px] uppercase tracking-widest text-ink">Top Up</span>
        </a>

        <a href="/wallet/send" class="bg-punch-2 border-4 border-ink p-4 flex flex-col items-center justify-center gap-2 shadow-bs hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all active:scale-95">
            <div class="h-10 w-10 border-2 border-ink bg-paper/30 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-ink" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
            </div>
            <span class="font-display font-black text-[10px] uppercase tracking-widest text-ink">Kirim</span>
        </a>

        <a href="/wallet/withdraw" class="bg-sky border-4 border-ink p-4 flex flex-col items-center justify-center gap-2 shadow-bs hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all active:scale-95">
            <div class="h-10 w-10 border-2 border-ink bg-paper/30 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-ink" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <span class="font-display font-black text-[10px] uppercase tracking-widest text-ink">Tarik</span>
        </a>
    </div>

    <!-- Recent Activity Section -->
    <div class="space-y-4">
        <div class="flex justify-between items-baseline px-1">
            <h3 class="text-2xl font-display font-black text-ink uppercase tracking-tighter">Aktivitas</h3>
            <a href="/transactions" class="font-mono text-[10px] font-bold text-punch uppercase underline decoration-2 underline-offset-4">Lihat Semua</a>
        </div>

        <div class="space-y-4">
            <!-- Loading State -->
            <template x-if="loading.transactions">
                <div>
                    <x-loading-skeleton count="3" />
                </div>
            </template>

            <!-- Data List -->
            <template x-if="!loading.transactions">
                <div class="space-y-4">
                    <template x-for="trx in transactions" :key="trx.id">
                        <div class="bg-paper border-4 border-ink p-4 flex items-center gap-4 shadow-bs hover:bg-cream transition-colors">
                            <div :class="trx.type === 'expense' ? 'bg-punch' : 'bg-mint'" 
                                class="h-14 w-14 border-4 border-ink flex items-center justify-center flex-shrink-0">
                                <template x-if="trx.type === 'expense'">
                                    <svg class="h-6 w-6 text-paper" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                        <path d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                    </svg>
                                </template>
                                <template x-if="trx.type === 'income'">
                                    <svg class="h-6 w-6 text-ink" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                        <path d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                    </svg>
                                </template>
                            </div>
                            
                            <div class="flex-grow overflow-hidden">
                                <p class="font-display font-black text-ink uppercase text-sm leading-tight truncate" x-text="trx.description"></p>
                                <p class="font-mono text-[10px] text-ink/50 font-bold uppercase tracking-widest" x-text="window.utils.formatDate(trx.created_at)"></p>
                            </div>
                            
                            <div class="text-right flex-shrink-0">
                                <p :class="trx.type === 'expense' ? 'text-punch' : 'text-mint'" 
                                    class="font-mono font-black text-base leading-none" 
                                    x-text="(trx.type === 'expense' ? '-' : '+') + window.utils.formatRupiah(trx.amount)">
                                </p>
                            </div>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <template x-if="transactions.length === 0">
                        <div class="border-4 border-ink border-dashed p-10 text-center bg-cream/20">
                            <p class="font-mono text-sm text-ink/30 uppercase font-bold tracking-widest">Belum ada riwayat</p>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    function dashboardHome() {
        return {
            balance: 0,
            transactions: [],
            loading: {
                balance: true,
                transactions: true
            },
            async init() {
                this.fetchBalance();
                this.fetchTransactions();
            },
            async fetchBalance() {
                try {
                    const res = await window.apiClient.get('/wallet/balance');
                    this.balance = res.data.data.balance || 0;
                } catch (e) {
                    console.error('Fetch balance error:', e);
                } finally {
                    this.loading.balance = false;
                }
            },
            async fetchTransactions() {
                try {
                    const res = await window.apiClient.get('/transactions?limit=5');
                    this.transactions = res.data.data || [];
                } catch (e) {
                    console.error('Fetch transactions error:', e);
                } finally {
                    this.loading.transactions = false;
                }
            }
        }
    }
</script>
@endsection
