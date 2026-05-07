param (
    [string]$Repo = "dhabyap/zaku-front-end"
)

$PRD_REF = "PRD-Frontend.md"
$PHASE_0 = "Phase 0 — Project Setup"
$PHASE_1 = "Phase 1 — API Client & Utilities"
$PHASE_2 = "Phase 2 — Layout & Komponen Dasar"
$PHASE_3 = "Phase 3 — Halaman Autentikasi"
$PHASE_4 = "Phase 4 — Dashboard"
$PHASE_5 = "Phase 5 — Fitur Wallet"
$PHASE_6 = "Phase 6 — Laravel Routing"
$PHASE_7 = "Phase 7 — Testing & Polish"
$tmpFile = "$env:TEMP\gh_issue_body.md"

function New-Issue($title, $label, $body) {
    $body | Out-File -FilePath $tmpFile -Encoding utf8
    gh issue create --repo $Repo --title $title --label $label --body-file $tmpFile
    Start-Sleep -Milliseconds 500
}

# ─── PHASE 0: Project Setup ────────────────────────────────────────────────

New-Issue "[TASK-02] Buat file konfigurasi environment" "phase:setup" @"
## $PHASE_0

**Task:** Setup file `.env` dengan konfigurasi API

## Steps
- [ ] Copy `.env.example` ke `.env`
- [ ] Isi `VITE_API_BASE_URL` dengan URL backend API
- [ ] Jalankan `php artisan key:generate`

## Config yang diperlukan
```env
APP_NAME="DOMPET Frontend"
APP_ENV=production
APP_DEBUG=false
VITE_API_BASE_URL=https://api.dompet.com/api
VITE_API_TIMEOUT=30000
```

## References
PRD: $PRD_REF — Environment Configuration section
"@

New-Issue "[TASK-03] Setup folder struktur views" "phase:setup" @"
## $PHASE_0

**Task:** Buat semua folder views sesuai struktur PRD

## Steps
- [ ] Buat folder: `resources/views/auth/`
- [ ] Buat folder: `resources/views/dashboard/`
- [ ] Buat folder: `resources/views/wallet/`
- [ ] Buat folder: `resources/views/components/`
- [ ] Buat folder: `resources/views/layouts/`

## References
PRD: $PRD_REF — Folder Structure section
"@

New-Issue "[TASK-04] Setup file JS & CSS utama" "phase:setup" @"
## Phase 0 — Project Setup

**Task:** Buat file JS dan CSS pondasi aplikasi

## Steps
- [ ] Buat `resources/js/api-client.js` (axios instance + interceptor)
- [ ] Buat `resources/js/auth.js` (helper autentikasi)
- [ ] Buat `resources/js/utils.js` (fungsi-fungsi helper umum)
- [ ] Buat `resources/css/app.css` (copy design tokens dari dompet-design.html)
- [ ] Buat `resources/css/custom.css`

## References
PRD: $PRD_REF — Design System section
"@

# ─── PHASE 1: API Client & Utilities ──────────────────────────────────────

New-Issue "[TASK-05] Buat api-client.js (Axios instance & interceptors)" "phase:api" @"
## Phase 1 — API Client & Utilities

**Task:** Buat Axios instance terpusat dengan JWT interceptor

## Steps
- [ ] Buat instance axios dengan `baseURL` dari `import.meta.env.VITE_API_BASE_URL`
- [ ] Set default header `Content-Type: application/json`
- [ ] Tambah **request interceptor** — sisipkan JWT token dari `localStorage`
- [ ] Tambah **response interceptor** — handle error 401 (token expired)
- [ ] Handle auto-refresh token jika `refresh_token` tersedia

## File
`resources/js/api-client.js`

## Notes
> IMPORTANT: Semua request API ke backend harus melalui instance ini, bukan axios langsung

## References
PRD: $PRD_REF — API Integration section
"@

New-Issue "[TASK-06] Buat auth.js (authentication helper)" "phase:api" @"
## Phase 1 — API Client & Utilities

**Task:** Buat helper functions untuk manajemen autentikasi dan token

## Steps
- [ ] Fungsi `getToken()` — ambil access token dari `localStorage`
- [ ] Fungsi `setToken(access, refresh)` — simpan token ke `localStorage`
- [ ] Fungsi `clearToken()` — hapus semua token dan redirect ke `/login`
- [ ] Fungsi `isLoggedIn()` — return true jika token ada dan valid
- [ ] Fungsi `getUser()` — ambil data user dari `sessionStorage`

## File
`resources/js/auth.js`

## References
PRD: $PRD_REF — Storage section (localStorage JWT tokens)
"@

