@extends('layouts.app')

@section('content')
<div x-data="transactionList()" class="shell">
  <div class="hist-header">
    <div class="hist-top">
      <div>
        <div class="hist-title"><strong>Riwayat</strong><span class="hist-title-dot">.</span></div>
        <div class="hist-sub">SEMUA TRANSAKSI KAMU</div>
      </div>
      <div class="hist-chips">
        <div class="hchip inc"><div class="hchip-dot"></div><span x-text="rp(transactions.filter(t=>t.type=='income').reduce((s,t)=>s+t.amount,0))"></span></div>
        <div class="hchip exp"><div class="hchip-dot"></div>−<span x-text="rp(transactions.filter(t=>t.type=='expense').reduce((s,t)=>s+t.amount,0))"></span></div>
      </div>
    </div>
    <div class="search-wrap">
      <input class="search-input" type="text" placeholder="Cari nama atau kategori..." @input.debounce="doSearch($event.target.value)">
      <div class="search-ico">⌕</div>
    </div>
    <div class="filter-strip">
      <button class="fpill on" @click="setFilter('all', $el)">SEMUA</button>
      <button class="fpill" @click="setFilter('income', $el)">↑ MASUK</button>
      <button class="fpill" @click="setFilter('expense', $el)">↓ KELUAR</button>
      <template x-for="cat in categories" :key="cat">
        <button class="fpill" @click="setFilter(cat, $el)" x-text="cat"></button>
      </template>
    </div>
  </div>

  <div class="sort-bar">
    <div class="sort-left">TOTAL <span class="sort-num" id="total-count" x-text="filtered().length"></span> TRANSAKSI</div>
    <div class="sort-right">
      <span class="sort-label">URUTKAN</span>
      <button class="sort-btn active" @click="setSort('date', $el)">↓ TERBARU</button>
      <button class="sort-btn" @click="setSort('amount', $el)">↕ NOMINAL</button>
    </div>
  </div>

  <div class="tx-body" id="tx-body">
    <template x-if="loading">
        <div style="padding:20px;">
            <x-loading-skeleton count="5" />
        </div>
    </template>
    <template x-if="!loading">
        <div class="tx-rows">
            <template x-for="(group, month) in grouped()" :key="month">
                <div class="month-group">
                    <div class="month-label-row">
                        <div class="month-label-text" x-text="month"></div>
                        <div class="month-total" x-text="'−Rp ' + formatNumber(group.reduce((s,t) => s + (t.type=='expense' ? t.amount : 0), 0))"></div>
                    </div>
                    <template x-for="trx in group" :key="trx.id">
                        <div class="tx-row" :class="trx.type" @click="openDrw(trx)">
                            <div class="tx-ico"><span x-text="getEmoji(trx.category_name)"></span><div class="tx-ico-dot"></div></div>
                            <div class="tx-info">
                                <div class="tx-name" x-text="trx.description"></div>
                                <div class="tx-meta"><span class="tx-cat" x-text="trx.category_name"></span><span class="tx-sep">·</span><span class="tx-date" x-text="transactionDay(trx)"></span></div>
                            </div>
                            <div class="tx-right">
                                <div class="tx-amount" x-text="(trx.type=='income'?'+':'−') + formatNumber(trx.amount)"></div>
                                <template x-if="trx.tag"><span class="tx-tag" :class="trx.tag" x-text="trx.tag.toUpperCase()"></span></template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </template>
  </div>

  <div class="pagination">
    <div class="pag-info">HAL <strong x-text="currentPage"></strong> DARI <span x-text="lastPage || 1"></span></div>
    <div class="pag-controls">
      <button class="pag-btn arrow" @click="loadPage(currentPage-1)" :disabled="currentPage<=1">‹</button>
      <button class="pag-btn arrow" @click="loadPage(currentPage+1)" :disabled="!hasMore">›</button>
    </div>
  </div>

  <div class="drawer-bg" id="drawer-bg" @click="closeDrw()">
    <div class="drawer">
      <div class="drawer-handle"></div>
      <div class="drawer-head" :class="activeTrx?.type" id="drawer-head">
        <div class="drawer-ico" x-text="activeTrx?.category_icon || '📌'"></div>
        <div>
          <div class="drawer-name" x-text="activeTrx?.description"></div>
          <div class="drawer-cat" x-text="(activeTrx?.category_name||'').toUpperCase() + ' · ' + (activeTrx?.type||'').toUpperCase()"></div>
        </div>
        <div class="drawer-amt" x-text="(activeTrx?.type=='income'?'+':'−') + formatNumber(activeTrx?.amount||0)"></div>
      </div>
      <div class="drawer-rows">
        <div class="drow"><div class="drow-k">NOMINAL</div><div class="drow-v" x-text="(activeTrx?.type=='income'?'+':'−') + formatNumber(activeTrx?.amount||0)"></div></div>
        <div class="drow"><div class="drow-k">TANGGAL</div><div class="drow-v" x-text="transactionDay(activeTrx)"></div></div>
      </div>
      <div class="drawer-ai">
        <div class="dai-badge">AI</div>
        <div class="dai-text" x-text="getAiHint(activeTrx?.category_name)"></div>
      </div>
      <div class="drawer-actions">
        <button class="d-btn" @click="showToast('Edit segera hadir!')">✎ EDIT</button>
        <button class="d-btn danger" @click="closeDrw()">✕ TUTUP</button>
      </div>
    </div>
  </div>
</div>
@endsection
