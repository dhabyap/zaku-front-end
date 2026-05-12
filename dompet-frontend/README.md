# 💳 Zaku DOMPET — Frontend Application

Zaku DOMPET adalah aplikasi dompet digital modern dengan desain **Brutalist**. Dibangun menggunakan Laravel Blade, Alpine.js, dan Axios.

## 🚀 Persiapan Cepat (Quick Start)

### 1. Prasyarat
- PHP >= 8.1
- Composer
- Node.js & NPM

### 2. Instalasi
Clone repository dan masuk ke direktori proyek:
```bash
cd dompet-frontend
composer install
npm install
```

### 3. Konfigurasi Environment
Salin file `.env.example` ke `.env` dan sesuaikan URL API:
```bash
cp .env.example .env
php artisan key:generate
```
Edit `.env` dan pastikan:
```env
VITE_API_BASE_URL=https://api.dompet.com/api
```

### 4. Menjalankan Server Pengembangan
Anda perlu menjalankan dua perintah di terminal terpisah:

**Terminal 1 (Laravel):**
```bash
php artisan serve
```

**Terminal 2 (Vite Assets):**
```bash
npm run dev
```

> [!IMPORTANT]
> Jika Anda tidak menjalankan `npm run dev`, CSS dan JavaScript (Vite) tidak akan muncul di browser.

### 5. Build untuk Produksi
Jika Anda ingin menjalankan aplikasi tanpa menjalankan server Vite dev, Anda harus melakukan build aset:
```bash
npm run build
```

---

## 🛠️ Tech Stack
- **Framework:** Laravel 10
- **Frontend Logic:** Alpine.js
- **API Client:** Axios
- **Styling:** Custom Vanilla CSS (Brutalist Style)
- **Icons:** Heroicons (SVG)

## 📁 Struktur Folder Utama
- `resources/views/auth`: Halaman Login, Register, Forgot Password.
- `resources/views/dashboard`: Halaman utama, riwayat transaksi, profil.
- `resources/views/wallet`: Fitur Top Up, Kirim Uang, Tarik Saldo.
- `resources/js/api-client.js`: Konfigurasi Axios & JWT Interceptors.
- `resources/js/auth.js`: Helper manajemen token & autentikasi.
