# 📋 DOMPET Frontend — Task List

> **Untuk:** Junior Frontend Developer  
> **Stack:** Laravel Blade + Alpine.js + Axios  
> **Referensi PRD:** `PRD-Frontend.md`  
> **Status Legend:** `[ ]` Belum · `[/]` Sedang dikerjakan · `[x]` Selesai

---

## 🚀 PHASE 0 — Project Setup

- [x] **TASK-01** · Install Laravel project baru
  - Jalankan `laravel new dompet-frontend`
  - Install NPM packages: `npm install alpinejs axios`

- [x] **TASK-02** · Buat file konfigurasi environment
  - Copy `.env.example` → `.env`
  - Isi `VITE_API_BASE_URL` dengan URL backend API
  - Jalankan `php artisan key:generate`

- [ ] **TASK-03** · Setup folder struktur views
  - Buat folder: `resources/views/auth/`
  - Buat folder: `resources/views/dashboard/`
  - Buat folder: `resources/views/wallet/`
  - Buat folder: `resources/views/components/`
  - Buat folder: `resources/views/layouts/`

- [x] **TASK-04** · Setup file JS & CSS utama
  - Buat `resources/js/api-client.js` (axios instance + interceptor)
  - Buat `resources/js/auth.js` (helper autentikasi)
  - Buat `resources/js/utils.js` (fungsi-fungsi helper umum)
  - Buat `resources/css/app.css` (copy dari `dompet-design.html`)
  - Buat `resources/css/custom.css`

---

## 🔌 PHASE 1 — API Client & Utilities

- [ ] **TASK-05** · Buat `api-client.js`
  - Buat instance axios dengan `baseURL` dari env
  - Set default header `Content-Type: application/json`
  - Tambah request interceptor → sisipkan JWT token dari `localStorage`
  - Tambah response interceptor → handle error 401 (token expired)
  - Handle auto-refresh token jika ada `refresh_token`

- [ ] **TASK-06** · Buat `auth.js`
  - Fungsi `getToken()` → ambil dari `localStorage`
  - Fungsi `setToken(access, refresh)` → simpan ke `localStorage`
  - Fungsi `clearToken()` → hapus semua token & redirect ke `/login`
  - Fungsi `isLoggedIn()` → cek apakah token ada
  - Fungsi `getUser()` → ambil data user dari `sessionStorage`

- [ ] **TASK-07** · Buat `utils.js`
  - Fungsi `formatRupiah(amount)` → format angka ke format Rupiah (Rp 100.000)
  - Fungsi `showToast(type, message)` → tampilkan notifikasi toast
  - Fungsi `formatDate(date)` → format tanggal ke bahasa Indonesia

---

## 🏗️ PHASE 2 — Layout & Komponen Dasar

- [ ] **TASK-08** · Buat layout `layouts/guest.blade.php`
  - Layout untuk halaman yang belum login (auth pages)
  - Load Alpine.js & Axios dari CDN atau dari Vite
  - Load CSS utama
  - Sisipkan `@yield('content')`

- [ ] **TASK-09** · Buat layout `layouts/app.blade.php`
  - Layout untuk halaman yang sudah login (dashboard, wallet, dll)
  - Include komponen navigasi bawah
  - Include komponen header atas
  - Include komponen toast notification
  - Sisipkan `@yield('content')`
  - Tambah logika cek login: jika token tidak ada → redirect ke `/login`

- [ ] **TASK-10** · Buat komponen `components/navigation.blade.php`
  - Bottom navigation bar dengan 4 menu: Home, Transaksi, Wallet, Profil
  - Gunakan ikon (bisa pakai emoji atau heroicons)
  - Active state berdasarkan route yang sedang aktif

- [ ] **TASK-11** · Buat komponen `components/header.blade.php`
  - Tampilkan nama user dari `sessionStorage`
  - Tampilkan tombol notifikasi (opsional)
  - Desain sesuai design system (font Syne, warna --ink)

- [ ] **TASK-12** · Buat komponen `components/toast-notification.blade.php`
  - Toast muncul di bagian atas layar
  - Tipe: `success`, `error`, `info`
  - Auto-hilang setelah 3 detik
  - Gunakan Alpine.js untuk animasi show/hide

- [ ] **TASK-13** · Buat komponen `components/loading-skeleton.blade.php`
  - Skeleton loading placeholder berbentuk card
  - Digunakan saat data API sedang di-fetch

---

## 🔐 PHASE 3 — Halaman Autentikasi

- [ ] **TASK-14** · Buat halaman **Login** (`auth/login.blade.php`)
  - Route: `GET /login`
  - Form: input email, input password, checkbox remember me
  - Validasi client-side: email format, password tidak kosong
  - Tombol submit dengan loading state
  - Panggil API `POST /api/auth/login`
  - Jika sukses → simpan token, redirect ke `/dashboard`
  - Jika gagal → tampilkan pesan error lewat toast
  - Link ke halaman Register & Forgot Password

- [ ] **TASK-15** · Buat halaman **Register** (`auth/register.blade.php`)
  - Route: `GET /register`
  - Form: nama lengkap, email, password, konfirmasi password
  - Validasi: password match, minimal 8 karakter
  - Panggil API `POST /api/auth/register`
  - Jika sukses → redirect ke `/verify-email`
  - Jika gagal → tampilkan error

- [ ] **TASK-16** · Buat halaman **Verifikasi Email** (`auth/verify-email.blade.php`)
  - Route: `GET /verify-email`
  - Tampilkan instruksi untuk cek email
  - Tombol "Kirim Ulang Email Verifikasi"
  - Panggil API resend verification jika tersedia