New-Issue "[TASK-07] Buat utils.js (helper functions)" "phase:api" @"
## Phase 1 — API Client & Utilities

**Task:** Buat fungsi-fungsi helper umum yang dipakai di seluruh aplikasi

## Steps
- [ ] Fungsi `formatRupiah(amount)` — format angka ke format Rupiah (Rp 100.000)
- [ ] Fungsi `showToast(type, message)` — trigger notifikasi toast (success/error/info)
- [ ] Fungsi `formatDate(date)` — format tanggal ke bahasa Indonesia

## File
`resources/js/utils.js`

## References
PRD: $PRD_REF — Design System section
"@

# ─── PHASE 2: Layout & Components ─────────────────────────────────────────

New-Issue "[TASK-08] Buat layout guest.blade.php" "phase:layout" @"
## Phase 2 — Layout & Komponen Dasar

**Task:** Buat layout dasar untuk halaman yang belum login (auth pages)

## Steps
- [ ] Load Alpine.js dan Axios via Vite
- [ ] Load CSS utama (`app.css`, `custom.css`)
- [ ] Sisipkan `@yield('content')` di body
- [ ] Tidak ada navigasi — hanya shell kosong

## File
`resources/views/layouts/guest.blade.php`

## References
PRD: $PRD_REF — Folder Structure section
"@

New-Issue "[TASK-09] Buat layout app.blade.php" "phase:layout" @"
## Phase 2 — Layout & Komponen Dasar

**Task:** Buat layout utama untuk halaman setelah login

## Steps
- [ ] Load Alpine.js, Axios, dan semua JS helpers via Vite
- [ ] Include komponen header (`@include('components.header')`)
- [ ] Include komponen navigation (`@include('components.navigation')`)
- [ ] Include komponen toast notification
- [ ] Sisipkan `@yield('content')` di area utama
- [ ] Tambah logika guard: jika token tidak ada di localStorage, redirect ke `/login`

## File
`resources/views/layouts/app.blade.php`

## Notes
> WARNING: Guard redirect harus jalan SEBELUM halaman di-render

## References
PRD: $PRD_REF — Folder Structure section
"@

New-Issue "[TASK-10] Buat komponen navigation.blade.php" "phase:layout" @"
## Phase 2 — Layout & Komponen Dasar

**Task:** Buat bottom navigation bar dengan 4 menu

## Steps
- [ ] Buat 4 menu: Home, Transaksi, Wallet, Profil
- [ ] Gunakan ikon (heroicons atau emoji)
- [ ] Active state berdasarkan route yang sedang aktif (`request()->is(...)`)
- [ ] Posisi fixed di bagian bawah layar

## File
`resources/views/components/navigation.blade.php`

