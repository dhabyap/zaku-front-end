@extends('layouts.app')

@section('content')
<div x-data="changelogPage()" style="display:flex;flex-direction:column;height:100%;">
    <div class="inner-top" style="display:flex;align-items:center;gap:12px;">
        <a href="/profile" class="back-btn" style="border-color:rgba(255,255,255,.2);color:var(--paper);text-decoration:none;">←</a>
        <div>
            <div class="inner-title" style="font-size:24px;">Update Zaku</div>
            <div class="inner-sub">CHANGELOG & PERBAIKAN</div>
        </div>
    </div>

    <div class="screen-body" style="padding-bottom:90px;">
        <template x-if="loading">
            <div style="padding:16px;">
                <x-loading-skeleton count="4" />
            </div>
        </template>

        <template x-if="!loading && logs.length === 0">
            <div style="padding:40px 16px;text-align:center;">
                <div style="font-size:48px;margin-bottom:12px;">📋</div>
                <span style="font-family:var(--font-mono);font-size:10px;color:rgba(17,16,16,.4);">BELUM ADA UPDATE</span>
                <div style="margin-top:8px;font-size:12px;color:rgba(17,16,16,.3);">Pantau terus halaman ini untuk info update terbaru Zaku</div>
            </div>
        </template>

        <template x-if="!loading && logs.length > 0">
            <div style="padding:8px 16px;font-family:var(--font-mono);font-size:9px;color:rgba(17,16,16,.35);letter-spacing:1px;">
                MENAMPILKAN <span x-text="logs.length"></span> DARI <span x-text="total"></span> UPDATE
            </div>
        </template>

        <template x-for="log in logs" :key="log.id">
            <div style="margin:12px 16px;background:#fff;border:var(--border);box-shadow:var(--bs-lg);padding:16px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                    <span style="background:var(--ink);color:var(--paper);font-family:var(--font-mono);font-size:9px;padding:3px 8px;letter-spacing:1px;" x-text="log.version || 'v1.0'"></span>
                    <span style="font-family:var(--font-mono);font-size:9px;color:rgba(17,16,16,.4);" x-text="formatDate(log.created_at)"></span>
                    <span :style="{
                        background: log.status === 'solved' ? 'var(--mint)' : 'var(--punch)',
                        color: log.status === 'solved' ? 'var(--ink)' : 'var(--paper)',
                        fontFamily: 'var(--font-mono)', fontSize: '9px', padding: '3px 8px', letterSpacing: '1px'
                    }" x-text="log.status === 'solved' ? '✓ SOLVED' : '◷ PENDING'"></span>
                </div>
                <div style="font-size:16px;font-weight:800;color:var(--ink);margin-bottom:4px;" x-text="log.title"></div>
                <div style="font-size:12px;color:rgba(17,16,16,.6);margin-bottom:8px;" x-text="log.description || ''"></div>
                <div style="font-family:var(--font-mono);font-size:9px;color:rgba(17,16,16,.4);">
                    Oleh <span style="font-weight:500;color:var(--ink);" x-text="log.author"></span>
                </div>
                <template x-if="log.issues && log.issues.length > 0">
                    <div style="margin-top:8px;padding-top:8px;border-top:1px solid rgba(17,16,16,.08);">
                        <div style="font-family:var(--font-mono);font-size:8px;letter-spacing:2px;color:rgba(17,16,16,.4);margin-bottom:4px;">ISSUES</div>
                        <template x-for="issue in log.issues" :key="issue">
                            <div style="font-size:11px;color:rgba(17,16,16,.6);padding:2px 0;">— <span x-text="issue"></span></div>
                        </template>
                    </div>
                </template>
            </div>
        </template>

        <template x-if="!loading && currentPage < lastPage">
            <div style="padding:16px;text-align:center;">
                <button @click="loadMore()" :disabled="loadingMore"
                    style="width:100%;background:var(--paper);color:var(--ink);border:var(--border);box-shadow:var(--bs);padding:14px;font-family:var(--font-display);font-size:13px;font-weight:700;letter-spacing:1px;cursor:pointer;transition:transform .1s,box-shadow .1s;"
                    :style="loadingMore ? 'opacity:.5;cursor:wait' : ''">
                    <span x-show="!loadingMore">MUAT LAINNYA →</span>
                    <span x-show="loadingMore">MEMUAT...</span>
                </button>
            </div>
        </template>

        <template x-if="!loading && currentPage >= lastPage && logs.length > 0">
            <div style="padding:16px;text-align:center;font-family:var(--font-mono);font-size:9px;color:rgba(17,16,16,.3);letter-spacing:1px;">
                SEMUA DATA SUDAH DIMUAT
            </div>
        </template>
    </div>
</div>
@endsection
