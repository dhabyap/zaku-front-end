# ISSUE-FE-009 — Verifikasi dan Lengkapi Ekspor Transaksi CSV

## 📋 Title
Pastikan ekspor transaksi CSV dari Profil lengkap, akurat, dan aman

## 🔴 Problem
Pemeriksaan kode menemukan menu **Export Data CSV** sudah ada pada Profil; jadi fitur ini bukan kosong. Namun export tampaknya dihasilkan di frontend dari transaksi yang dimuat, sehingga perlu dipastikan seluruh data/filter/halaman ikut ter-export dan format CSV aman dibuka di spreadsheet. Permintaan awal menyebut CSV/Excel; scope pertama adalah CSV yang sudah tersedia, bukan membuat Excel baru tanpa kebutuhan.

Bukti:
- `resources/views/dashboard/profile.blade.php` sekitar baris 68–72 memanggil `exportData()` dan menampilkan “Export Data CSV”.
- `resources/js/alpine-components.js` sekitar baris 770–780 menjalankan fungsi export.
- Backend memiliki `GET /export/monthly-pdf`, tetapi tidak ditemukan endpoint CSV di `routes/api.php`.

## ✅ Tujuan / Expected Result
- Kategori: **Frontend**, backend hanya bila ditemukan kebutuhan terverifikasi untuk mengambil seluruh transaksi secara aman.
- Jangan membuat ulang tombol/menu ekspor yang sudah ada.
- CSV berisi semua transaksi milik user, bukan hanya transaksi yang kebetulan sedang termuat di halaman.
- Header kolom jelas, encoding spreadsheet kompatibel, tanggal dan angka konsisten.
- Nilai teks yang dimulai karakter formula spreadsheet diekspor dengan mitigasi CSV formula injection.
- Jika aplikasi belum mampu mengambil seluruh transaksi, tampilkan batasan atau implementasikan paging fetch tanpa membebani API.
- Pertahankan kontrol akses: user hanya dapat mengekspor datanya sendiri.

## 📦 File yang harus diperiksa/diubah
| No | File | Aksi | Keterangan |
|---|---|---|---|
| 1 | `resources/views/dashboard/profile.blade.php` sekitar baris 68–72 | Verifikasi | Tombol export sudah ada; pertahankan kecuali label/aksesibilitas perlu perbaikan. |
| 2 | `resources/js/alpine-components.js` sekitar baris 713–785 | Ubah bila perlu | Telusuri `exportData()` dan sumber/limit data transaksi. |
| 3 | `resources/js/api-client.js` | Verifikasi | Gunakan Axios client dan auth yang sudah ada. |
| 4 | `routes/api.php` | Verifikasi/ubah bila perlu | `GET /transactions` tersedia; tidak ditemukan route CSV khusus. Buat route hanya jika pendekatan fetch penuh tidak layak. |
| 5 | `app/Http/Controllers/Api/TransactionController.php` | Ubah hanya bila perlu | Jika perlu endpoint export server-side, scope dan validasi user harus aman. |

## 🔧 Langkah-Langkah Implementasi

### Langkah 1 — Audit sumber data `exportData()`
**File:** `resources/js/alpine-components.js` sekitar baris 713–785.

Telusuri tepat bagaimana `exportData()` mendapatkan `txs`. Pastikan apakah mengambil seluruh data atau hanya state halaman Profil/daftar terbatas. Endpoint transaksi mendukung paginasi; jangan menganggap response halaman pertama berarti seluruh data.

### Langkah 2 — Tetapkan satu sumber kebenaran dan kelengkapan data
Jika transaksi di-fetch dari endpoint `/v1/transactions`:
- Verifikasi parameter page/per_page dan struktur response.
- Ambil semua halaman secara bertahap, dengan batas aman atau konfirmasi user jika dataset sangat besar.
- Hindari request paralel berlebihan.
- Alternatif server-side export hanya bila API pagination client tidak layak; laporkan alasan dan tambahkan tes authorization.

### Langkah 3 — Bentuk CSV yang konsisten
Sertakan kolom yang sudah tersedia dan berguna, minimal tanggal, deskripsi, tipe transaksi, kategori, nominal, serta catatan jika ada. Cocokkan nama field dengan model/response aktual; jangan membuat field fiktif.

- Gunakan quoting CSV yang benar untuk koma, tanda kutip, dan newline.
- Gunakan UTF-8; bila dibutuhkan agar Excel Windows membaca UTF-8, tambahkan BOM secara sengaja dan uji file.
- Gunakan tanggal ISO `YYYY-MM-DD` atau format yang jelas dan konsisten.
- Pertahankan nominal sebagai angka tanpa simbol mata uang dalam CSV.
- Mitigasi formula injection pada sel teks yang diawali `=`, `+`, `-`, atau `@` (terapkan sesuai parser spreadsheet dan tanpa merusak nilai nominal yang merupakan kolom angka).

### Langkah 4 — UX download dan kegagalan
- Disable tombol selama proses export dan beri label loading.
- Nama file menyertakan tanggal export, misalnya `zaku-transactions-YYYY-MM-DD.csv`.
- Empty state: jangan download file kosong; tampilkan info “Belum ada transaksi untuk diekspor”.
- Error API: tampilkan pesan aman dan tidak mengunduh file parsial seolah sukses.

### Langkah 5 — Pertahankan isolasi user
Jika ada endpoint baru, gunakan user dari middleware auth di server. Jangan menerima `user_id` dari query/body sebagai sumber data export. Tambahkan test yang memastikan user A tidak dapat mengekspor transaksi user B.

## 🧪 Cara Test
1. Jalankan `npm run build`.
2. Buat/siapkan lebih dari satu halaman transaksi; bandingkan jumlah CSV dengan total transaksi milik user.
3. Uji deskripsi yang berisi koma, tanda kutip, newline, karakter Indonesia, dan awalan formula spreadsheet.
4. Uji transaksi kosong; pastikan ada pesan dan tidak ada unduhan palsu.
5. Putuskan jaringan saat fetch halaman berikutnya; pastikan file parsial tidak ditandai sukses.
6. Jika endpoint backend ditambahkan: jalankan test authorization dan `php artisan test`.
7. Buka CSV dengan spreadsheet umum dan verifikasi kolom tidak bergeser serta karakter terbaca.

## ✔️ Acceptance Criteria
- [ ] Tombol Export Data CSV yang sudah ada tetap berfungsi.
- [ ] CSV mencakup seluruh transaksi user, bukan hanya subset di state saat ini.
- [ ] Kolom dan format tanggal/angka sesuai data aktual.
- [ ] CSV menangani koma, kutip, newline, Unicode, dan formula injection.
- [ ] Loading, empty, dan error state akurat.
- [ ] Download gagal tidak menghasilkan file parsial yang diklaim berhasil.
- [ ] User tidak dapat mengekspor data milik user lain.
- [ ] `npm run build` berhasil; test backend lulus jika backend diubah.

## 🔗 Reference
- Menu export: `resources/views/dashboard/profile.blade.php`
- Logic export: `resources/js/alpine-components.js` sekitar baris 713–785
- API transaksi: `routes/api.php`, `app/Http/Controllers/Api/TransactionController.php`
- API client: `resources/js/api-client.js`
