# Analisis Komprehensif Website ZAKU (zaku.abysoft.my.id)

> **Tanggal Analisis:** 19 November 2026  
> **Target:** Arsitektur, Fitur, Desain/UX, Keamanan, Performa, dan Potensi Monetisasi / Pengembangan

---

## 1. Ringkasan Eksekutif & Karakter Produk

ZAKU adalah aplikasi pencatatan keuangan pribadi (*Personal Financial Management*) dengan pendekatan visual **Brutalisme Neo-Retro / Paper-Ink** (font mono, border hitam tebal, palet krem/punch/mint). 

Keunggulan utamanya:
- **Natural Language & AI-driven:** Fitur pencatatan lewat chat AI (`/chat`) menggunakan model LLM (Groq / Gemini) dengan fallback parser lokal bahasa Indonesia.
- **Micro-features lengkap:** Anggaran dinamis (Budgets), Transaksi Berulang (Recurring), Kalender Arus Kas, Insights & Financial Health Score, Rekap Bulanan + Ekspor PDF, hingga Notifikasi in-app.

---

## 2. Peta Arsitektur Saat Ini

```
[ User Browser ]
       │
       ├─► [ Frontend: zaku.abysoft.my.id ]
       │     └─ Laravel Blade + Alpine.js 3 + TailwindCSS/PostCSS (Vite)
       │
       └─► [ Backend API: api-zaku.abysoft.my.id ]
             └─ Laravel 10/11 REST API + JWT Auth + SQLite/MySQL
             └─ AI Gateway: Groq (Llama-3) / Gemini / Local Regex
```

---

## 3. Temuan Masalah & Kekurangan Saat Ini

### A. Arsitektur & Autentikasi (High Priority)
1. **Split-Domain Cookie Friction:**
   - Karena frontend (`zaku.abysoft.my.id`) dan backend (`api-zaku.abysoft.my.id`) beda subdomain, browser sering memblokir third-party cookies atau terjadi mismatch token.
   - *Rekomendasi:* Gunakan Nginx reverse proxy di cPanel agar API bisa diakses lewat `zaku.abysoft.my.id/api/v1/...` (100% same-origin).
2. **Double Encryption Misunderstanding:**
   - Frontend Laravel mengenkripsi cookie sendiri; token JWT dari backend sempat ditolak middleware sebelum ditambahkan ke `EncryptCookies::$except`.

### B. User Experience & Antarmuka (UX/UI)
1. **Desain Brutalisme Terlalu Kaku di Mobile:**
   - Gaya brutalist dengan garis hitam 2px-3px dan font mono sangat unik di desktop, namun di layar HP sempit (360px - 390px) teks sering sesak, button terasa padat, dan kontras tajam melelahkan mata bila dipakai jangka panjang.
2. **Tidak Ada Mode Gelap (Dark Mode):**
   - Aplikasi keuangan sering dibuka malam hari saat mengecek pengeluaran harian. Ketiadaan dark mode membuat layar silau.
3. **PWA (Progressive Web App) Belum Ada:**
   - Belum ada `manifest.json` dan Service Worker. User belum bisa install Zaku sebagai shortcut icon app di homescreen HP Android/iOS dan tidak ada offline fallback.

### C. Fungsionalitas & Fitur Finansial
1. **Export Transaksi Belum Ada Format Excel/CSV:**
   - Baru tersedia ekspor PDF rekap bulanan. Pengguna yang suka olah data keuangan di spreadsheet membutuhkan file `.csv` atau `.xlsx`.
2. **Scan Struk Belum Ada (OCR Receipt):**
   - Input masih manual atau chat teks. Fitur foto struk belanjaan (OCR) akan mempercepat pencatatan transaksi.

### D. Performa & Database
1. **Cron Scheduler di Shared Hosting:**
   - `ProcessRecurringTransactions` dan `CheckBudgetNotifications` memerlukan cron job cPanel yang aktif secara berkala (`* * * * * php artisan schedule:run`). Jika cron server mati, transaksi berulang tidak ter-trigger.

---

## 4. Rekomendasi Peningkatan (Roadmap Enhancement)

### 🚀 Quick Wins (Bisa Dikerjakan Cepat - 1 s/d 2 Hari)
| Fitur / Perbaikan | Dampak | Detail Teknis |
|---|---|---|
| **Ekspor CSV / Excel** | ⭐⭐⭐⭐ | Tambah endpoint `/v1/export/transactions-csv` di backend dan tombol download di halaman transaksi. |
| **Quick Action / Floating Add** | ⭐⭐⭐⭐ | Tambah Floating Action Button (FAB) `+` di pojok kanan bawah dashboard untuk akses cepat: Catat Pengeluaran, Pemasukan, atau Scan. |
| **PWA Web Manifest** | ⭐⭐⭐⭐ | Tambah `manifest.json`, icon app 192x192 & 512x512, serta meta tag iOS agar bisa *Add to Home Screen*. |

---

### 🌟 Mid-Term Enhancements (1 s/d 2 Minggu)
| Fitur / Peningkatan | Deskripsi |
|---|---|
| **OCR Scan Struk (Receipt Scanner)** | Upload foto struk Alfamart/Indomaret/resto, diproses AI vision (Gemini Flash / OpenAI Vision) untuk langsung mendeteksi total dan list belanja. |
| **Dark Theme Toggle** | Pilihan tema Brutalist Light vs Modern Cyber/Dark Slate. |
| **Widget Analitik & Tren Pengeluaran** | Chart perbandingan pengeluaran bulan ini vs bulan lalu (cth: "Pengeluaran kopi kamu naik 25% minggu ini"). |

---

### 🛡️ Hardening & Security (Pondasi Teknis)
1. **Penyatuan Subdomain (Reverse Proxy):** Ubah arsitektur agar web frontend dan API berada dalam satu origin via cPanel/Nginx proxy.
2. **Rate Limiting AI Chat:** Lindungi endpoint AI dari abuse token dengan limiter per IP/User (misal max 20 pesan per jam untuk user gratis).
3. **Database Backup Otomatis:** Buat command auto-backup SQLite/MySQL ke cloud storage (Google Drive / S3 / Telegram Bot pribadi).

---

## 5. Matriks Prioritas

```
              TINGGI
                │
                │   [OCR Scan Struk]     [Export CSV]
                │   [PWA Manifest]       [Dark Mode]
     DAMPAK     │
     TERHADAP   │
     USER       │   [Reverse Proxy]      [Widget Analitik]
                │
                └─────────────────────────────────────────►
               RENDAH                  TINGGI
                            KOMPLEKSITAS
```

Dokumen ini disimpan di root project: `D:/Latihan/Zaku/ANALISIS_DAN_ENHANCEMENT.md`.
