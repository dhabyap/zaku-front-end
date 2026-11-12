@extends('layouts.app')

@section('content')
<div x-data="settingsPage" style="display:flex;flex-direction:column;height:100%;">
    <header class="dash-header">
        <div class="dh-row">
            <div>
                <div class="dh-greet">PENGATURAN</div>
                <div class="dh-name">Preferensi Kamu</div>
            </div>
            <a href="/dashboard" class="dh-avatar" style="text-decoration:none;font-size:18px;">←</a>
        </div>
    </header>

    <div class="screen-body" style="padding-bottom:100px;">
        <div style="padding:16px;">

            {{-- Monthly Budget --}}
            <div class="settings-section">
                <div class="settings-section-title">BUDGET BULANAN</div>
                <div class="settings-section-desc">Batas pengeluaran per bulan untuk tracking budget.</div>
                <div class="field" style="margin-top:12px;">
                    <label>NOMINAL (Rp)</label>
                    <input type="number" x-model.number="form.monthly_budget" placeholder="6000000" min="0" class="field-input">
                </div>
            </div>

            {{-- Currency --}}
            <div class="settings-section">
                <div class="settings-section-title">MATA UANG</div>
                <div class="settings-section-desc">Mata uang yang digunakan untuk tampilan nominal.</div>
                <div class="field" style="margin-top:12px;">
                    <label>MATA UANG</label>
                    <select x-model="form.currency" class="field-select">
                        <option value="IDR">IDR — Rupiah (Rp)</option>
                        <option value="USD">USD — US Dollar ($)</option>
                    </select>
                </div>
            </div>

            {{-- Notification availability --}}
            <div class="settings-section">
                <div class="settings-section-title">NOTIFIKASI</div>
                <div class="settings-section-desc">Peringatan budget muncul di inbox aplikasi dan lonceng notifikasi.</div>

                <div class="settings-toggle-row" style="margin-top:12px;">
                    <div style="flex:1;">
                        <div style="font-size:14px;font-weight:700;color:var(--ink);">Notifikasi Budget</div>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;color:rgba(17,16,16,.5);">Peringatan dibuat saat pengeluaran mencapai 80% dan 100% budget, lalu muncul di lonceng notifikasi.</div>
                    </div>
                    <span class="settings-feature-status">OTOMATIS</span>
                </div>

                <div class="settings-toggle-row">
                    <div style="flex:1;">
                        <div style="font-size:14px;font-weight:700;color:var(--ink);">Notifikasi Email</div>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;color:rgba(17,16,16,.5);">Ringkasan dan peringatan melalui email belum tersedia.</div>
                    </div>
                    <span class="settings-feature-status unavailable">BELUM TERSEDIA</span>
                </div>
            </div>

            {{-- Save --}}
            <button class="btn-budget-submit" @click="save()" :disabled="saving" x-text="saving ? 'MENYIMPAN...' : 'SIMPAN PENGATURAN'"></button>
        </div>
    </div>
</div>
@endsection
