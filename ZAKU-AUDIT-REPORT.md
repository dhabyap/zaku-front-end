# ZAKU — Comprehensive Audit Report

**Project:** ZAKU Digital Wallet + AI Chat  
**Tech Stack:** Laravel 10 (Backend) · Laravel Blade + Alpine.js (Frontend) · JWT Auth · Vite  
**Date:** 2026-11-12  
**Auditor Perspective:** Professional Developer + Marketing Strategist  

---

## 📌 Ringkasan Eksekutif

ZAKU adalah aplikasi dompet digital dengan fitur AI chat yang cukup matang di level functional. Fondasi code arsitektur service-layer, versioning API, dan design system konsisten. Namun ada celah keamanan, missing marketing funnel, dan opportunity fitur yang belum dimanfaatkan.

---

## 🔴 PRIORITAS TINGGI — Security & Architecture

### 1. JWT Token di localStorage — XSS Risk
**Severity: HIGH**  
**File:** `dompet-frontend/resources/js/auth.js:3`, `api-client.js:24-31`

```js
// ❌ Current: Token di localStorage, rentan XSS
export const getToken = () => localStorage.getItem('access_token');
```

```js
// ✅ Recommended: Cookie httpOnly + SameSite
export const getToken = () => getCookie('access_token') || localStorage.getItem('access_token');
```

**Masalah:** localStorage dapat dibaca JavaScript manapun di page (XSS attack). Token JWT harus diisolasi di httpOnly cookie dengan `SameSite=Strict` agar tidak terbaca script berbahaya.

**Fix:**
1. Backend: set cookie `access_token` dengan `httpOnly: true, sameSite: 'strict'` saat login
2. Frontend: hapus `setItem('access_token')`, gunakan cookie
3. CSRF middleware perlu diaktifkan untuk cross-origin request

---

### 2. Refresh Token Implementasi — Kontrak Ambigu
**Severity: HIGH**  
**File:** `backend/app/Http/Controllers/Api/AuthController.php:215-222`

Dokumentasi menyebut `access_token` dan `refresh_token` terpisah, tapi implementasi mengembalikan token yang sama di dua field. Kontrak perlu diperjeli dan didokumentasikan. Jika memang single-token rotate, dokumentasi harus akurat. Jika ingin benar-benar terpisah, perlu desain terpisah dan migrasi frontend yang kompatibel.

**Recommendation:** Tentukan strategi terlebih dahulu, jangan ubah setengah-setengah.

---

### 3. Password Change — Session Tidak Dicabut
**Severity: HIGH**  
**File:** `backend/app/Http/Controllers/Api/AuthController.php:408-420`

Reset/ganti password tidak secara eksplisit mencabut sesi lain. User yang login di perangkat lain akan tetap dapat akses setelah password diubah.

**Fix:** Saat password berubah, invalidate semua token atau masukkan ke blacklist.

---

### 4. Auth Audit Trail — Tidak Ada Logging
**Severity: MEDIUM**  
**File:** `AuthController.php` secara umum

Tidak ada pencatatan event login berhasil/gagal, reset password, logout, dan keputusan admin. Ini penting untuk investigasi insiden keamanan.

**Fix:** Tambahkan `ActivityLog` untuk event:
- Login berhasil/gagal (user_id, IP, user-agent, timestamp)
- Password reset requested/completed
- Logout
- Admin role escalation attempt

> ⚠️ Jangan simpan password, JWT, reset token, atau secret di log.

---

### 5. Otorisasi Admin — Cakupan Tidak Lengkap
**Severity: MEDIUM**  
**File:** `backend/routes/api.php:88-101`, `AdminOnly.php`

Middleware `admin` ditemukan hanya di operasi kategori dan analytics dashboard. Inventaris seluruh endpoint sensitif — pastikan setiap aksi yang butuh izin khusus punya pemeriksaan backend yang tepat. User biasa tidak boleh bisa create/update/delete kategori.

---

## 🟡 PRIORITAS MENENGAH — SEO & Performance

### 6. Schema.org JSON-LD — Belum Ada
**Severity: HIGH**  
**File:** `dompet-frontend/resources/views/landing.blade.php`

Landing page tidak memiliki structured data. Ini直接影响 SEO score di Google Search. Sudah terdokumentasi di `docs/issues/seo-zaku-enhancement_issue.md` tapi belum diimplementasi.

