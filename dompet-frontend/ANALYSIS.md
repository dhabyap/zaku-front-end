# Analisa Lengkap — Zaku Frontend (dompet-frontend)

> Dokumen ini dibuat dari hasil pembacaan langsung source code: views, controllers, Alpine components, JS modules, routes, config, package.json.
> Tanggal analisa: 2026-11-25.

---

## 1. Ringkasan Proyek

| Aspek | Detail |
|-------|--------|
| Nama | `dompet-frontend` |
| Tipe | **Laravel 10 + Blade + Alpine.js** (server-rendered, bukan SPA) |
| Fungsi | Web app UI untuk Zaku — catat keuangan via chat, dashboard, budget, report |
| Auth | JWT di **httpOnly cookie** (diset backend, dibaca via cookie fallback) |
| API Base | `https://api-zaku.abysoft.my.id/api` (env: `VITE_API_BASE_URL`) |
| Build | Vite 5 + laravel-vite-plugin |
| CSS | Custom CSS di `landing.blade.php` (inline, 1600+ baris) + `custom.css` |
| JS | Alpine.js 3.15 + Axios + modular ES modules (`api-client.js`, `auth.js`, `alpine-components.js`, `utils.js`) |
| Deployment | Shared hosting / VPS (Laravel) |
| Status | `vendor/` & `node_modules/` terinstall, `.env` ada |

**Halaman tersedia:** Landing, Login, Register, Verify Email, Forgot Password, Dashboard (Home, Transactions, Detail, Categories, Budgets, Recurring, Insights, Changelogs, Profile, Settings, Calendar, Monthly Recap, Chat, Manual Input, Onboarding).

---

## 2. Arsitektur & Alur Data

```
Browser (Alpine.js)
    │
    ├─ api-client.js (Axios instance)
    │     ├─ baseURL: VITE_API_BASE_URL
    │     ├─ withCredentials: true
    │     ├─ Interceptor request: attach Bearer token dari cookie 'token'/'zaku_token'
    │     └─ Interceptor response: 401 → clear localStorage, redirect /login?session=expired
    │
    ├─ auth.js (token di httpOnly cookie, user data di localStorage/sessionStorage)
    │
    ├─ alpine-components.js (1000+ baris, 15+ komponen: loginForm, registerForm, dashboardHome, chatPage, transactionList, transactionDetail, profilePage, budgetPage, changelogPage, dll.)
    │
    └─ utils.js (showToast, parseApiError, confirmDialog, handleApiError, formatNumber, formatDate, dll.)

Laravel Blade (Server)
    ├─ routes/web.php → PageController (hanya return view)
    ├─ views/ (Blade templates, inline <script> memanggil Alpine.data())
    ├─ middleware/JwtSession.php (validasi JWT dari cookie, set auth user)
    └─ config/cors.php (allowed_origins: '*')
```

**Pola komunikasi:**
1. User buka halaman → Laravel render Blade + inject CSRF/asset
2. Alpine component `init()` → `apiClient.get('/v1/dashboard')` → Backend API
3. Response JSON → Alpine reactive data → DOM update
4. Form submit → `apiClient.post(...)` → Backend → Toast notifikasi → Redirect/Refresh

---

## 3. Analisis Kritis

### 3.1 BUG — Login simpan token di **cookie biasa** (bukan httpOnly) — HIGH

`alpine-components.js` baris 30-35:
```js
const response = await window.apiClient.post('/v1/auth/login', this.formData);
const { token, user } = response.data.data;
if (token) {
    const maxAge = this.formData.remember ? 30 * 86400 : 86400;
    document.cookie = `token=${token}; path=/; max-age=${maxAge}; SameSite=Lax; Secure`;
}
window.auth.setUser(user, this.formData.remember);
```

**Masalah:**
- Token JWT disimpan di cookie **bisa diakses JavaScript** (`document.cookie`) → rentan XSS.
- Backend seharusnya yang set **httpOnly cookie** saat login (response `Set-Cookie`).
- `auth.js` baris 5 komentar: "Token is now stored in httpOnly cookie (set by backend)" — **tapi backend TIDAK melakukannya**.
- `apiClient` interceptor fallback baca cookie `token`/`zaku_token` → ini artinya frontend **harus** simpan token di cookie biasa.

