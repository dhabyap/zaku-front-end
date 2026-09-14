@extends('layouts.app')

@section('content')
<div x-data="calendarPage" style="display:flex;flex-direction:column;height:100%;">
    {{-- Header --}}
    <header class="dash-header">
        <div class="dh-row">
            <div>
                <div class="dh-greet">KALENDER</div>
                <div class="dh-name" x-text="getMonthName(currentMonth) + ' ' + currentYear"></div>
            </div>
            <a href="/dashboard" class="dh-avatar" style="text-decoration:none;font-size:18px;">←</a>
        </div>
    </header>

    <div class="screen-body" style="padding-bottom:100px;">
        {{-- Navigasi Bulan --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;">
            <button @click="prevMonth()" style="background:var(--ink);color:var(--paper);border:none;padding:8px 14px;font-family:var(--font-mono);font-size:11px;letter-spacing:1px;cursor:pointer;box-shadow:var(--bs);">← SEBELUMNYA</button>
            <button @click="goToToday()" style="background:var(--punch);color:var(--paper);border:none;padding:8px 14px;font-family:var(--font-mono);font-size:11px;letter-spacing:1px;cursor:pointer;box-shadow:var(--bs);">HARI INI</button>
            <button @click="nextMonth()" style="background:var(--ink);color:var(--paper);border:none;padding:8px 14px;font-family:var(--font-mono);font-size:11px;letter-spacing:1px;cursor:pointer;box-shadow:var(--bs);">SELANJUTNYA →</button>
        </div>

        {{-- Loading --}}
        <template x-if="loading">
            <div style="padding:20px;">
                <div class="budget-card-skeleton" style="margin-bottom:12px;" x-repeat="5">
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

        {{-- Kalender Grid --}}
        <template x-if="!loading">
            <div style="padding:0 16px 16px;">
                {{-- Header Hari --}}
                <div class="cal-grid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;margin-bottom:4px;">
                    <template x-for="dayName in ['MIN','SEN','SEL','RAB','KAM','JUM','SAB']" :key="dayName">
                        <div style="text-align:center;font-family:var(--font-mono);font-size:9px;letter-spacing:1.5px;color:rgba(17,16,16,.4);padding:6px 0;" x-text="dayName"></div>
                    </template>
                </div>

                {{-- Grid Tanggal --}}
                <div class="cal-grid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;">
                    <template x-for="(cell, idx) in generateCalendarDays()" :key="idx">
                        <div class="cal-cell"
                             :class="{
                                 'cal-cell-empty': !cell.day,
                                 'cal-cell-today': isToday(cell.dateKey),
                                 'cal-cell-selected': selectedDate === cell.dateKey,
                                 'cal-cell-has-tx': daySummary[cell.dateKey] && daySummary[cell.dateKey].count > 0
                             }"
                             @click="cell.day && selectDate(cell.dateKey)"
                             style="aspect-ratio:1;display:flex;flex-direction:column;align-items:center;justify-content:center;border:1.5px solid transparent;font-family:var(--font-mono);cursor:pointer;position:relative;transition:all .15s;">

                            {{-- Angka Tanggal --}}
                            <span x-show="cell.day" x-text="cell.day"
                                  style="font-size:13px;font-weight:500;color:var(--ink);"></span>

                            {{-- Dot Indicator --}}
                            <div x-show="daySummary[cell.dateKey] && daySummary[cell.dateKey].count > 0"
                                 style="display:flex;gap:3px;margin-top:3px;">
                                <div x-show="daySummary[cell.dateKey]?.income > 0"
                                     style="width:5px;height:5px;border-radius:50%;background:var(--mint);"></div>
                                <div x-show="daySummary[cell.dateKey]?.expense > 0"
                                     style="width:5px;height:5px;border-radius:50%;background:var(--punch);"></div>
                            </div>

                            {{-- Jumlah Transaksi --}}
                            <span x-show="daySummary[cell.dateKey]?.count > 0"
                                  x-text="daySummary[cell.dateKey]?.count"
                                  style="font-size:8px;color:rgba(17,16,16,.35);margin-top:1px;"></span>
                        </div>
                    </template>
                </div>

                {{-- Legend --}}
                <div style="display:flex;gap:16px;justify-content:center;margin-top:12px;font-family:var(--font-mono);font-size:9px;color:rgba(17,16,16,.5);">
                    <div style="display:flex;align-items:center;gap:4px;">
                        <div style="width:6px;height:6px;border-radius:50%;background:var(--mint);"></div>
                        <span>MASUK</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:4px;">
                        <div style="width:6px;height:6px;border-radius:50%;background:var(--punch);"></div>
                        <span>KELUAR</span>
                    </div>
                </div>

                {{-- Summary Bulanan --}}
                <div style="margin-top:16px;padding:12px;border:var(--border);background:var(--cream);box-shadow:var(--bs);">
                    <div style="font-family:var(--font-mono);font-size:9px;letter-spacing:2px;color:rgba(17,16,16,.4);margin-bottom:8px;">RINGKASAN BULAN INI</div>
                    <div style="display:flex;justify-content:space-between;">
                        <div>
                            <div style="font-family:var(--font-mono);font-size:9px;color:rgba(17,16,16,.5);">MASUK</div>
                            <div style="font-size:16px;font-weight:700;color:var(--mint);" x-text="'Rp ' + formatNumber(transactions.filter(t => t.type === 'income').reduce((s, t) => s + (Number(t.amount) || 0), 0))"></div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-family:var(--font-mono);font-size:9px;color:rgba(17,16,16,.5);">KELUAR</div>
                            <div style="font-size:16px;font-weight:700;color:var(--punch);" x-text="'Rp ' + formatNumber(transactions.filter(t => t.type === 'expense').reduce((s, t) => s + (Number(t.amount) || 0), 0))"></div>
                        </div>
                    </div>
                    <div style="margin-top:8px;padding-top:8px;border-top:1.5px solid rgba(17,16,16,.1);display:flex;justify-content:space-between;">
                        <div style="font-family:var(--font-mono);font-size:9px;color:rgba(17,16,16,.5);">TOTAL TRANSAKSI</div>
                        <div style="font-family:var(--font-mono);font-size:12px;font-weight:700;" x-text="transactions.length + ' transaksi'"></div>
                    </div>
                </div>
            </div>
        </template>

        {{-- Detail Transaksi per Tanggal --}}
        <template x-if="selectedDate">
            <div style="padding:0 16px 16px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <div>
                        <div style="font-family:var(--font-mono);font-size:9px;letter-spacing:2px;color:rgba(17,16,16,.4);">TRANSAKSI TANGGAL</div>
                        <div style="font-size:16px;font-weight:700;color:var(--ink);" x-text="formatDate(selectedDate)"></div>
                    </div>
                    <button @click="closeDetail()" style="background:var(--ink);color:var(--paper);border:none;padding:6px 12px;font-family:var(--font-mono);font-size:10px;cursor:pointer;">TUTUP</button>
                </div>

                {{-- Empty State --}}
                <template x-if="selectedTransactions.length === 0">
                    <div style="text-align:center;padding:24px;border:1.5px dashed rgba(17,16,16,.15);">
                        <div style="font-size:32px;margin-bottom:8px;">📭</div>
                        <div style="font-family:var(--font-mono);font-size:11px;color:rgba(17,16,16,.4);">Tidak ada transaksi di tanggal ini</div>
                    </div>
                </template>

                {{-- List Transaksi --}}
                <template x-if="selectedTransactions.length > 0">
                    <div>
                        <template x-for="tx in selectedTransactions" :key="tx.id">
                            <a :href="'/transactions/' + tx.id"
                               style="display:flex;align-items:center;gap:10px;padding:10px;border:1.5px solid rgba(17,16,16,.1);margin-bottom:6px;text-decoration:none;color:var(--ink);transition:border-color .15s;"
                               onmouseover="this.style.borderColor='var(--ink)'" onmouseout="this.style.borderColor='rgba(17,16,16,.1)'">
                                <div style="font-size:20px;" x-text="getEmoji(tx.category_name)"></div>
                                <div style="flex:1;">
                                    <div style="font-size:13px;font-weight:700;" x-text="tx.description"></div>
                                    <div style="font-family:var(--font-mono);font-size:10px;color:rgba(17,16,16,.5);" x-text="tx.category_name || 'LAINNYA'"></div>
                                </div>
                                <div style="text-align:right;">
                                    <div style="font-size:13px;font-weight:700;" :style="'color:' + (tx.type === 'income' ? 'var(--mint)' : 'var(--punch)')"
                                         x-text="(tx.type === 'income' ? '+' : '−') + 'Rp ' + formatNumber(tx.amount)"></div>
                                </div>
                            </a>
                        </template>
                    </div>
                </template>
            </div>
        </template>
    </div>
</div>
@endsection
