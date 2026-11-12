# ISSUE-FULLSTACK-001 — Fitur Sasaran Tabungan dengan Progres

## 📋 Title
Tambahkan sasaran tabungan personal dengan target nominal dan progres

## 🔴 Problem
Zaku sudah mendukung budget pengeluaran dan progres budget, tetapi audit belum menemukan entitas/API untuk sasaran tabungan yang berdiri sendiri. Budget membatasi belanja; sasaran tabungan membantu pengguna menetapkan tujuan (misalnya dana darurat atau barang tertentu), target nominal, tenggat opsional, dan progres terkumpul.

Bukti hasil audit:
- `routes/api.php` memiliki CRUD `/budgets` dan endpoint progress budget.
- Belum ditemukan `SavingGoal`/`Goal` model, migration, atau route sasaran tabungan.
- Tidak ditemukan route/model untuk multiple wallets. **Jangan mengimplementasikan multiple wallets.**

## ✅ Tujuan / Expected Result
- Kategori: **Full-stack** (Laravel API + Blade/Alpine UI).
- Pengguna dapat membuat, melihat, mengubah, menyelesaikan, dan menghapus sasaran tabungan milik sendiri.
- Sasaran memiliki nama, target nominal positif, nominal terkumpul, tanggal target opsional, dan status.
- Progres/remaining dihitung konsisten dan tidak dapat bernilai di bawah nol/di atas target tanpa aturan yang jelas.
- Sasaran tidak otomatis mengubah saldo, transaksi, atau budget; pencatatan kontribusi harus eksplisit dan tercatat dengan benar.
- Semua data terikat user terautentikasi; tidak ada akses silang antar user.

## 📦 File yang harus dibuat/diubah
Backend repo: `D:/Latihan/Zaku/backend`
| No | File | Aksi | Keterangan |
|---|---|---|---|
| 1 | `database/migrations/<timestamp>_create_saving_goals_table.php` | Buat | Tabel sasaran dengan `user_id`, nama, target, terkumpul, tanggal opsional, status, timestamps. Migration baru; jangan edit migration lama. |
| 2 | `app/Models/SavingGoal.php` | Buat | Relasi user dan casts/fillable yang aman. |
| 3 | `app/Http/Requests/Api/StoreSavingGoalRequest.php` | Buat bila mengikuti pola | Validasi input create. |
| 4 | `app/Http/Requests/Api/UpdateSavingGoalRequest.php` | Buat bila mengikuti pola | Validasi perubahan sasaran. |
| 5 | `app/Http/Controllers/Api/SavingGoalController.php` | Buat | Index/store/show/update/delete dan aksi kontribusi/status sesuai kontrak API. |
| 6 | `routes/api.php` | Ubah | Tambahkan route di dalam `jwt.auth`; jangan ubah middleware global. |
| 7 | Tests API/model/factory | Buat | Ownership, validasi, progres, status, dan operasi gagal. |

Frontend repo: `D:/Latihan/Zaku/frontend/dompet-frontend`
| No | File | Aksi | Keterangan |
|---|---|---|---|
| 8 | `routes/web.php` | Ubah | Tambah halaman authenticated `/saving-goals` menggunakan pola `PageController`. |
| 9 | `app/Http/Controllers/PageController.php` | Ubah | Tambah handler page jika memang diperlukan pola aplikasi. |
| 10 | `resources/views/dashboard/saving-goals.blade.php` | Buat | Daftar, form create/edit, empty/loading/error state. |
| 11 | `resources/js/alpine-components.js` | Ubah | Logic API memakai `window.apiClient`, validasi dan refresh list. |
| 12 | `resources/views/components/navigation.blade.php` atau halaman desktop nav | Integrasi seperlunya | Jangan menambah item bottom-nav mobile tanpa keputusan; letakkan tautan di navigasi fitur/desktop yang sudah sesuai. |

## 🔧 Langkah-Langkah Implementasi

### Langkah 1 — Verifikasi pola repo dan sepakati kontrak data
Baca `Budget` model/controller/request/migrations dan pola CRUD `RecurringTransactionController`. Pastikan direktori Request yang benar serta response envelope `ApiResponse`. Jangan menyalin kontrak budget karena sasaran tabungan berbeda.

Kontrak minimal:
- `name`: wajib, teks non-kosong, panjang dibatasi.
- `target_amount`: wajib, angka positif dengan presisi sesuai standar DB transaksi.
- `saved_amount`: default nol; perubahan dilakukan lewat operasi kontribusi yang tervalidasi, bukan menerima angka bebas tanpa aturan.
- `target_date`: nullable, tanggal valid; tidak wajib.
- `status`: status yang ditetapkan server, contoh `active`, `completed`, `archived` (finalisasi daftar sebelum implementasi).

### Langkah 2 — Buat schema dan model
Buat migration Laravel baru dengan foreign key `user_id`, index yang relevan, decimal/integer sesuai satuan uang yang dipakai model transaksi. Periksa konvensi proyek: sebagian nominal transaksi disimpan dalam cents/amount_cents. Jangan mencampur satuan.

