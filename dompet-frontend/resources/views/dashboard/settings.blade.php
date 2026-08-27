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

            {{-- Notifications --}}
            <div class="settings-section">
                <div class="settings-section-title">NOTIFIKASI</div>
                <div class="settings-section-desc">Kontrol notifikasi dari Zaku.</div>

                <div class="settings-toggle-row" style="margin-top:12px;">
                    <div style="flex:1;">
                        <div style="font-size:14px;font-weight:700;color:var(--ink);">Notifikasi Budget</div>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;color:rgba(17,16,16,.5);">Peringatan saat budget mendekati batas.</div>
                    </div>
                    <button class="settings-toggle" :class="{ on: form.budget_alerts }" @click="form.budget_alerts = !form.budget_alerts">
                        <div class="settings-toggle-knob"></div>
                    </button>
                </div>

                <div class="settings-toggle-row">
                    <div style="flex:1;">
                        <div style="font-size:14px;font-weight:700;color:var(--ink);">Notifikasi Email</div>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;color:rgba(17,16,16,.5);">Kirim ringkasan lewat email.</div>
                    </div>
                    <button class="settings-toggle" :class="{ on: form.email_notifications }" @click="form.email_notifications = !form.email_notifications">
                        <div class="settings-toggle-knob"></div>
                    </button>
                </div>
            </div>

            {{-- Save --}}
            <button class="btn-budget-submit" @click="save()" :disabled="saving" x-text="saving ? 'MENYIMPAN...' : 'SIMPAN PENGATURAN'"></button>
        </div>
    </div>
</div>
@endsection