- [ ] **TASK-17** · Buat halaman **Lupa Password** (`auth/forgot-password.blade.php`)
  - Route: `GET /forgot-password`
  - Form: input email
  - Panggil API forgot password
  - Tampilkan pesan sukses setelah submit

---

## 🏠 PHASE 4 — Dashboard

- [ ] **TASK-18** · Buat halaman **Home Dashboard** (`dashboard/home.blade.php`)
  - Route: `GET /dashboard`
  - Gunakan layout `app.blade.php`
  - Tampilkan saldo wallet (ambil dari API, format Rupiah)
  - Tampilkan 3 tombol aksi cepat: Top Up, Tarik, Kirim
  - Tampilkan 5 transaksi terakhir (list mini)
  - Gunakan loading skeleton saat data sedang dimuat
  - Panggil API `GET /api/wallet/balance`
  - Panggil API `GET /api/transactions?limit=5`

- [ ] **TASK-19** · Buat halaman **Daftar Transaksi** (`dashboard/transactions.blade.php`)
  - Route: `GET /transactions`
  - Tampilkan semua transaksi dalam list/card
  - Setiap item: tanggal, deskripsi, jumlah (merah=keluar, hijau=masuk)
  - Bisa scroll infinite atau pagination
  - Panggil API `GET /api/transactions`

- [ ] **TASK-20** · Buat halaman **Detail Transaksi** (`dashboard/transaction-detail.blade.php`)
  - Route: `GET /transactions/{id}`
  - Tampilkan detail lengkap satu transaksi
  - Tampilkan: tanggal, jumlah, tipe, deskripsi, status
  - Tombol kembali ke daftar transaksi
  - Panggil API `GET /api/transactions/{id}`

- [ ] **TASK-21** · Buat halaman **Profil** (`dashboard/profile.blade.php`)
  - Route: `GET /profile`
  - Tampilkan nama dan email user
  - Form edit nama (opsional untuk v1)
  - Tombol Logout → panggil `clearToken()` dan redirect ke `/login`
  - Panggil API `GET /api/auth/me`

---

## 💰 PHASE 5 — Fitur Wallet

- [ ] **TASK-22** · Buat halaman **Top Up** (`wallet/topup.blade.php`)
  - Route: `GET /wallet/topup`
  - Form input jumlah top up
  - Pilihan nominal cepat (Rp 50k, 100k, 200k, 500k)
  - Tombol submit dengan loading state
  - Panggil API `POST /api/wallet/topup`
  - Tampilkan konfirmasi sukses atau error

- [ ] **TASK-23** · Buat halaman **Tarik Saldo** (`wallet/withdraw.blade.php`)
  - Route: `GET /wallet/withdraw`
  - Form input jumlah tarik & nomor rekening tujuan
  - Validasi: jumlah tidak boleh melebihi saldo
  - Panggil API `POST /api/wallet/withdraw`
  - Tampilkan konfirmasi atau error

- [ ] **TASK-24** · Buat halaman **Kirim Uang** (`wallet/send-money.blade.php`)
  - Route: `GET /wallet/send`
  - Form input: email/ID penerima, jumlah, catatan (opsional)
  - Validasi: penerima tidak boleh sama dengan pengirim
  - Panggil API `POST /api/wallet/send`
  - Tampilkan konfirmasi atau error

---

## 🛣️ PHASE 6 — Routing Laravel

- [ ] **TASK-25** · Setup `routes/web.php`
  - Daftarkan semua route yang dibutuhkan:
    ```
    GET /login           → PageController@login
    GET /register        → PageController@register
    GET /verify-email    → PageController@verifyEmail
    GET /forgot-password → PageController@forgotPassword
    GET /dashboard       → PageController@home
    GET /transactions    → PageController@transactions
    GET /transactions/{id} → PageController@transactionDetail
    GET /profile         → PageController@profile
    GET /wallet/topup    → PageController@topup
    GET /wallet/withdraw → PageController@withdraw
    GET /wallet/send     → PageController@sendMoney
    ```

- [ ] **TASK-26** · Buat `PageController.php`
  - Satu controller untuk return semua view
  - Method per halaman yang hanya `return view('...')`

---

## ✅ PHASE 7 — Testing & Polish

- [ ] **TASK-27** · Test semua halaman di browser
  - Pastikan tidak ada error console JavaScript
  - Pastikan semua form bisa di-submit dan menerima respon API
  - Pastikan token tersimpan dan terhapus dengan benar

- [ ] **TASK-28** · Cek responsivitas mobile
  - Semua halaman harus nyaman di layar HP (max-width 420px)
  - Bottom nav tidak tertutup konten

- [ ] **TASK-29** · Review design system
  - Pastikan warna menggunakan CSS variable dari design system
  - Pastikan font Syne & DM Mono ter-load dari Google Fonts
  - Pastikan shadow `--bs`, `--bs-lg`, `--bs-xl` diterapkan pada card/button

---

## 📌 Catatan Penting untuk Developer

> [!NOTE]
> Selalu gunakan CSS variable dari design system, jangan hardcode warna.

> [!IMPORTANT]
> JWT token disimpan di `localStorage`. Pastikan setiap request API menyertakan header `Authorization: Bearer <token>`.

> [!WARNING]
> Jangan lupa handle kasus token expired (HTTP 401). Arahkan user kembali ke halaman login.

---

**Total Tasks:** 29  
**Estimasi:** ~5–7 hari kerja (full-time junior developer)