Pertimbangkan soft delete hanya jika proyek memiliki kebutuhan restore; jangan menambah kompleksitas tanpa requirement. `down()` harus menghapus tabel dengan urutan FK yang aman.

### Langkah 3 — Implementasikan API dengan ownership
Tambahkan endpoint terautentikasi, misalnya:
- `GET /saving-goals`
- `POST /saving-goals`
- `GET /saving-goals/{id}`
- `PUT /saving-goals/{id}`
- `DELETE /saving-goals/{id}`
- `POST /saving-goals/{id}/contributions` jika kontribusi eksplisit dipakai.

ID sasaran harus selalu dicari melalui relasi/constraint user login (`user_id = request user id`). Jangan gunakan `SavingGoal::find($id)` tanpa ownership check. Jangan percaya `user_id` dari payload client.

Gunakan `DB::transaction()` untuk kontribusi dan update status bila terdiri dari beberapa operasi. Tentukan dan dokumentasikan apakah kontribusi boleh mengurangi nominal terkumpul, dan bagaimana mencapai/menembus target ditangani. Rekomendasi: kontribusi positif; cap status completed saat target terpenuhi; jangan diam-diam menerima kontribusi tambahan yang menggelembungkan progres.

### Langkah 4 — Tambahkan tests backend
Uji user A hanya dapat melihat/mengubah/menghapus sasaran milik A. User B mengakses ID sasaran A harus mendapat 404/403 sesuai konvensi API, tanpa membocorkan data.

Uji nominal nol/negatif, nama kosong/terlalu panjang, tanggal invalid, kontribusi negatif, duplikasi request bila ada perlindungan idempotensi, serta status completed. Pastikan nominal tidak kehilangan presisi.

### Langkah 5 — Buat UI halaman sasaran
Buat halaman dengan pola layout Zaku yang ada. Sediakan:
- daftar sasaran aktif dan selesai;
- nama, target, terkumpul, sisa, tanggal target bila ada;
- progress bar dengan teks persentase (jangan hanya warna);
- form tambah/edit;
- aksi tambah kontribusi yang eksplisit;
- empty state dengan CTA buat sasaran;
- loading/error state dan tombol coba lagi;
- konfirmasi sebelum hapus.

Jangan menyebut uang telah dipindahkan/disimpan secara fisik. Sasaran adalah tracker saja kecuali ada integrasi rekening yang memang tidak termasuk scope.

### Langkah 6 — Integrasikan navigasi tanpa menambah kepadatan bottom nav
Gunakan navigasi desktop/halaman fitur yang tersedia atau tautan dari halaman Budget/Profile. Bottom navigation mobile saat ini empat item; jangan menambah item kelima/keenam sebagai bagian issue ini.

### Langkah 7 — Verifikasi migrasi, API, UI
Jalankan migration pada DB test, tests backend, build frontend. Jangan memakai database production. Jangan menampilkan stack trace/API internals kepada user.

## 🧪 Cara Test
Backend:
1. `php artisan migrate:fresh --env=testing` hanya pada DB test, atau gunakan test database repo.
2. `php artisan test`.
3. Uji auth user A/B dan seluruh operasi CRUD/kontribusi.
4. Verifikasi migration rollback dan migrate ulang.

Frontend:
1. `npm run build`.
2. Uji sasaran kosong, satu aktif, satu selesai, target tanpa tanggal, dan nama panjang.
3. Tambah kontribusi; refresh; pastikan progres persisten dan hitungannya benar.
4. Uji error API, loading, retry, validasi form, konfirmasi hapus, viewport mobile/desktop.

## ✔️ Acceptance Criteria
- [ ] User dapat membuat, membaca, mengedit, menghapus sasaran sendiri.
- [ ] Kontribusi menambah progres melalui aksi eksplisit dan tervalidasi.
- [ ] Target, terkumpul, sisa, persentase, dan status dihitung benar.
- [ ] Sasaran user lain tidak dapat diakses atau diubah.
- [ ] Nilai uang memakai satuan/precision konsisten dengan DB transaksi.
- [ ] Tidak ada perubahan saldo/transaksi otomatis atau klaim pemindahan uang.
- [ ] Empty/loading/error/retry state tersedia.
- [ ] Navigasi mobile tetap empat item; akses fitur tersedia di tempat yang wajar.
- [ ] Migration rollback/migrate sukses di DB test.
- [ ] `php artisan test` dan `npm run build` berhasil.
- [ ] Multiple wallets tidak dibuat.

## 🔗 Reference
- Budget API: `routes/api.php`, `app/Http/Controllers/Api/BudgetController.php`, `app/Models/Budget.php`
- Authenticated page routes: frontend `routes/web.php`
- API client: frontend `resources/js/api-client.js`
- Alpine patterns: frontend `resources/js/alpine-components.js`
- Explicit exclusion: jangan implementasikan multiple wallets.