**Perbaikan:** Backend (`AuthController::login`) harus return `Set-Cookie: zaku_token=...; HttpOnly; Secure; SameSite=Lax` dan **jangan** return token di body JSON. Frontend hapus `document.cookie` set token.

---

### 3.2 BUG — `apiClient` baseURL hardcode production di source — MEDIUM

`api-client.js` baris 6:
```js
baseURL: import.meta.env.VITE_API_BASE_URL || 'https://api-zaku.abysoft.my.id/api',
```

**Masalah:**
- Default fallback production URL → kalau developer lupa set `.env` (VITE_API_BASE_URL), development akan hit production API.
- Sebaiknya default `http://localhost:8000/api` atau `''` (same-origin) lalu di `.env` di-set per environment.

---

### 3.3 BUG — `JwtSession` middleware tidak ada di repo backend — MEDIUM

Frontend punya `app/Http/Middleware/JwtSession.php` yang validasi JWT dari cookie & set `auth()->setUser()`. Tapi **backend API** (`zaku-back-end`) pakai `JwtAuthenticate` middleware (header Authorization). Dua middleware berbeda → inkonsisten.

Jika frontend & backend shared domain (subdomain), cookie httpOnly bisa dipakai sama backend. Kalau beda domain (sekarang: frontend `zaku.abysoft.my.id`, backend `api-zaku.abysoft.my.id`), cookie `SameSite=Lax` tidak terkirim cross-site → **frontend harus pakai Authorization header**.

Saat ini frontend **pakai Authorization header** (dari cookie fallback di interceptor) → OK tapi redundant.

---

### 3.4 BUG — CORS `allowed_origins => ['*']` di frontend & backend — MEDIUM

`config/cors.php` (frontend & backend) sama: `'allowed_origins' => ['*']`, `supports_credentials: false`.

- `withCredentials: true` di `apiClient` tapi `supports_credentials: false` → **cookie tidak akan terkirim/terima** cross-origin.
- Kalau mau pakai cookie httpOnly cross-subdomain, butuh:
  - `allowed_origins` spesifik domain (bukan `*`)
  - `supports_credentials: true`
  - `SameSite=None; Secure` di cookie
  - Domain cookie `.abysoft.my.id` (parent domain)

Saat ini: frontend set cookie `domain=.abysoft.my.id` (baris 17 `auth.js`) tapi CORS tidak support credentials → **cookie tidak akan work cross-origin**.

---

### 3.5 BUG — Logout frontend call `/v1/auth/logout` tapi tidak hapus httpOnly cookie — LOW

`alpine-components.js` (profilePage) baris 978-983:
```js
async logout() {
    try { await window.apiClient.post('/v1/auth/logout'); }
    catch { /* ignore */ } finally { window.auth.clearToken(); }
}
```
`clearToken()` hapus localStorage & set cookie `token=; max-age=-1` tapi **tidak hapus httpOnly cookie** (karena JS tidak bisa baca httpOnly). Harus backend yang hapus via `Set-Cookie: zaku_token=; Max-Age=0; HttpOnly`.

---

### 3.6 KEAMANAN — CSP / Security Headers tidak ada — MEDIUM

- Tidak ada `Content-Security-Policy` header.
- Inline `<style>` 1600+ baris di `landing.blade.php` → CSP `style-src 'self' 'unsafe-inline'` perlu.
- Inline `<script>` di Blade views (Alpine init) → CSP `script-src 'self' 'unsafe-inline'`.
- `trustedTypes` / `nonce` belum dipakai.

---

### 3.7 PERFORMA — Landing page CSS inline 1600+ baris — LOW

`landing.blade.php` baris 48-1612: **semua CSS inline** di `<style>` tag.
- Tidak cacheable browser (html tidak cache lama).
- Ukuran HTML besar (~100KB+).
- Sebaiknya extract ke `public/css/landing.css` → load via `<link rel="stylesheet">` → cacheable, parallel load.

---

