@extends('layouts.app')

@section('content')
<div x-data="transactionList()" class="shell" @keydown.escape.window="if (detailOpen) closeDetail()">
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
    <div class="data-state data-state-error" x-show="!loading && error" role="alert" aria-live="assertive">
      <p x-text="error"></p>
      <button type="button" @click="retryTransactions()" :disabled="loading || loadingMore">Coba lagi</button>
    </div>
    <div class="data-state-refresh" x-show="loading && hasLoaded" role="status" aria-live="polite">Memperbarui transaksi…</div>
    <template x-if="loading && !hasLoaded">
        <div style="padding:20px;" role="status" aria-live="polite" aria-label="Memuat transaksi">
            <x-loading-skeleton count="5" />
        </div>
    </template>
    <div class="data-state data-state-empty" x-show="hasLoaded && transactions.length === 0 && !error" role="status" aria-live="polite">
      <p>Belum ada transaksi untuk ditampilkan.</p>
    </div>
    <template x-if="transactions.length > 0">
        <div class="tx-rows">
            <template x-for="(group, month) in grouped()" :key="month">
                <div class="month-group">
                    <div class="month-label-row">
                        <div class="month-label-text" x-text="month"></div>
                        <div class="month-total" x-text="'−Rp ' + formatNumber(group.reduce((s,t) => s + (t.type=='expense' ? t.amount : 0), 0))"></div>
                    </div>
                    <template x-for="trx in group" :key="trx.id">
                        <div class="tx-row" :class="trx.type" @click="openDetail(trx, $event.currentTarget)" @keydown.enter.prevent="openDetail(trx, $event.currentTarget)" @keydown.space.prevent="openDetail(trx, $event.currentTarget)" role="button" tabindex="0" :aria-label="'Lihat detail transaksi ' + trx.description" :aria-haspopup="'dialog'" :aria-expanded="detailOpen && detailTarget?.id === trx.id">
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
                                <div class="tx-amount" x-text="(trx.type=='income'?'+':'−') + 'Rp ' + formatNumber(trx.amount)"></div>
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
        <button class="load-more-btn" type="button" @click="loadMore" :disabled="loading || loadingMore" :class="{'loading': loadingMore}">
        <span x-show="!loadingMore">Muat Transaksi Lainnya</span>
        <span x-show="loadingMore">Memuat...</span>
        </button>
    </div>
  </div>

  <div class="pagination">
    <div class="pag-info">
      HAL <strong x-text="currentPage"></strong> DARI <span x-text="lastPage || 1"></span>
    </div>
    <div class="pag-controls">
      <button class="pag-btn arrow" @click="loadPage(currentPage-1)" :disabled="loading || currentPage<=1">‹</button>
      <template x-for="p in paginationNumbers()" :key="p">
        <template x-if="p === '...'">
          <span class="pag-dots">···</span>
        </template>
        <template x-if="p !== '...'">
          <button class="pag-btn" :class="{'on': p === currentPage}" @click="loadPage(p)" :disabled="loading" x-text="p"></button>
        </template>
      </template>
      <button class="pag-btn arrow" @click="loadPage(currentPage+1)" :disabled="loading || currentPage>=lastPage">›</button>
    </div>
  </div>

  <template x-teleport="body">
    <div class="drawer-bg" id="drawer-bg" x-show="detailOpen" :class="{ 'open': detailOpen }" x-transition.opacity @click.self="closeDetail()" role="presentation">
      <section class="drawer" role="dialog" aria-modal="true" aria-labelledby="transaction-detail-title" @click.stop>
        <div class="drawer-handle" aria-hidden="true"></div>
        <div class="drawer-head" :class="activeTrx?.type">
          <div class="drawer-ico" x-text="activeTrx?.category_icon || getEmoji(activeTrx?.category_name)"></div>
          <div class="drawer-heading">
            <h2 class="drawer-name" id="transaction-detail-title" x-text="activeTrx?.description || (detailLoading ? 'Memuat detail transaksi…' : 'Detail transaksi')"></h2>
            <div class="drawer-cat" x-show="activeTrx" x-text="((activeTrx?.category_name || activeTrx?.category || 'UMUM').toUpperCase()) + ' · ' + ((activeTrx?.type || '').toUpperCase())"></div>
          </div>
          <button class="drawer-close-icon" id="transaction-detail-close" type="button" aria-label="Tutup detail transaksi" @click="closeDetail()">×</button>
        </div>

        <div class="drawer-content" aria-live="polite">
          <div class="drawer-loading" x-show="detailLoading" role="status">Memuat detail transaksi…</div>
          <div class="drawer-error" x-show="!detailLoading && detailError" role="alert">
            <p x-text="detailError"></p>
            <button class="d-btn" type="button" @click="retryDetail()">Coba lagi</button>
          </div>
          <template x-if="!detailLoading && activeTrx">
            <div class="drawer-details">
              <div class="drawer-amount-card" :class="activeTrx.type">
                <div class="drow-k">NOMINAL TRANSAKSI</div>
                <div class="drawer-amt" x-text="(activeTrx.type === 'income' ? '+' : '−') + 'Rp ' + formatNumber(activeTrx.amount)"></div>
              </div>
              <div class="drawer-rows">
                <div class="drow">
                  <div class="drow-k">KATEGORI</div>
                  <div class="drow-v" x-text="(activeTrx.category_icon || '') + ' ' + (activeTrx.category_name || activeTrx.category || 'UMUM')"></div>
                </div>
                <div class="drow">
                  <div class="drow-k">TIPE</div>
                  <div class="drow-v" x-text="activeTrx.type === 'income' ? 'Pemasukan' : 'Pengeluaran'"></div>
                </div>
                <div class="drow">
                  <div class="drow-k">TANGGAL & WAKTU</div>
                  <div class="drow-v" x-text="formatTransactionDate(activeTrx)"></div>
                </div>
                <div class="drow" x-show="activeTrx.source">
                  <div class="drow-k">SUMBER</div>
                  <div class="drow-v" x-text="activeTrx.source"></div>
                </div>
                <div class="drow drow-note" x-show="activeTrx.note || activeTrx.notes">
                  <div class="drow-k">CATATAN</div>
                  <div class="drow-v" x-text="activeTrx.note || activeTrx.notes"></div>
                </div>
              </div>
            </div>
          </template>
        </div>
        <div class="drawer-actions">
          <button class="d-btn edit-action" type="button" @click="window.location.href = '/transactions/' + encodeURIComponent(activeTrx.id)" :disabled="!activeTrx" aria-label="Buka halaman detail untuk mengedit transaksi">✎ EDIT</button>
          <button class="d-btn danger" type="button" @click="closeDetail()">TUTUP</button>
        </div>
      </section>
    </div>
  </template>
</div>
@endsection