**Fix:** Tambahkan di `<head>` landing page:

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "ZAKU",
  "applicationCategory": "FinanceApplication",
  "operatingSystem": "Web",
  "url": "https://zaku.abysoft.my.id",
  "description": "ZAKU — Catat keuanganmu dengan chat. Tanpa form ribet. AI langsung paham dan analisis.",
  "offers": {"@type": "Offer", "price": "0", "priceCurrency": "IDR"},
  "inLanguage": "id-ID"
}
</script>
```

---

### 7. sitemap.xml & robots.txt — Belum Ada
**Severity: HIGH**  
**File:** `dompet-frontend/public/`

Tidak ada sitemap untuk search engine crawler. robots.txt tidak punya `Sitemap:` directive.

**Fix:**
1. Buat `public/sitemap.xml`
2. Update `public/robots.txt` dengan `Sitemap: https://zaku.abysoft.my.id/sitemap.xml`
3. Submit ke Google Search Console

---

### 8. Webfont Performance — Google Fonts 90% Total Weight
**Severity: MEDIUM**  
**File:** `dompet-frontend/resources/views/landing.blade.php:31`

Lighthouse report: Fraunces 81KB, Syne 34.6KB, DM Mono 8.7KB. Total 90% dari 147KB page weight.

**Fix Options:**
- **Quick win:** Tambahkan `&display=swap` ke Google Fonts URL
- **Best practice:** Self-host subset woff2 fonts untuk mobile performance
- **Fonts yang perlu di-subset:** Fraunces italic 700 → ~20KB subset cukup

---

### 9. Cache Headers — Tidak Ada
**Severity: MEDIUM**  
**File:** `.htaccess` atau Laravel middleware

Font dan static asset tidak punya Cache-Control header. Setiap load ulang page, browser download ulang semua asset.

**Fix di .htaccess:**
```apache
<IfModule mod_headers.c>
  <FilesMatch "\.(woff2|css|js|svg|png|jpg)$">
    Header set Cache-Control "public, max-age=604800"
  </FilesMatch>
</IfModule>
```

---

## 🟠 PRIORITAS STANDARD — Features & UX

### 10. Privacy / Pin Lock — Tidak Ada
**Severity: MEDIUM**

Tidak ada mekanisme lock app dengan PIN atau biometric. User yang pinjam HP bisa langsung lihat seluruh history keuangan.

**Recommendation:**
- PIN 4-6 digit sebagai second layer (simpan hashed di localStorage)
- Biometric prompt di mobile (Web Authentication API)
- Auto-lock setelah 5 menit idle

---

### 11. Multi-Device Sync — Token Tidak Saling Tadnig
**Severity: MEDIUM**  
**File:** `backend/app/Http/Controllers/Api/AuthController.php`

User login di perangkat A tidak tahu bahwa perangkat B juga login. Tidak ada "recent activity" atau "logout other devices".

**Recommendation:**
- Tampilkan "Active Sessions" di halaman Profile
- Fitur "Logout Semua Perangkat Lain"
- Notifikasi saat login dari perangkat baru

---

### 12. Recurring Transactions — UI Belum Ada
**Severity: MEDIUM**  
**File:** `backend/app/Http/Controllers/Api/RecurringTransactionController.php`

Backend sudah punya fitur lengkap recurring transactions (`/v1/recurring-transactions`), tapi di frontend belum ada halaman khusus untuk mengelolanya.

**Recommendation:** Buat `/recurring` page untuk:
- Daftar recurring yang aktif
- Form buat recurring baru (frekuensi: harian/mingguan/bulanan)
- Edit/hapus recurring
- Toggle on/off tanpa hapus

---

### 13. PWA / Offline Mode — Tidak Ada
**Severity: MEDIUM**

Zaku adalah financial app — user perlu bisa catat transaksi bahkan saat offline. Tidak ada service worker.

**Recommendation:**
- Service Worker untuk cache static assets
- IndexedDB untuk offline transaction queue
- Sync saat online kembali
- Install prompt untuk "Add to Home Screen"

---

### 14. Push Notifications — Tidak Ada
**Severity: MEDIUM**

Frontend sudah ada sistem notification panel (dashboard), tapi belum ada push notification browser-level. User tidak dapat notifikasi saat:
- Budget hampir habis
- Recurring transaction berhasil diproses
- Transfer masuk dari user lain

**Recommendation:**
- Web Push API (`Notification.requestPermission()`)
- Endpoint subscribe/unsubscribe di backend
- Service worker untuk handle push events

---

### 15. Dark Mode Toggle — Tidak Ada (UI sudah mendukung dark)
**Severity: LOW**

CSS sudah punya `--ink: #111010` sebagai dark background. Tapi belum ada toggle untuk switch theme. Beberapa user prefer dark mode.

**Fix:**
```js
// Tambahkan di settings atau app header
const theme = localStorage.getItem('zaku_theme') || 'light';
document.documentElement.setAttribute('data-theme', theme);
```

---

### 16. CSV Export — Terbatas
**Severity: LOW**  
**File:** `backend/app/Http/Controllers/Api/ExportController.php`

