# 💸 Zaku — Dompet Digital dengan AI Chat

[![Laravel](https://img.shields.io/badge/Laravel-10-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC6EC?style=for-the-badge&logo=alpine.js&logoColor=white)](https://alpinejs.dev)
[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

> **Track · Control · Thrive**

Zaku adalah aplikasi dompet digital yang membantu kamu mengelola keuangan pribadi dengan cara yang **mudah, cepat, dan menyenangkan**. Dilengkapi dengan **AI Chat** yang bisa mencatat transaksi hanya dengan mengetik pesan sehari-hari seperti *"makan siang 35rb"*.

Dibangun dengan desain **Brutalist** — tebal, kontras, dan berani.

![Zaku Screenshot](https://via.placeholder.com/800x400/FF4D00/111010?text=Zaku+DOMPET+Screenshot)

---

## ✨ Fitur

| Fitur | Deskripsi |
|---|---|
| 🔐 **Autentikasi** | Login, Register, Verifikasi Email, Lupa Password |
| 💰 **Cek Saldo** | Lihat saldo, pemasukan & pengeluaran bulan ini |
| 📝 **Catat Transaksi** | Catat pemasukan & pengeluaran dengan mudah |
| 🤖 **AI Chat** | Ketik transaksi dalam bahasa sehari-hari, AI yang akan mencatat |
| 📊 **Riwayat** | Lihat semua transaksi dengan filter kategori & bulan |
| 💳 **Top Up** | Isi saldo dompet |
| 📤 **Kirim Uang** | Kirim saldo ke pengguna lain |
| 🏦 **Tarik Saldo** | Cairkan saldo ke rekening bank |
| 👤 **Profil** | Kelola profil, atur budget bulanan, export data CSV |

---

## 🎨 Design System

Zaku menggunakan desain **Brutalist** dengan karakter yang kuat:

### Color Palette

| Warna | Hex | Penggunaan |
|---|---|---|
| 🖤 Ink | `#111010` | Background gelap, teks utama |
| 🤍 Paper | `#F5F0E8` | Background terang |
| 🤎 Cream | `#EDE8DC` | Background sekunder |
| 🧡 Punch | `#FF4D00` | CTA, tombol utama |
| 💛 Punch-2 | `#FFD600` | Aksen sekunder |
| 💚 Mint | `#00E5A0` | Status sukses |
| 💙 Sky | `#C8F0FF` | Status info |

### Typography

| Font | Kegunaan |
|---|---|
| **Syne** | Heading, judul |
| **DM Mono** | Angka, label, metadata |
| **Fraunces** | Aksen, angka besar |

---

## 🚀 Tech Stack

| Teknologi | Versi | Kegunaan |
|---|---|---|
| **Laravel** | 10.x | Backend framework, Blade templating, routing |
| **Alpine.js** | 3.x | Interaktivitas frontend tanpa React/Vue |
| **Axios** | 1.x | HTTP client untuk komunikasi API |
| **Vite** | 5.x | Build tool, bundling CSS/JS |
| **CSS Vanilla** | - | Styling custom (Brutalist design) |
| **Heroicons** | - | Icon SVG |

---

## 📋 Prasyarat

Sebelum memulai, pastikan sudah terinstall:

- **PHP** >= 8.1
- **Composer** (manajer package PHP)
- **Node.js** >= 18 & **npm**
- **MySQL** atau MariaDB (untuk backend API)
- **Git**

Cek instalasi:

```bash
php -v
composer --version
node -v
npm -v
git --version
```

---

## 🛠️ Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/dhabyap/zaku-front-end.git
cd zaku-front-end
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Buka file `.env` dan sesuaikan konfigurasi:

```env
APP_NAME=Zaku
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

# URL Backend API (WAJIB diisi)
VITE_API_BASE_URL=http://127.0.0.1:8001/api
VITE_API_TIMEOUT=30000
```

> ⚠️ **PENTING:** Zaku membutuhkan Backend API terpisah. Tanpa backend, hanya halaman statis dan AI Chat (dengan parser lokal) yang bisa berjalan.

### 4. Jalankan Aplikasi

Buka **dua terminal** terpisah:

**Terminal 1 — Laravel Server:**
```bash
php artisan serve
```
Akses di: `http://127.0.0.1:8000`

**Terminal 2 — Vite Dev Server:**
```bash
npm run dev
```

> Tanpa `npm run dev`, tampilan akan rusak karena CSS & JS tidak termuat.

### 5. Build untuk Produksi

```bash
npm run build
```

Setelah build, cukup jalankan `php artisan serve` saja.

---

## 📁 Struktur Folder

```
dompet-frontend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── PageController.php        # Render semua halaman
│   │   │   └── Api/
│   │   │       └── ChatController.php    # Proxy AI chat + parser lokal
│   │   └── Middleware/
│   └── Models/
│       └── User.php
├── resources/
│   ├── css/
│   │   └── app.css                       # Semua styling (Brutalist)
│   ├── js/
│   │   ├── app.js                        # Entry point Alpine.js
│   │   ├── api-client.js                 # Axios + JWT interceptor
│   │   ├── auth.js                       # Manajemen token
│   │   └── utils.js                      # Helper (formatRupiah, toast, dll)
│   └── views/
│       ├── layouts/                      # app.blade.php, guest.blade.php
│       ├── auth/                         # Login, Register, Verify, Forgot
│       ├── dashboard/                    # Home, Transaksi, Detail, Profil
│       ├── wallet/                       # Top Up, Kirim, Tarik
│       ├── chat/                         # AI Chat
│       └── components/                   # Navbar, Header, Toast, Modal
├── routes/
│   ├── web.php                           # Route halaman frontend
│   └── api.php                           # Route API internal
├── public/                               # File publik
├── .env                                  # Konfigurasi environment
├── .env.example                          # Template .env
├── composer.json
├── package.json
└── vite.config.js
```

---

## 🗺️ Routes

| URL | Halaman | Auth |
|---|---|---|
| `/login` | Login | ❌ |
| `/register` | Registrasi | ❌ |
| `/forgot-password` | Lupa password | ❌ |
| `/dashboard` | Beranda | ✅ |
| `/transactions` | Riwayat transaksi | ✅ |
| `/transactions/{id}` | Detail transaksi | ✅ |
| `/wallet/topup` | Top up saldo | ✅ |
| `/wallet/withdraw` | Tarik saldo | ✅ |
| `/wallet/send` | Kirim uang | ✅ |
| `/chat` | AI Chat | ✅ |
| `/profile` | Profil & pengaturan | ✅ |

---

## 🔧 Troubleshooting

### Halaman polos tanpa CSS/JS
```bash
npm run dev
# atau untuk production:
npm run build
```

### 401 Unauthorized terus-menerus
- Pastikan backend API berjalan di `VITE_API_BASE_URL`
- Cek token di `localStorage` masih valid

### Composer install gagal
Pastikan PHP >= 8.1 dan ekstensi berikut aktif:
`BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML`

### npm install gagal
```bash
rm -rf node_modules package-lock.json
npm install
```

---

## 📝 Development

### Menambahkan Halaman Baru

1. Buat view di `resources/views/`
2. Daftarkan route di `routes/web.php`
3. Tambahkan method di `PageController.php`

### Menambahkan Komponen

Buat file baru di `resources/views/components/` dan include dengan:
```blade
@include('components.nama-komponen')
```

---

## 🤝 Kontribusi

Kontribusi sangat dipersilakan! Berikut cara berkontribusi:

1. Fork repository ini
2. Buat branch fitur (`git checkout -b fitur/amazing-feature`)
3. Commit perubahan (`git commit -m 'feat: tambah amazing feature'`)
4. Push ke branch (`git push origin fitur/amazing-feature`)
5. Buat Pull Request

---

## 📄 Lisensi

Hak cipta © 2026 Zaku. Semua hak dilindungi.

---

## 📞 Kontak

- **GitHub:** [@dhabyap](https://github.com/dhabyap)
- **Repository:** https://github.com/dhabyap/zaku-front-end

---

<div align="center">
  <strong>Dibuat dengan ❤️ menggunakan Laravel + Alpine.js</strong>
</div>
