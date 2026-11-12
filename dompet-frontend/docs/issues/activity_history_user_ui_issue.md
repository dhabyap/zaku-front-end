# ISSUE-FULLSTACK-002 — Tampilkan Riwayat Aktivitas Akun

## 📋 Title
Sediakan halaman riwayat aktivitas akun yang aman dan berguna untuk pengguna

## 🔴 Problem
Audit menemukan fondasi activity log sudah ada: model `ActivityLog`, pencatat event, migration, middleware, dan endpoint `GET /analytics/activity`. Jadi pekerjaan ini **bukan** membuat tabel/model/endpoint dasar dari awal. Yang perlu dipastikan ialah cakupan event mana yang benar-benar tercatat dan menyediakan UI pengguna untuk melihat aktivitas miliknya sendiri.

Bukti:
- Backend `app/Models/ActivityLog.php` mempunyai `log()`, `logPageView()`, dan `logFeature()`.
- Backend `app/Http/Controllers/Api/AnalyticsController.php` memiliki method `activity()` yang memfilter `user_id` berdasarkan user terautentikasi dan memberi pagination.
- Backend `routes/api.php` telah mendaftarkan `GET /analytics/activity` di grup `jwt.auth`.
- Frontend belum ditemukan halaman pengguna yang memanggil endpoint tersebut.

## ✅ Tujuan / Expected Result
- Kategori: **Full-stack verifikasi + Frontend**; backend API hanya diubah bila ada kekurangan yang terbukti.
- Pengguna dapat melihat aktivitas akunnya sendiri secara kronologis dengan paginasi.
- Event yang tampil ramah pengguna dan tidak membocorkan IP, user-agent, token, atau data sensitif.
- Pengguna lain tidak dapat membaca aktivitas akun ini.
- Tampilkan loading, empty, error/retry dan tombol muat lebih banyak/paginasi.
- Jangan menampilkan seluruh page view berulang sebagai aktivitas penting; pilih event bermakna seperti transaksi dibuat/diubah/dihapus dan perubahan akun/budget bila pencatatannya tersedia.

## 📦 File yang harus diperiksa/diubah
Backend: `D:/Latihan/Zaku/backend`
| No | File | Aksi | Keterangan |
|---|---|---|---|
| 1 | `routes/api.php` sekitar baris 98–101 | Verifikasi saja | `GET /analytics/activity` sudah terdaftar dan berada di auth group. Jangan menambah route duplikat. |
| 2 | `app/Http/Controllers/Api/AnalyticsController.php` method `activity()` | Verifikasi/ubah bila ada gap | Filter owner dan pagination sudah ada; verifikasi batas `per_page` dan payload. |
| 3 | `app/Models/ActivityLog.php` | Verifikasi | Model menyediakan pencatat activity; periksa sanitasi field dan event yang masuk. |
| 4 | `app/Http/Middleware/TrackActivity.php` | Verifikasi | Pastikan middleware hanya mencatat event yang sesuai dan menghindari noise/sensitif. |
| 5 | Transaction/User/Budget controllers/services | Ubah hanya bila perlu | Tambahkan pencatatan event bermakna yang terbukti belum ada. Jangan mencatat payload/token/password penuh. |

Frontend: `D:/Latihan/Zaku/frontend/dompet-frontend`
| No | File | Aksi | Keterangan |
|---|---|---|---|
| 6 | `routes/web.php` | Ubah | Tambah halaman authenticated `/activity` bila tidak ada route UI serupa. |
| 7 | `app/Http/Controllers/PageController.php` | Ubah | Handler halaman bila mengikuti pola page route sekarang. |
| 8 | `resources/views/dashboard/activity.blade.php` | Buat | Daftar aktivitas dengan tanggal, label, deskripsi aman, paginasi/load more. |
| 9 | `resources/js/alpine-components.js` | Ubah | Fetch `/v1/analytics/activity` dengan `window.apiClient`; state loading/error/empty. |
| 10 | `resources/views/components/desktop-navigation.blade.php` atau Profile | Integrasi seperlunya | Letakkan link di lokasi yang mudah ditemukan tanpa menambah item bottom nav mobile. |

## 🔧 Langkah-Langkah Implementasi

### Langkah 1 — Audit event yang masuk sebelum membuat UI
Baca `app/Http/Middleware/TrackActivity.php`, cari pemanggilan `ActivityLog::log`, `logPageView`, `logFeature` pada controllers/services, dan lihat migration `2026_07_02_100000_add_soft_deletes_and_activity_logs.php`.

Buat daftar event aktual dan bedakan:
- event yang bermakna untuk pengguna (transaksi dibuat/diubah/dihapus, profil/budget diubah);
- event teknis/noise (page view berulang, request background);
- field sensitif yang tidak boleh ditampilkan (IP, user-agent, credential, token, raw request body).

Jangan menampilkan data sebelum daftar ini disepakati.