### 3.8 PERFORMA — Alpine components 1 file 1067 baris — LOW

`alpine-components.js` 1067 baris, semua komponen di satu file.
- Semua halaman load semua komponen (walau tidak dipakai).
- Sebaiknya code-split per halaman (Vite dynamic import) atau minimal pisah file per fitur.

---

### 3.9 KUALITAS — Error handling duplikat & inkonsisten — LOW

- `utils.js` punya `parseApiError`, `handleApiError`, `showToast`, `confirmDialog`.
- Komponen pakai `window.utils.parseApiError` / `handleApiError` / manual try-catch.
- Beberapa tempat hardcode pesan error Indonesia, beberapa pakai util.
- Standarisasi: selalu pakai `window.utils.handleApiError(error, 'fallback message')`.

---

### 3.10 KUALITAS — `chatPage` localStorage chat history tanpa batas — LOW

`alpine-components.js` baris 401-418: simpan semua chat ke `localStorage` (`zaku_chat_history`) tanpa pagination / limit.
- Bisa bloat localStorage (quota 5MB).
- Tambahkan limit (mis. 100 pesan terakhir) & auto-cleanup.

---

## 4. Keamanan Detail

| # | Temuan | Level | Catatan |
|---|--------|-------|---------|
| 1 | Token di cookie non-httpOnly (3.1) | **HIGH** | Rentan XSS, token bisa dicuri via `document.cookie` |
| 2 | CORS `*` + `withCredentials` mismatch (3.4) | **HIGH** | Cookie cross-origin tidak work, credentials bocor |
| 3 | Default API URL production di source (3.2) | **MEDIUM** | Dev bisa hit prod API |
| 4 | Tidak ada CSP / Security Headers (3.6) | **MEDIUM** | Inline style/script rentan XSS |
| 5 | Logout tidak hapus httpOnly cookie (3.5) | **LOW** | Sesi tetap hidup di browser sampai expired |
| 6 | `JwtSession` middleware duplikat backend (3.3) | **LOW** | Inkonsistensi arsitektur |
| 7 | Password visible di network tab (login) | **INFO** | HTTPS wajib; sudah `Secure` flag |

---

## 5. Performa & DX

| # | Temuan | Level | Catatan |
|---|--------|-------|---------|
| 1 | CSS inline 1600+ baris landing (3.7) | **MEDIUM** | Tidak cacheable, HTML besar |
| 2 | Alpine components monolith (3.8) | **LOW** | Semua load di setiap halaman |
| 3 | Vite dev server OK, production build `vite build` | **OK** | `package.json` scripts standar |
| 4 | Font Google Fonts (Syne, DM Mono, Fraunces) 3 font | **LOW** | Bisa self-host / preload |
| 5 | `landing.blade.php` 1959 baris (HTML+CSS+JS) | **LOW** | Sulit maintain, extract partials |
| 6 | Tidak ada test frontend (Jest/Vitest/Playwright) | **INFO** | Hanya backend test |

---

## 6. Fitur & UX

**Sudah bagus:**
- Landing page modern, branding kuat, animasi reveal, marquee, comparison table, testimonial, FAQ, pricing.
- Dashboard real-time dengan budget health, category breakdown, insight proaktif.
- Chat AI dengan quick chips, confirm card, persistent history.
- Transaction list: filter, search, sort, pagination, CSV export, detail modal.
- Profile: edit name/email, budget, export data, delete account.
- Toast notifications, confirm dialogs, loading states, retry buttons.
- Responsive mobile-first (CSS media queries lengkap).
- Accessibility: focus-visible, semantic HTML, aria-label partial.

**Bisa ditingkatkan:**
- Onboarding flow (halaman `/onboarding` redirect ke dashboard).
- Dark mode (CSS custom properties sudah rapi, mudah tambah).
- PWA (manifest, service worker) untuk installable.
- Offline support (service worker cache API GET).
- Keyboard shortcuts (cmd+k search, dll.).

---

## 7. Prioritas Enhancement (Rekomendasi)