Hanya export monthly PDF. User finance-savvy sering butuh CSV untuk analysis di spreadsheet.

**Recommendation:** Tambahkan endpoint `/export/monthly-csv` yang menghasilkan CSV download dengan kolom: Tanggal, Kategori, Deskripsi, Tipe, Jumlah, Balance.

---

### 17. Transaction Search — Kurang Powerful
**Severity: LOW**  
**File:** `dompet-frontend/resources/views/dashboard/transactions.blade.php`

Filter sudah ada (tanggal, kategori, tipe) tapi search masih basic text match. Belum ada:
- Search by amount range
- Search by date range via UI (kalau sudah ada date input)
- Filter by wallet (jika ada multi-wallet)

---

### 18. Budget Alerts — Push notification atau Email
**Severity: LOW**  
**File:** `backend/app/Services/BudgetNotificationService.php`

Backend punya `BudgetNotificationService` + scheduler command (`CheckBudgetNotifications`). Tapi belum ada bukti email atau push notification dikirim.

**Recommendation:** Aktifkan dan test scheduler, tambahkan email template jika belum ada.

---

## 🟢 PRIORITAS OPSIONAL — Marketing & Growth

### 19. Landing Page — Missing Marketing Elements
**Severity: HIGH** (Growth)

Landing page sudah bagus secara visual (1910 lines CSS, good structure), tapi tidak ada:

| Elemen | Status | Priority |
|---|---|---|
| User count / social proof | ❌ | P1 |
| Testimonial section | ❌ | P1 |
| Feature video demo | ❌ | P2 |
| Comparison table (vs competitor) | ❌ | P2 |
| Use case / persona stories | ❌ | P2 |
| FAQ section | ❌ | P2 |
| App store badges | ❌ | P2 |
| Referral program CTA | ❌ | P3 |
| Changelog / roadmap public | ❌ | P3 |
| Blog / educational content | ❌ | P3 |
| Press mentions | ❌ | P3 |

**Recommendation:** Tambahkan minimal 3 elemen P1-P2:
1. Social proof bar: "📊 10.000+ user aktif" (jika ada data)
2. Testimonial carousel dari 3-5 user
3. FAQ accordion dengan 5-8 pertanyaan umum

---

### 20. Gamification — Tidak Ada
**Severity: MEDIUM** (Engagement)

Tidak ada elemen gamification yang bikin user balik lagi:
- Streak catat transaksi harian
- Badge achievement ("10 transaksi pertama", "Budget master")
- Weekly summary email
- Challenge bulanan (e.g., "Hemat Rp 500rb bulan ini")

**Recommendation:** Mulai dari yang cheap:
- "🔥 Catat transaksi 7 hari berturut-turut" badge
- Monthly summary dikirim via email
- Celebration animation saat budget goal tercapai

---

### 21. Onboarding Flow — Tidak Ada
**Severity: MEDIUM**

User baru register langsung masuk dashboard tanpa panduan. Pertama kali pakai, user bingung fitur apa saja yang ada.

**Recommendation:** Buat 3-step onboarding:
1. "Selamat datang! Atur budget bulanan kamu" → link ke `/budgets`
2. "Catat transaksi pertama via chat atau manual?" → pilihan
3. "Tambahkan kategori kustom" → opsional

Simpan flag `onboarding_completed` di user profile. Tampilkan lagi jika belum selesai.

---

### 22. Referral System — Tidak Ada
**Severity: MEDIUM** (Growth)

Tidak ada mekanisme ajak teman. Revenue model yang realistis untuk app finance adalah:
- Free tier dengan limit
- Paid tier untuk fitur premium
- Referral bonus untuk kedua belah pihak

**Recommendation:**
- Generate unique referral code per user
- Landing page: "Dapat Rp 10.000 untuk teman yang kamu ajak"
- Tracking: siapa ajak siapa, kapan signup, apakah first transaction done

---

### 23. Email Templates — Tidak Teratur
**Severity: MEDIUM**

Backend menggunakan `Mail` facade tapi tidak ada evidence email templates yang well-structured (Laravel Mailable classes). Email verification, reset password, budget alerts, dll perlu template yang branded.

**Recommendation:**
- Buat Mailable classes dengan Blade template
- Design email sesuai brand identity (Brutalist style)
- Test dengan Mailtrap sebelum production

---

## ✅ YANG SUDAH BAGUS

