# Zaku Frontend

Frontend web aplikasi **Zaku** — pelacak keuangan pribadi dengan fitur AI chat. Dibangun menggunakan Laravel Blade + Vite sebagai tampilan (*view layer*) yang berkomunikasi dengan backend API terpisah.

## Tech Stack

- **Laravel** — routing, Blade templating, middleware
- **Blade Templates** — server-side rendering untuk halaman dan komponen
- **Alpine.js** — interaktivitas frontend (dropdown, modal, state management)
- **Vite** — build tool untuk JavaScript dan CSS
- **Custom CSS** — gaya Tailwind-inspired tanpa framework CSS penuh

## Fitur

| Fitur | Deskripsi |
|---|---|
| **Landing Page** | Halaman publik dengan statistik umum dari `/api/v1/stats/public` |
| **Dashboard** | Ringkasan keuangan untuk pengguna terautentikasi |
| **Riwayat Transaksi** | Daftar transaksi dengan filter dan pencarian |
| **Detail Transaksi** | Lihat detail dan edit transaksi |
| **AI Chat** | Chat dengan AI asisten keuangan |
| **Profil** | Kelola data profil pengguna |
| **Changelogs** | Catatan perubahan/rilis aplikasi |
| **Print Struk** | Cetak struk thermal 80mm dengan format rapi untuk transaksi |

## Screenshots

### Landing Page

**Desain landing page:** warna utama hitam, aksen oranye (#FF5C00), layout bold dengan hero section, mockup HP di kanan, marquee teks berjalan, navigasi sticky di atas. Headline besar: "Catat duit, ngobrol aja."

### Login Page

**Halaman login:** form login di tengah dengan background putih kekrem, toggler password "LIHAT", tombol "MASUK SEKARANG →" oranye, tombol "BUAT AKUN BARU" outlined, link demo access di bawah.

## Setup Local

### Prasyarat

- PHP 8.1+
- Composer
- Node.js 18+ & npm

### Instalasi

```bash
# Clone repository
git clone <repo-url>
cd dompet-frontend

# Install dependencies PHP
composer install

# Konfigurasi environment
cp .env.example .env

# Edit .env — set URL backend API (contoh: http://localhost:8000)
# VITE_API_BASE_URL=http://localhost:8000
php artisan key:generate

# Install dependencies JavaScript
npm install

# Jalankan development server
npm run dev
```

Dalam terminal terpisah:

```bash
php artisan serve
```

Buka `http://localhost:8000` di browser.

## Variabel Environment Penting

| Variable | Deskripsi | Default |
|---|---|---|
| `VITE_API_BASE_URL` | URL base backend API | `http://localhost:8000` |

> **Penting untuk produksi:** `VITE_API_BASE_URL` harus menggunakan **HTTPS**. Browser memblokir mixed content (HTTP request dari halaman HTTPS), sehingga semua komunikasi API akan gagal jika menggunakan HTTP.

## Komunikasi API

Frontend berkomunikasi dengan backend API melalui tiga modul JavaScript utama di `resources/js/`:

### `api-client.js`

Instance axios dengan interceptor JWT:
- Menyertakan header `Authorization: Bearer <token>` di setiap request
- Mengecek masa berlaku token sebelum request
- Redirect ke halaman login jika token expired (response 401)

### `auth.js`

Manajemen token autentikasi:
- `getToken()` — ambil token dari localStorage
- `setToken(token)` — simpan token
- `clearToken()` — hapus token (logout)
- `isTokenExpired()` — cek apakah token sudah kedaluwarsa
- `isLoggedIn()` — cek status login

### `alpine-components.js`

Komponen Alpine.js untuk setiap halaman:
- `homePage` — halaman dashboard
- `transactionsPage` — daftar transaksi
- `transactionDetail` — detail/edit transaksi
- `chatPage` — AI chat
- `profilePage` — profil pengguna
- `changelogPage` — changelogs

### `utils.js`

Fungsi utilitas:
- `handleApiError()` — tangani error dari API
- `showToast()` — tampilkan notifikasi toast
- `formatNumber()` — format angka (rupiah)

## Struktur Folder

```
dompet-frontend/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
│   └── build/              # Aset Vite yang sudah di-build
├── resources/
│   ├── css/                # Custom CSS
│   ├── js/
│   │   ├── api-client.js   # Axios instance dengan JWT interceptor
│   │   ├── auth.js         # Manajemen token autentikasi
│   │   ├── alpine-components.js  # Komponen Alpine.js per halaman
│   │   └── utils.js        # Fungsi utilitas (error, toast, format)
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php     # Layout utama (authenticated)
│       ├── landing.blade.php     # Halaman landing publik
│       ├── dashboard/
│       │   ├── home.blade.php
│       │   ├── transactions.blade.php
│       │   ├── transaction-detail.blade.php
│       │   ├── profile.blade.php
│       │   └── changelogs.blade.php
│       ├── auth/
│       │   ├── login.blade.php
│       │   ├── register.blade.php
│       │   ├── verify-email.blade.php
│       │   └── forgot-password.blade.php
│       └── components/
│           ├── navigation.blade.php
│           ├── header.blade.php
│           ├── toast-notification.blade.php
│           ├── confirm-modal.blade.php
│           └── loading-skeleton.blade.php
├── routes/
├── .env.example
├── composer.json
├── package.json
└── vite.config.js
```

## Deployment Produksi (Shared Hosting)

Frontend ini mendukung deploy ke **shared hosting** tanpa perlu instalasi Node.js/npm di server.

### Langkah Build (Lokal)

```bash
# Build aset untuk produksi
npm run build
```

Ini menghasilkan folder `public/build/` berisi JavaScript dan CSS yang sudah di-minify.

**Commit folder `public/build/` ke repository:**

```bash
git add public/build/
git commit -m "build: update production assets"
git push
```

### Langkah Deploy (Server)

```bash
# Di server shared hosting
git pull origin main

# Clear cache Laravel
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

> **Catatan:** Tidak perlu menjalankan `npm install` atau `npm run build` di server. Aset sudah di-build lokal dan di-commit ke repository. Cukup `git pull` untuk mengambil aset terbaru.

### Alur Deployment

1. **Lokal** — push kode + `public/build/` ke repository
2. **Server** — `git pull` + clear cache
3. Selesai

## Landing Page

`resources/views/landing.blade.php` adalah halaman publik yang berdiri sendiri (tidak memerlukan autentikasi). Halaman ini mengambil data statistik dari endpoint:

```
GET {VITE_API_BASE_URL}/api/v1/stats/public
```

Data yang ditampilkan meliputi jumlah pengguna terdaftar, total transaksi, dan statistik umum lainnya.

## API Backend

Backend API berjalan di server terpisah:

- **Development:** `http://localhost:8000` atau sesuai `.env`
- **Produksi:** `https://api-zaku.abysoft.my.id/api/v1/*`

Semua komunikasi menggunakan format JSON dengan autentikasi JWT (Bearer token).
