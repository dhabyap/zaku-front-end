@extends('layouts.app')

@section('content')
<div x-data="insightsPage" style="display:flex;flex-direction:column;height:100%;">
    <header class="dash-header">
        <div class="dh-row">
            <div>
                <div class="dh-greet">INSIGHTS</div>
                <div class="dh-name">Analisis Pengeluaran</div>
            </div>
            <a href="/dashboard" class="dh-avatar" style="text-decoration:none;font-size:18px;">←</a>
        </div>
    </header>

    <div class="screen-body" style="padding-bottom:100px;">
        {{-- Loading --}}
        <template x-if="loading">
            <div style="padding:16px;">
                <div class="budget-card-skeleton" style="margin-bottom:12px;" x-repeat="3">
                    <div style="display:flex;gap:10px;align-items:center;">
                        <div style="width:40px;height:40px;border-radius:10px;background:rgba(17,16,16,.08);"></div>
                        <div style="flex:1;">
                            <div style="width:70%;height:12px;background:rgba(17,16,16,.08);border-radius:4px;margin-bottom:6px;"></div>
                            <div style="width:90%;height:10px;background:rgba(17,16,16,.06);border-radius:4px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- Empty state --}}
        <template x-if="!loading && insights.length === 0">
            <div class="budget-empty-state">
                <div style="font-size:48px;margin-bottom:16px;">💡</div>
                <div style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:var(--ink);margin-bottom:8px;">Belum ada insight</div>
                <div style="font-family:'DM Mono',monospace;font-size:12px;color:rgba(17,16,16,.5);margin-bottom:20px;">Catat transaksi dan atur budget agar Zaku bisa memberi analisis.</div>
                <button class="btn-budget-add" @click="refresh()">↻ MUAT ULANG</button>
            </div>
        </template>

        {{-- Insights list --}}
        <template x-if="!loading && insights.length > 0">
            <div style="padding:16px;">
                <button class="btn-budget-add" @click="refresh()" style="margin-bottom:16px;">↻ MUAT ULANG</button>

                <template x-for="(ins, idx) in insights" :key="idx">
                    <div class="insight-card" :class="'sev-' + (ins.severity || 'info')">
                        <div class="insight-card-icon" x-text="iconFor(ins)"></div>
                        <div class="insight-card-body">
                            <div class="insight-card-title" x-text="ins.title || 'Insight'"></div>
                            <div class="insight-card-msg" x-text="ins.message || ''"></div>
                            <div class="insight-card-meta" x-show="ins.related_category" x-text="'Kategori: ' + ins.related_category"></div>
                        </div>
                        <div class="insight-card-badge" x-text="badgeFor(ins.severity)"></div>
                    </div>
                </template>
            </div>
        </template>
    </div>
</div>
@endsection