| Aspek | Keterangan |
|---|---|
| ✅ API Versioning | `v1` prefix, legacy redirect |
| ✅ Form Request Validation | Semua input divalidasi via dedicated Request classes |
| ✅ Service Layer | Business logic di `app/Services/`, controller fokus routing |
| ✅ Design System | CSS variables konsisten, Brutalist character kuat |
| ✅ Accessibility | ARIA labels, roles, live regions, focus management |
| ✅ Token Expiry Handling | 60s buffer, graceful redirect ke login |
| ✅ Rate Limiting | Throttle middleware di auth endpoints |
| ✅ Password Hashing | Laravel `hashed` cast |
| ✅ Database Indexes | Migration `add_indexes_to_transactions` |
| ✅ Feature Flags | `config('app.feature_email_verification')` |
| ✅ Changelog System | DB + UI untuk public/internal changelog |
| ✅ Budget Health Scoring | Algorithm: score 0-100, status: aman/waspada/boros |
| ✅ Privacy Mode | Hide/show amount, persisted via localStorage |
| ✅ Notification Panel | Slide panel dengan unread count |
| ✅ Monthly Recap | PDF export via DomPdf |
| ✅ AI Transaction Parser | NLP-like parser dari chat text |
| ✅ Skeleton Loading | User tidak melihat blank screen |
| ✅ Responsive Nav | Mobile bottom nav + desktop sidebar |
| ✅ Toast Notifications | User tahu hasil aksi |

---

## 📋 ROADMAP PRIORITAS

### 🎯 Sprint 1 — Security Hardening (1-2 minggu)
1. Fix JWT cookie httpOnly (backend + frontend)
2. Dokumentasikan kontrak refresh token
3. Implement session revocation on password change
4. Audit trail untuk event auth
5. Inventaris & fix otorisasi semua route

### 🎯 Sprint 2 — SEO & Performance (1 minggu)
1. JSON-LD schema di landing
2. sitemap.xml + robots.txt
3. Font self-host atau subset + display=swap
4. Cache-Control headers
5. Lighthouse retest incognito

### 🎯 Sprint 3 — Feature Completeness (2-3 minggu)
1. Halaman Recurring Transactions
2. PIN/Biometric lock
3. Dark mode toggle
4. CSV export
5. Onboarding flow

### 🎯 Sprint 4 — PWA & Notifications (2 minggu)
1. Service Worker setup
2. Offline transaction queue (IndexedDB)
3. Web Push API
4. Install prompt

### 🎯 Sprint 5 — Growth (ongoing)
1. Landing page marketing elements
2. Referral system
3. Gamification
4. Email templates + transactional emails
5. Blog/content marketing

---

## 📊 Effort vs Impact Matrix

| Feature | Effort | Impact | Priority |
|---|---|---|---|
| JSON-LD schema | 1h | HIGH | P0 |
| sitemap.xml + robots.txt | 1h | HIGH | P0 |
| Font optimization | 2h | MEDIUM | P1 |
| JWT httpOnly cookie | 4h | HIGH | P0 |
| Session revocation | 2h | HIGH | P0 |
| Auth audit trail | 4h | MEDIUM | P1 |
| Recurring Transactions UI | 8h | MEDIUM | P1 |
| Onboarding flow | 6h | MEDIUM | P1 |
| Pin/Biometric lock | 4h | MEDIUM | P1 |
| Dark mode toggle | 2h | LOW | P2 |
| CSV export | 3h | LOW | P2 |
| PWA + offline | 16h | HIGH | P1 |
| Push notifications | 8h | MEDIUM | P1 |
| Referral system | 12h | HIGH | P2 |
| Gamification | 8h | MEDIUM | P2 |
| Landing marketing | 8h | HIGH | P1 |
| Email templates | 6h | MEDIUM | P2 |
| FAQ section | 3h | MEDIUM | P2 |

---

## 🔧 File yang Perlu Diubah (Summary)

### Frontend (`dompet-frontend/`)
| File | Aksi |
|---|---|
| `resources/js/auth.js` | Cookie-based token storage |
| `resources/js/api-client.js` | Baca token dari cookie |
| `resources/views/landing.blade.php` | JSON-LD, marketing elements |
| `public/sitemap.xml` | Buat baru |
| `public/robots.txt` | Update dengan Sitemap directive |
| `.htaccess` | Cache-Control headers |
| `resources/views/dashboard/recurring.blade.php` | Buat baru |
| `resources/views/dashboard/onboarding.blade.php` | Buat baru |
| `resources/views/dashboard/settings.blade.php` | Dark mode + PIN toggle |

### Backend (`backend/`)
| File | Aksi |
|---|---|
| `app/Http/Controllers/Api/AuthController.php` | httpOnly cookie, session revocation, audit log |
| `app/Http/Middleware/JwtAuthenticate.php` | Baca dari cookie |
| `app/Http/Controllers/Api/ExportController.php` | CSV export |
| `app/Services/RecurringTransactionService.php` | Buat baru (jika belum ada) |
| `app/Mail/*.php` | Buat Mailable classes |
| `routes/api.php` | Push subscription endpoints |

---

*Doc ini bersifat living document — update saat sprint baru dimulai.*