### Prioritas 1 — Segera (Keamanan)
1. **Backend set httpOnly cookie saat login** — hapus token dari JSON response, frontend hapus `document.cookie` set token.
2. **Fix CORS** — `allowed_origins` spesifik domain, `supports_credentials: true`, cookie `SameSite=None; Secure; Domain=.abysoft.my.id`.
3. **Hapus default production URL** di `api-client.js` → default `''` (same-origin) atau localhost.
4. **Tambah CSP header** (via middleware atau web server).

### Prioritas 2 — Kualitas & Performa
5. **Extract landing CSS** ke file terpisah (`public/css/landing.css`), load via `<link>`.
6. **Split `alpine-components.js`** per fitur / halaman (Vite dynamic import).
7. **Standarisasi error handling** → selalu pakai `window.utils.handleApiError`.
8. **Limit chat history localStorage** (max 100 messages).

### Prioritas 3 — Fitur & UX
9. **Dark mode** (CSS variables sudah siap).
10. **PWA** (manifest, service worker).
11. **Onboarding flow** yang benar (bukan redirect).
12. **Test E2E** (Playwright/Cypress) untuk flow kritis: login → chat → dashboard.
13. **CI/CD** GitHub Actions (lint, build, test).

---

## 8. Checklist Cepat

- [ ] Backend: `Set-Cookie: zaku_token=...; HttpOnly; Secure; SameSite=Lax` di login
- [ ] Frontend: hapus `document.cookie = token=...` di `loginForm.submit()`
- [ ] Frontend: `apiClient` default baseURL `''` atau `import.meta.env.VITE_API_BASE_URL` tanpa fallback prod
- [ ] Frontend & Backend: `config/cors.php` → `allowed_origins: ['https://zaku.abysoft.my.id']`, `supports_credentials: true`
- [ ] Backend: logout → `Set-Cookie: zaku_token=; Max-Age=0; HttpOnly; Secure; SameSite=Lax`
- [ ] Frontend: extract landing CSS ke file
- [ ] Frontend: split alpine-components.js
- [ ] Frontend: tambah CSP meta tag / header
- [ ] Frontend: limit localStorage chat history
- [ ] CI: tambah GitHub Actions (composer, npm, test, build)

---

## 9. Limitasi Analisa Ini

- **Tidak menjalankan aplikasi** — hanya baca source statis.
- **Tidak test endpoint nyata** — backend API terpisah (`zaku-back-end` di `C:\Users\dhaby\zaku-back-end`).
- **Tidak cek production deployment** — hanya lihat config & env example.
- **Tidak audit dependency** (`npm audit`, `composer audit`) — `node_modules` & `vendor` ada tapi tidak di-scan.
- **Tidak cek database/frontend session** — hanya kode.

---

## 10. Ringkasan Arsitektur Lengkap (Frontend + Backend)

```
┌─────────────────────────────────────────────────────────────────┐
│                        USER BROWSER                             │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │  Laravel Blade (dompet-frontend)                        │   │
│  │  - PageController → render views                        │   │
│  │  - JwtSession middleware → validasi cookie JWT          │   │
│  └─────────────────────────────────────────────────────────┘   │
│                              │                                  │
│                              ▼                                  │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │  Alpine.js Components (SPA-like interactivity)          │   │
│  │  - apiClient (Axios) → Authorization: Bearer <token>    │   │
│  │  - Token dari cookie 'token' (fallback, NON-HTTPONLY)   │   │
│  │  - auth.js → user data di localStorage                  │   │
│  └─────────────────────────────────────────────────────────┘   │
└─────────────────────────────┬───────────────────────────────────┘
                              │ HTTPS /api/v1/*
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      BACKEND API (zaku-back-end)                │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │  Laravel API                                             │   │
│  │  - JwtAuthenticate middleware (header Authorization)    │   │
│  │  - AuthController::login → return JSON {token, user}    │   │
│  │  - CORS: allowed_origins: ['*'], supports_credentials: false │
│  └─────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
```

**Masalah arsitektur utama:** Frontend simpan token di cookie non-httpOnly (JS readable), backend return token di JSON, CORS tidak support credentials → **tidak aman untuk production cross-subdomain**. Perlu refactor auth flow ke httpOnly cookie pattern yang benar.