### Langkah 2 — Verifikasi dan harden endpoint yang sudah tersedia
**File:** backend `app/Http/Controllers/Api/AnalyticsController.php`, method `activity()`; route di `routes/api.php` sekitar baris 100.

Endpoint sudah memfilter `user_id` dari auth. Verifikasi:
- `per_page` memiliki batas maksimum dan nilai minimum yang aman;
- response hanya menyertakan field yang diperlukan UI;
- `old_values`/`new_values` tidak mengandung password, token, alamat IP atau user-agent untuk response user;
- urutan terbaru dahulu stabil, dengan tie-breaker ID bila timestamp sama.

Jangan mengubah method admin `dashboard()` atau endpoint analytics admin sebagai bagian issue ini.

### Langkah 3 — Buat normalisasi label event yang aman
Tampilkan label Indonesia yang dikenal, misalnya “Transaksi dibuat”. Untuk event yang belum punya mapping, gunakan label fallback generik yang aman; jangan menampilkan nama class internal/model secara mentah. Render deskripsi dan data sebagai teks ter-escape, bukan `x-html`.

Buat allowlist field yang boleh ditampilkan per event. Jangan merender seluruh JSON `old_values`/`new_values` sebagai dump.

### Langkah 4 — Buat halaman frontend
Ikuti pola Blade+Alpine pada halaman `resources/views/dashboard/changelogs.blade.php` atau halaman daftar sejenis, tetapi jangan menyalin makna changelog ke activity pribadi.

Tampilkan:
- waktu penuh dengan format yang konsisten;
- label event yang ramah pengguna;
- deskripsi singkat;
- perubahan relevan yang sudah disanitasi bila tersedia;
- pagination atau “Muat lebih banyak”.

Sertakan loading skeleton, empty state (“Belum ada aktivitas”), error dengan retry, dan indikator saat memuat halaman berikutnya. Jangan menampilkan stack trace.

### Langkah 5 — Tambah pencatatan event yang kurang hanya berdasarkan audit
Jika event penting belum pernah dicatat, tambahkan di service/controller yang bertanggung jawab dengan pola `ActivityLog::log(...)` yang ada. Pastikan pencatatan tidak menggagalkan transaksi bisnis utama kecuali memang harus atomic; putuskan secara eksplisit.

Jangan mencatat setiap request API/page view sebagai notifikasi/aktivitas pengguna. Jangan menyimpan password, token, session id, atau seluruh request payload.

### Langkah 6 — Tambahkan route halaman dan akses navigasi
Tambahkan route UI `/activity` di grup `jwt.session`. Aksesnya melalui Profile, desktop navigation, atau menu akun yang tersedia. Jangan menambah item ke bottom navigation empat item sebagai scope implisit.

## 🧪 Cara Test
Backend:
1. Jalankan `php artisan test`.
2. User A membuat activity; endpoint A menampilkan eventnya.
3. User B memanggil endpoint; response hanya memuat activity B, tidak ada data A.
4. Uji `per_page` invalid/terlalu besar; pastikan dibatasi/ditolak sesuai pola API.
5. Uji payload dengan nilai sensitif; pastikan field tidak dikembalikan ke user.

Frontend:
1. Jalankan `npm run build`.
2. Uji akun tanpa activity, dengan satu halaman activity, dan lebih dari satu halaman.
3. Uji loading, offline/500, retry, dan navigasi load more.
4. Uji string berisi HTML; pastikan tampil sebagai teks, tidak dieksekusi.
5. Uji mobile/desktop dan akses via keyboard.

## ✔️ Acceptance Criteria
- [ ] Halaman activity menampilkan event bermakna terbaru milik user login.
- [ ] Endpoint `GET /analytics/activity` yang ada dipakai; tidak ada API duplikat.
- [ ] Pagination memiliki batas aman dan bisa digunakan UI.
- [ ] Data user lain tidak dapat dibaca.
- [ ] Password, token, IP, user-agent, raw payload sensitif tidak tampil di UI/response.
- [ ] Label event dikenal diterjemahkan; event tak dikenal punya fallback yang aman.
- [ ] Loading, empty, error/retry, dan load-more/pagination tersedia.
- [ ] HTML pada deskripsi tidak dieksekusi.
- [ ] Mobile bottom navigation tetap empat item.
- [ ] `php artisan test` dan `npm run build` lulus jika backend/frontend terkait berubah.

## 🔗 Reference
- API route: backend `routes/api.php` sekitar baris 98–101
- Controller: backend `app/Http/Controllers/Api/AnalyticsController.php`
- Model: backend `app/Models/ActivityLog.php`
- Middleware: backend `app/Http/Middleware/TrackActivity.php`
- Migration: backend `database/migrations/2026_07_02_100000_add_soft_deletes_and_activity_logs.php`
- Frontend API client: `resources/js/api-client.js`
- Similar paginated UI pattern: `resources/views/dashboard/changelogs.blade.php`
