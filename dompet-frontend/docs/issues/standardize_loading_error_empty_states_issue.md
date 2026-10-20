# ISSUE-FE-011 — Standarkan Loading, Error, Empty, dan Retry State

## 📋 Title
Buat pola kondisi data konsisten untuk Transactions, Insights, Budgets, dan Profile

## 🔴 Problem
Pemeriksaan frontend menemukan pola loading/error tidak seragam. Contoh:
- `resources/views/components/loading-skeleton.blade.php` sudah menyediakan skeleton transaksi reusable.
- `resources/js/alpine-components.js` memiliki penanganan berbeda: Transactions sekitar baris 477–620, Profile sekitar 713–785, Budgets sekitar 861–965, Insights sekitar 1328–1350.
- Beberapa halaman menampilkan toast saat gagal, tetapi belum jelas apakah halaman selalu punya state error tetap dan tombol retry.
- Empty state juga perlu dibedakan dari error: data kosong yang sah tidak boleh disajikan seperti request gagal.

## ✅ Tujuan / Expected Result
- Kategori: **Frontend**.
- Halaman Transactions, Insights, Budgets, Profile punya pola loading, error, empty, dan retry yang konsisten.
- Error jaringan/API tidak menghapus data valid yang sudah terlihat bila refresh gagal.
- Tombol retry memanggil ulang request yang relevan dan mencegah double request.
- Skeleton hanya tampil saat loading awal; refresh tidak menyebabkan flash kosong yang tidak perlu.
- Pesan user ramah dan tidak menampilkan stack trace/API internals.
- Jangan mengubah desain atau perilaku bisnis halaman dalam issue ini.

## 📦 File yang harus diperiksa/diubah
| No | File | Aksi | Keterangan |
|---|---|---|---|
| 1 | `resources/views/components/loading-skeleton.blade.php` | Verifikasi/ubah | Skeleton transaksi reusable sudah ada; pertahankan bila sesuai. |
| 2 | `resources/js/alpine-components.js` sekitar 477–620 | Ubah | Transactions: state loading/error/empty/retry dan perilaku fetch. |
| 3 | `resources/js/alpine-components.js` sekitar 1328–1351 | Ubah | Insights: sudah memiliki `error` dan `refresh()`; gunakan untuk retry UI. |
| 4 | `resources/js/alpine-components.js` sekitar 861–965 | Ubah | Budgets: cek fetch/list, empty state, serta error/retry. |
| 5 | `resources/js/alpine-components.js` sekitar 713–785 | Ubah | Profile: cek fetch profile/export; tambahkan error state untuk load profile bila perlu. |
| 6 | `resources/views/dashboard/transactions.blade.php` | Ubah | Render state transaksi yang sesuai; jangan ubah fitur filter/pagination. |
| 7 | `resources/views/dashboard/insights.blade.php` | Ubah | Render error/retry/empty berdasarkan state yang ada. |
| 8 | `resources/views/dashboard/budgets.blade.php` | Ubah | Render loading/error/empty yang konsisten. |
| 9 | `resources/views/dashboard/profile.blade.php` | Ubah | Render state profil; jaga menu yang sudah ada. |
| 10 | `resources/css/app.css` / `resources/css/custom.css` | Ubah | Style reusable untuk error/empty/retry bila diperlukan. |

## 🔧 Langkah-Langkah Implementasi

### Langkah 1 — Audit state per halaman sebelum refactor
Buka masing-masing Alpine component dan Blade view. Buat tabel singkat untuk setiap halaman:
- sumber data/fungsi fetch;
- state `loading`, `error`, dan data;
- apakah ada empty state;
- apakah ada tombol retry;
- bagaimana state pagination/filter dipulihkan.

Jangan mengganti pola yang sudah berfungsi tanpa bukti. Contoh Insights sudah memiliki properti `error` serta method `refresh()`; pakai ulang, jangan buat request handler kedua.

### Langkah 2 — Definisikan state minimum dengan makna berbeda
Setiap halaman/section data minimal membedakan:
- **Initial loading:** belum ada data dan request pertama berjalan.
- **Loaded with data:** data tersedia.
- **Empty:** request sukses, hasil valid memang kosong.
- **Error:** request gagal; pesan aman dan aksi retry tersedia.
- **Refresh loading/error:** bila data lama tersedia, pertahankan data lama dan beri indikator kecil; jangan menggantinya dengan empty state.

