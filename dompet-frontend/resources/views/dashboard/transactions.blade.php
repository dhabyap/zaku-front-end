@extends('layouts.app')

@section('content')
<div x-data="transactionList()" class="shell">
  <div class="hist-header">
    <div class="hist-top">
      <div>
        <div class="hist-title"><strong>Riwayat</strong><span class="hist-title-dot">.</span></div>
        <div class="hist-sub">SEMUA TRANSAKSI KAMU</div>
      </div>
      <!-- Removed hist-chips div -->
    </div>
    <div class="search-wrap">
      <input class="search-input" type="text" placeholder="Cari nama atau kategori..." @input="doSearch($event.target.value)">
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
    <div style="display:flex;align-items:center;gap:8px;padding:0 16px 8px;font-family:var(--font-mono);font-size:10px;">
      <span style="letter-spacing:1.5px;color:rgba(17,16,16,.4);">TANGGAL</span>
      <input type="date" :value="dateFrom" @change="setDateRange($event.target.value, dateTo)"
        style="font-family:var(--font-mono);font-size:11px;padding:5px 8px;border:1.5px solid var(--border-color, rgba(17,16,16,.15));background:var(--paper);color:var(--ink);cursor:pointer;">
      <span style="color:rgba(17,16,16,.3);">—</span>
      <input type="date" :value="dateTo" @change="setDateRange(dateFrom, $event.target.value)"
        style="font-family:var(--font-mono);font-size:11px;padding:5px 8px;border:1.5px solid var(--border-color, rgba(17,16,16,.15));background:var(--paper);color:var(--ink);cursor:pointer;">
      <button x-show="dateFrom || dateTo" @click="setDateRange('', '')"
        style="font-family:var(--font-mono);font-size:9px;letter-spacing:1px;color:var(--punch);background:none;border:none;cursor:pointer;padding:4px 6px;">RESET</button>
    </div>
  </div>

  <div class="sort-bar">
    <div class="sort-left">TOTAL <span class="sort-num" id="total-count" x-text="total"></span> TRANSAKSI</div>
    <div class="sort-right">
      <span class="sort-label">URUTKAN</span>
      <button class="sort-btn" :class="{'active': sortKey === 'date'}" @click="setSort('date', $el)" x-text="sortKey === 'date' ? (sortAsc ? '↑ TERLAMA' : '↓ TERBARU') : '↓ TERBARU'"></button>
      <button class="sort-btn" :class="{'active': sortKey === 'amount'}" @click="setSort('amount', $el)" x-text="sortKey === 'amount' ? (sortAsc ? '↑ TERKECIL' : '↓ TERBESAR') : '↕ NOMINAL'"></button>
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
                        <div class="tx-row" :class="trx.type" @click="window.location.href = '/transactions/' + trx.id">
                            <div class="tx-ico"><span x-text="getEmoji(trx.category_name)"></span><div class="tx-ico-dot"></div></div>
                            <div class="tx-info">
                                <div class="tx-name" x-text="trx.description"></div>
                                <div class="tx-meta">
                                    <span class="tx-cat" x-text="trx.category_name"></span>
                                    <span class="tx-sep">·</span>
                                    <span class="tx-date" x-text="trx.date_formatted"></span>
                                </div>
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
    
    <!-- Load more button, integrated -->
    <div class="load-more-wrap" x-show="hasMore">
        <button class="load-more-btn" @click="loadMore" :disabled="loading" :class="{'loading': loading}">
        <span x-show="!loading">Muat Transaksi Lainnya</span>
        <span x-show="loading">Memuat...</span>
        </button>
    </div>
  </div>

  <div class="pagination">
    <div class="pag-info">
      HAL <strong x-text="currentPage"></strong> DARI <span x-text="lastPage || 1"></span>
    </div>
    <div class="pag-controls">
      <button class="pag-btn arrow" @click="loadPage(currentPage-1)" :disabled="currentPage<=1">‹</button>
      <template x-for="p in paginationNumbers()" :key="p">
        <template x-if="p === '...'">
          <span class="pag-dots">···</span>
        </template>
        <template x-if="p !== '...'">
          <button class="pag-btn" :class="{'on': p === currentPage}" @click="loadPage(p)" x-text="p"></button>
        </template>
      </template>
      <button class="pag-btn arrow" @click="loadPage(currentPage+1)" :disabled="currentPage>=lastPage">›</button>
    </div>
  </div>

  <div class="drawer-bg" id="drawer-bg" @click="closeDrw()" style="display:none;">
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