## Design Tokens
- Warna aktif: `var(--punch)` (#FF4D00)
- Background: `var(--ink)` (#111010)

## References
PRD: $PRD_REF — Design System section
"@

New-Issue "[TASK-11] Buat komponen header.blade.php" "phase:layout" @"
## Phase 2 — Layout & Komponen Dasar

**Task:** Buat header atas yang menampilkan nama user

## Steps
- [ ] Tampilkan nama user dari `sessionStorage` via Alpine.js
- [ ] Gunakan font Syne (display font dari design system)
- [ ] Desain sesuai design token: warna `var(--ink)`, background `var(--paper)`

## File
`resources/views/components/header.blade.php`

## References
PRD: $PRD_REF — Design System / Typography section
"@

New-Issue "[TASK-12] Buat komponen toast-notification.blade.php" "phase:layout" @"
## Phase 2 — Layout & Komponen Dasar

**Task:** Buat komponen toast notification untuk feedback aksi user

## Steps
- [ ] Tipe toast: `success`, `error`, `info`
- [ ] Posisi: pojok atas tengah atau kanan layar
- [ ] Auto-hilang setelah 3 detik
- [ ] Gunakan Alpine.js untuk animasi show/hide (`x-show`, `x-transition`)
- [ ] Dipanggil via fungsi `showToast(type, message)` dari `utils.js`

## File
`resources/views/components/toast-notification.blade.php`

## Design Tokens
- Success: `var(--mint)` (#00E5A0)
- Error: `var(--punch)` (#FF4D00)
- Info: `var(--sky)` (#C8F0FF)

## References
PRD: $PRD_REF — Components section
"@

New-Issue "[TASK-13] Buat komponen loading-skeleton.blade.php" "phase:layout" @"
## Phase 2 — Layout & Komponen Dasar

**Task:** Buat placeholder skeleton loading untuk saat data API belum datang

## Steps
- [ ] Buat skeleton berbentuk card (lebar penuh, tinggi ~80px)
- [ ] Gunakan animasi shimmer/pulse
- [ ] Bisa dipakai ulang di halaman dashboard dan transaksi

## File
`resources/views/components/loading-skeleton.blade.php`

## References
PRD: $PRD_REF — Components section
"@

# ─── PHASE 3: Authentication Pages ────────────────────────────────────────

New-Issue "[TASK-14] Buat halaman Login" "phase:auth" @"
## Phase 3 — Halaman Autentikasi

**Task:** Implementasi halaman login dengan form dan integrasi API

## Route
`GET /login`

## File
`resources/views/auth/login.blade.php`

## Steps
- [ ] Extend layout `layouts.guest`
- [ ] Form: input email, input password, checkbox remember me
- [ ] Validasi client-side: format email, password tidak kosong
- [ ] Tombol submit dengan loading state (`:disabled="loading"`)
- [ ] Panggil API `POST /api/auth/login`
- [ ] Jika sukses: simpan `access_token` & `refresh_token` ke localStorage, simpan user ke sessionStorage, redirect ke `/dashboard`
- [ ] Jika gagal: tampilkan pesan error via `showToast('error', message)`
- [ ] Link ke `/register` dan `/forgot-password`

## API
`POST /api/auth/login`

## References
PRD: $PRD_REF — Phase 1: Login Page section
"@

New-Issue "[TASK-15] Buat halaman Register" "phase:auth" @"
## Phase 3 — Halaman Autentikasi

**Task:** Implementasi halaman registrasi akun baru

## Route
`GET /register`

## File
`resources/views/auth/register.blade.php`

## Steps
- [ ] Extend layout `layouts.guest`
- [ ] Form: nama lengkap, email, password, konfirmasi password
- [ ] Validasi: password cocok, minimal 8 karakter
- [ ] Tombol submit dengan loading state
- [ ] Panggil API `POST /api/auth/register`
- [ ] Jika sukses: redirect ke `/verify-email`
- [ ] Jika gagal: tampilkan error via toast

## API
`POST /api/auth/register`

## References
PRD: $PRD_REF — Phase 1: Register Page section
"@

New-Issue "[TASK-16] Buat halaman Verifikasi Email" "phase:auth" @"
## Phase 3 — Halaman Autentikasi

**Task:** Implementasi halaman instruksi verifikasi email

## Route
`GET /verify-email`

## File
`resources/views/auth/verify-email.blade.php`

## Steps
- [ ] Extend layout `layouts.guest`
- [ ] Tampilkan pesan instruksi cek inbox email
- [ ] Tombol 'Kirim Ulang Email Verifikasi'
- [ ] Panggil API resend verification jika tersedia

## References
PRD: $PRD_REF — Folder Structure section
"@

New-Issue "[TASK-17] Buat halaman Lupa Password" "phase:auth" @"
## Phase 3 — Halaman Autentikasi

**Task:** Implementasi halaman forgot password

## Route
`GET /forgot-password`

## File
`resources/views/auth/forgot-password.blade.php`

## Steps
- [ ] Extend layout `layouts.guest`
- [ ] Form: input email
- [ ] Tombol submit dengan loading state
- [ ] Panggil API forgot password
- [ ] Tampilkan pesan sukses setelah submit berhasil

## API
`POST /api/auth/forgot-password`

## References
PRD: $PRD_REF — Folder Structure section
"@

# ─── PHASE 4: Dashboard ────────────────────────────────────────────────────

New-Issue "[TASK-18] Buat halaman Home Dashboard" "phase:dashboard" @"
## Phase 4 — Dashboard

**Task:** Implementasi halaman utama dashboard setelah login

## Route
`GET /dashboard`

## File
`resources/views/dashboard/home.blade.php`

## Steps
- [ ] Extend layout `layouts.app`
- [ ] Tampilkan saldo wallet (format Rupiah via `formatRupiah()`)
- [ ] Tampilkan 3 tombol aksi cepat: Top Up, Tarik, Kirim
- [ ] Tampilkan 5 transaksi terakhir dalam list mini
- [ ] Gunakan loading skeleton saat data sedang di-fetch
- [ ] Panggil API `GET /api/wallet/balance`
- [ ] Panggil API `GET /api/transactions?limit=5`

## API
- `GET /api/wallet/balance`
- `GET /api/transactions?limit=5`

## References
PRD: $PRD_REF — Dashboard section
"@

New-Issue "[TASK-19] Buat halaman Daftar Transaksi" "phase:dashboard" @"
## Phase 4 — Dashboard

**Task:** Implementasi halaman list semua transaksi

## Route
`GET /transactions`

## File
`resources/views/dashboard/transactions.blade.php`

## Steps
- [ ] Extend layout `layouts.app`
- [ ] Tampilkan semua transaksi dalam list/card
- [ ] Setiap item: tanggal, deskripsi, jumlah (merah=keluar, hijau=masuk)
- [ ] Implementasi pagination atau infinite scroll
- [ ] Panggil API `GET /api/transactions`

## API
`GET /api/transactions`

## Design Tokens
- Keluar (debit): `var(--punch)` (#FF4D00)
- Masuk (kredit): `var(--mint)` (#00E5A0)

## References
PRD: $PRD_REF — Dashboard section
"@

New-Issue "[TASK-20] Buat halaman Detail Transaksi" "phase:dashboard" @"
## Phase 4 — Dashboard

**Task:** Implementasi halaman detail satu transaksi

## Route
`GET /transactions/{id}`

## File
`resources/views/dashboard/transaction-detail.blade.php`

## Steps
- [ ] Extend layout `layouts.app`
- [ ] Ambil ID transaksi dari URL parameter
- [ ] Tampilkan: tanggal, jumlah, tipe, deskripsi, status transaksi
- [ ] Tombol kembali ke `/transactions`
- [ ] Panggil API `GET /api/transactions/{id}`

## API
`GET /api/transactions/{id}`

## References
PRD: $PRD_REF — Dashboard section
"@

New-Issue "[TASK-21] Buat halaman Profil" "phase:dashboard" @"
## Phase 4 — Dashboard

**Task:** Implementasi halaman profil user

## Route
`GET /profile`

## File
`resources/views/dashboard/profile.blade.php`

## Steps
- [ ] Extend layout `layouts.app`
- [ ] Tampilkan nama dan email user dari `sessionStorage`
- [ ] Tombol Logout: panggil `clearToken()` lalu redirect ke `/login`
- [ ] Panggil API `GET /api/auth/me` untuk validasi data user terkini

## API
`GET /api/auth/me`

## References
PRD: $PRD_REF — Dashboard section
"@

# ─── PHASE 5: Wallet Features ──────────────────────────────────────────────

New-Issue "[TASK-22] Buat halaman Top Up" "phase:wallet" @"
## Phase 5 — Fitur Wallet

**Task:** Implementasi halaman top up saldo

## Route
`GET /wallet/topup`

## File
`resources/views/wallet/topup.blade.php`

## Steps
- [ ] Extend layout `layouts.app`
- [ ] Form input jumlah top up (angka)
- [ ] Tombol pilihan nominal cepat: Rp 50.000, Rp 100.000, Rp 200.000, Rp 500.000
- [ ] Tombol submit dengan loading state
- [ ] Panggil API `POST /api/wallet/topup`
- [ ] Tampilkan konfirmasi sukses atau pesan error via toast

## API
`POST /api/wallet/topup`

## References
PRD: $PRD_REF — Wallet section
"@

New-Issue "[TASK-23] Buat halaman Tarik Saldo" "phase:wallet" @"
## Phase 5 — Fitur Wallet

**Task:** Implementasi halaman penarikan saldo ke rekening bank

## Route
`GET /wallet/withdraw`

## File
`resources/views/wallet/withdraw.blade.php`

## Steps
- [ ] Extend layout `layouts.app`
- [ ] Form: input jumlah tarik, input nomor rekening tujuan
- [ ] Validasi: jumlah tidak boleh melebihi saldo yang tersedia
- [ ] Tombol submit dengan loading state
- [ ] Panggil API `POST /api/wallet/withdraw`
- [ ] Tampilkan konfirmasi sukses atau error via toast

## API
`POST /api/wallet/withdraw`

## References
PRD: $PRD_REF — Wallet section
"@

New-Issue "[TASK-24] Buat halaman Kirim Uang" "phase:wallet" @"
## Phase 5 — Fitur Wallet

**Task:** Implementasi halaman transfer uang ke sesama pengguna

## Route
`GET /wallet/send`

## File
`resources/views/wallet/send-money.blade.php`

## Steps
- [ ] Extend layout `layouts.app`
- [ ] Form: email/ID penerima, jumlah, catatan (opsional)
- [ ] Validasi: penerima tidak boleh sama dengan pengirim
- [ ] Tombol submit dengan loading state
- [ ] Panggil API `POST /api/wallet/send`
- [ ] Tampilkan konfirmasi sukses atau error via toast

## API
`POST /api/wallet/send`

## References
PRD: $PRD_REF — Wallet section
"@

# ─── PHASE 6: Routing ─────────────────────────────────────────────────────

New-Issue "[TASK-25] Setup routes/web.php" "phase:routing" @"
## Phase 6 — Laravel Routing

**Task:** Daftarkan semua route frontend ke web.php

## File
`routes/web.php`

## Steps
- [ ] Daftarkan semua route berikut:

| Route | Method | Controller@Method |
|---|---|---|
| `/login` | GET | PageController@login |
| `/register` | GET | PageController@register |
| `/verify-email` | GET | PageController@verifyEmail |
| `/forgot-password` | GET | PageController@forgotPassword |
| `/dashboard` | GET | PageController@home |
| `/transactions` | GET | PageController@transactions |
| `/transactions/{id}` | GET | PageController@transactionDetail |
| `/profile` | GET | PageController@profile |
| `/wallet/topup` | GET | PageController@topup |
| `/wallet/withdraw` | GET | PageController@withdraw |
| `/wallet/send` | GET | PageController@sendMoney |

## References
PRD: $PRD_REF — Routing section
"@

New-Issue "[TASK-26] Buat PageController.php" "phase:routing" @"
## Phase 6 — Laravel Routing

**Task:** Buat controller tunggal untuk render semua halaman

## File
`app/Http/Controllers/PageController.php`

## Steps
- [ ] Buat class `PageController extends Controller`
- [ ] Setiap method hanya me-return view yang sesuai
- [ ] Method yang diperlukan:
  - `login()`, `register()`, `verifyEmail()`, `forgotPassword()`
  - `home()`, `transactions()`, `transactionDetail($id)`, `profile()`
  - `topup()`, `withdraw()`, `sendMoney()`

## Notes
> Controller ini hanya render view, TIDAK ada logic bisnis — semua logic ada di Alpine.js / JS

## References
PRD: $PRD_REF — Controllers section
"@

# ─── PHASE 7: Testing & Polish ────────────────────────────────────────────

New-Issue "[TASK-27] Test semua halaman di browser" "phase:testing" @"
## Phase 7 — Testing & Polish

**Task:** Manual testing seluruh halaman dan alur aplikasi

## Steps
- [ ] Buka setiap halaman, pastikan tidak ada error di console JavaScript
- [ ] Test alur login: isi form → submit → redirect ke dashboard
- [ ] Test alur register → verify email
- [ ] Test semua form wallet (topup, withdraw, send)
- [ ] Pastikan token tersimpan di localStorage setelah login
- [ ] Pastikan token terhapus setelah logout
- [ ] Test akses halaman dashboard tanpa login — harus redirect ke `/login`

## Notes
> Gunakan DevTools (F12) untuk cek localStorage, Network, dan Console
"@

New-Issue "[TASK-28] Cek responsivitas mobile" "phase:testing" @"
## Phase 7 — Testing & Polish

**Task:** Verifikasi tampilan di layar mobile

## Steps
- [ ] Buka DevTools → Toggle Device Toolbar (Ctrl+Shift+M)
- [ ] Set viewport ke 390x844 (iPhone 14)
- [ ] Pastikan semua halaman nyaman di layar HP (max-width 420px)
- [ ] Pastikan bottom navigation tidak menimpa konten
- [ ] Pastikan tidak ada horizontal scroll

## Design Constraint
Layout shell: `max-width: 420px; height: 100dvh`

## References
PRD: $PRD_REF — Layout section
"@

New-Issue "[TASK-29] Review dan audit design system" "phase:testing" @"
## Phase 7 — Testing & Polish

**Task:** Audit konsistensi penggunaan design system di seluruh halaman

## Steps
- [ ] Pastikan semua warna menggunakan CSS variable (`var(--ink)`, `var(--punch)`, dll) — TIDAK hardcode hex
- [ ] Pastikan font Syne dan DM Mono ter-load dari Google Fonts
- [ ] Pastikan shadow `--bs`, `--bs-lg`, `--bs-xl` diterapkan pada card dan button
- [ ] Pastikan tidak ada warna merah/biru/hijau generik yang tidak sesuai palette

## Design Tokens
```css
--ink:     #111010;
--paper:   #F5F0E8;
--cream:   #EDE8DC;
--punch:   #FF4D00;
--punch-2: #FFD600;
--mint:    #00E5A0;
--sky:     #C8F0FF;
```

## References
PRD: $PRD_REF — Design System section
"@

Write-Host "Done! All 29 issues created." -ForegroundColor Green
Remove-Item $tmpFile -ErrorAction SilentlyContinue