Jangan menampilkan empty state hanya karena request gagal atau array belum terisi saat loading.

### Langkah 3 — Standarkan markup yang reusable
Buat partial/component sederhana bila cocok dengan pola Blade proyek, misalnya `components/data-state.blade.php`, atau gunakan markup per view bila prop Alpine tidak mudah diteruskan. Hindari over-engineering.

Komponen/markup harus mendukung:
- label loading yang dapat diumumkan (`aria-live="polite"` bila sesuai);
- pesan empty yang spesifik halaman;
- pesan error tanpa detail sensitif;
- tombol retry semantik (`type="button"`) dengan disabled/loading state.

Jangan mengganti skeleton transaksi yang ada jika telah memenuhi fungsi.

### Langkah 4 — Pastikan retry memanggil fungsi yang sama
- Transactions: retry harus memanggil ulang fetch transaksi dengan filter/pagination yang sedang aktif.
- Insights: gunakan method `refresh()`/`fetchInsights()` yang sudah ada.
- Budgets: gunakan fungsi fetch budget yang sudah ada; jangan membuat state dataset terpisah.
- Profile: retry hanya memuat data profil; jangan menjalankan aksi export atau update.

Tangani `loading` dengan `try/finally`. Cegah klik berulang saat request berlangsung. Jika API mengembalikan 401, ikuti auth interceptor/redirect yang sudah ada; jangan menunjukkan retry tanpa akhir.

### Langkah 5 — Pesan aman dan observabilitas
Gunakan pesan singkat seperti “Data belum dapat dimuat. Coba lagi.” Simpan detail teknis hanya di console/log yang aman untuk debugging tanpa membocorkan token/PII. Jangan tampilkan JSON exception, file path server, atau stack trace ke layar.

### Langkah 6 — Uji filter dan navigasi
Pastikan error saat fetch dengan filter tidak menghapus filter. Setelah retry sukses, hasil sesuai query/filter yang sama. Pastikan empty result tetap menampilkan empty state, bukan error.

## 🧪 Cara Test
1. Jalankan `npm run build`.
2. Untuk tiap halaman (Transactions, Insights, Budgets, Profile), uji request sukses berisi data.
3. Uji sukses dengan daftar kosong; pastikan empty state jelas dan berbeda dari error.
4. Simulasikan offline/500; pastikan error message dan tombol retry muncul.
5. Tekan retry; pastikan request diulang sekali dan state pulih pada sukses.
6. Simulasikan kegagalan refresh setelah data sudah tampil; pastikan data lama tidak hilang tanpa alasan.
7. Pada Transactions, uji retry saat filter/tanggal/page bukan default; pastikan parameter tetap dipakai.
8. Uji double-click retry dan loading; tidak boleh ada request ganda yang tidak disengaja.
9. Periksa console untuk error baru dan pastikan tidak ada stack trace/token di UI.
10. Uji viewport mobile dan desktop serta navigasi keyboard ke tombol retry.

## ✔️ Acceptance Criteria
- [ ] Empat halaman memiliki state initial loading, data, empty, error, dan retry yang benar.
- [ ] Empty hanya tampil setelah request sukses dengan hasil kosong.
- [ ] Error tidak ditampilkan sebagai empty dan tidak memperlihatkan stack trace.
- [ ] Tombol retry memanggil fungsi fetch halaman yang sama.
- [ ] Retry mempertahankan filter/pagination halaman Transactions.
- [ ] Data lama tidak dibuang pada kegagalan refresh jika masih aman dipakai.
- [ ] Retry/loading mencegah request ganda yang tidak disengaja.
- [ ] Skeleton reusable yang sudah ada tetap dipakai bila sesuai.
- [ ] Semua state bisa diakses dengan keyboard dan fokus terlihat.
- [ ] `npm run build` berhasil tanpa error baru.

## 🔗 Reference
- Skeleton: `resources/views/components/loading-skeleton.blade.php`
- Transactions component: `resources/js/alpine-components.js` sekitar baris 477–620
- Profile component: `resources/js/alpine-components.js` sekitar baris 713–785
- Budgets component: `resources/js/alpine-components.js` sekitar baris 861–965
- Insights component: `resources/js/alpine-components.js` sekitar baris 1328–1351
- Views: `resources/views/dashboard/transactions.blade.php`, `insights.blade.php`, `budgets.blade.php`, `profile.blade.php`
