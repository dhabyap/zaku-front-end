# Issues Tracking — Zaku Frontend

> **Repository:** https://github.com/dhabyap/zaku-front-end  
> **Stack:** Laravel Blade + Alpine.js + Axios  
> **Status Legend:** \[ ]\ Open · \[/]\ In Progress · \[x]\ Closed

---

## 🔴 High Priority

### Issue #1: Testing & Quality Assurance (TASK-27)

**Priority:** High  
**Status:** Open

**Description:**  
Semua halaman sudah dibuat tapi belum di-test secara menyeluruh di browser. Perlu verifikasi bahwa semua fitur berjalan dengan benar.

**Checklist:**
- [ ] Tidak ada error di console JavaScript
- [ ] Form login bisa submit dan menerima response API
- [ ] Form register bisa submit dan menerima response API
- [ ] Token JWT tersimpan di localStorage dengan benar
- [ ] Token terhapus saat logout
- [ ] Dashboard menampilkan saldo dari API
- [ ] Daftar transaksi menampilkan data dari API
- [ ] Detail transaksi menampilkan data lengkap
- [ ] Top up, withdraw, send money bisa submit ke API
- [ ] AI Chat bisa mengirim pesan dan menerima response
- [ ] Auto-logout saat token expired (HTTP 401)

**Acceptance Criteria:**
- Semua halaman bisa diakses tanpa error
- Semua API call berhasil dan menampilkan data yang benar
- Auth flow (login → dashboard → logout) berjalan sempurna

---

### Issue #2: Responsivitas Mobile (TASK-28)

**Priority:** High  
**Status:** Open

**Description:**  
Aplikasi ditargetkan untuk mobile-first (max-width 420px). Perlu pengecekan di berbagai ukuran layar.

**Checklist:**
- [ ] Semua halaman nyaman di layar HP (320px - 420px)
- [ ] Bottom navigation tidak tertutup konten
- [ ] Form input tidak overflow di layar kecil
- [ ] Modal bisa diakses dan ditutup di layar kecil
- [ ] Toast notification muncul di posisi yang tepat
- [ ] Scroll berfungsi dengan baik di semua halaman

**Acceptance Criteria:**
- Aplikasi responsive di semua ukuran layar mobile
- Tidak ada elemen yang terpotong atau overflow

---

### Issue #3: Review Design System (TASK-29)

**Priority:** Medium  
**Status:** Open

**Description:**  
Pastikan semua halaman konsisten menggunakan design system Brutalist.

**Checklist:**
- [ ] Semua warna menggunakan CSS variable (\--ink\, \--paper\, \--punch\, dll)
- [ ] Tidak ada hardcode warna di CSS
- [ ] Font Syne & DM Mono ter-load dari Google Fonts
- [ ] Shadow \--bs\, \--bs-lg\, \--bs-xl\ diterapkan pada card/button
- [ ] Border style konsisten (3px solid var(--ink))
- [ ] Typography hierarchy konsisten

**Acceptance Criteria:**
- Semua halaman mengikuti design system yang sama
- Tidak ada inkonsistensi visual

---

## 🟡 Medium Priority

### Issue #4: Error Handling yang Lebih Baik

**Priority:** Medium  
**Status:** Open

**Description:**  
Beberapa halaman belum menampilkan error message yang user-friendly saat API gagal.

**Implementation:**
- Tampilkan toast error di setiap halaman saat API call gagal
- Tambah retry button untuk failed requests
- Tambah offline detection message

**Files to Check:**
- \esources/js/api-client.js\
- \esources/views/dashboard/home.blade.php\
- \esources/views/dashboard/transactions.blade.php\

---

### Issue #5: Loading State di Semua Halaman

**Priority:** Medium  
**Status:** Open

**Description:**  
Beberapa halaman belum menampilkan loading skeleton saat data sedang di-fetch dari API.

**Implementation:**
- Gunakan komponen \loading-skeleton.blade.php\ di semua halaman yang fetch data
- Tambah loading state di tombol submit
- Disable input saat loading

**Files to Check:**
- \esources/views/dashboard/home.blade.php\
- \esources/views/dashboard/transactions.blade.php\
- \esources/views/wallet/topup.blade.php\
- \esources/views/wallet/withdraw.blade.php\
- \esources/views/wallet/send-money.blade.php\

---

### Issue #6: Konfirmasi Logout

**Priority:** Medium  
**Status:** Open

**Description:**  
Tombol logout seharusnya menampilkan konfirmasi sebelum user keluar.

**Implementation:**
\\\javascript
// Gunakan confirmDialog dari utils.js
if (confirmDialog('Yakin ingin keluar?')) {
  clearToken();
  window.location.href = '/login';
}
\\\

**Files to Modify:**
- \esources/views/components/navigation.blade.php\
- \esources/views/dashboard/profile.blade.php\

---

### Issue #7: Validasi Form Client-Side

**Priority:** Medium  
**Status:** Open

**Description:**  
Beberapa form belum memiliki validasi client-side yang lengkap.

**Checklist:**
- [ ] Login: validasi format email
- [ ] Register: validasi password match, minimal 8 karakter
- [ ] Top up: validasi nominal > 0
- [ ] Withdraw: validasi nominal <= saldo
- [ ] Send money: validasi penerima != pengirim

**Files to Check:**
- \esources/views/auth/login.blade.php\
- \esources/views/auth/register.blade.php\
- \esources/views/wallet/topup.blade.php\
- \esources/views/wallet/withdraw.blade.php\
- \esources/views/wallet/send-money.blade.php\

---

## 🟢 Low Priority / Enhancement

### Issue #8: Dark Mode Toggle

**Priority:** Low  
**Status:** Open

**Description:**  
Tambahkan opsi dark mode untuk kenyamanan pengguna.

**Implementation:**
- Simpan preferensi di localStorage
- Toggle di halaman profil
- Gunakan CSS variable yang sama dengan nilai terbalik

---

### Issue #9: Export Data Transaksi ke CSV

**Priority:** Low  
**Status:** Open

**Description:**  
Tambahkan fitur export riwayat transaksi ke file CSV.

**Implementation:**
- Tombol "Export CSV" di halaman riwayat transaksi
- Generate file CSV dari data transaksi yang ditampilkan
- Download otomatis

---

### Issue #10: Pagination / Infinite Scroll di Riwayat Transaksi

**Priority:** Low  
**Status:** Open

**Description:**  
Halaman riwayat transaksi belum memiliki pagination atau infinite scroll.

**Implementation:**
- Load 20 transaksi per request
- Tombol "Load More" atau infinite scroll
- Query parameter \?page=X&limit=20\

---

### Issue #11: Notifikasi Push (Opsional)

**Priority:** Low  
**Status:** Open

**Description:**  
Tambahkan notifikasi browser untuk transaksi baru atau saldo masuk.

**Implementation:**
- Gunakan Web Push API
- Minta permission saat pertama kali login
- Notifikasi saat ada transaksi baru

---

### Issue #12: Remember Me Functionality

**Priority:** Low  
**Status:** Open

**Description:**  
Checkbox "Remember Me" di halaman login belum berfungsi.

**Implementation:**
- Simpan email di localStorage saat remember me dicentang
- Auto-fill email saat kembali ke halaman login

**Files to Modify:**
- \esources/views/auth/login.blade.php\

---

## ✅ Resolved Issues

### Issue #0: Login Response Structure Mismatch

**Priority:** High  
**Status:** Closed ✅

**Description:**  
Login berhasil tapi user tidak redirect ke dashboard karena mismatch response API.

**Root Cause:**
- Code menggunakan \esponse.data.data\ padahal response langsung di \esponse.data\
- Field name \	oken\ (bukan \ccess_token\)
- Tidak ada \efresh_token\ di response
- Redirect ke dashboard dikomentari

**Resolution:**
- Ubah response path dari \esponse.data.data\ menjadi \esponse.data\
- Ubah destructuring dari \{ token, refresh_token, user }\ menjadi \{ token, user }\
- Uncomment redirect ke dashboard

**PR:** #60

---

### Issue #13: Auto Logout on Unauthorized

**Priority:** High  
**Status:** Closed ✅

**Description:**  
User tidak otomatis logout saat menerima response 401 dari API.

**Resolution:**
- Tambah axios response interceptor untuk handle 401
- Clear token dan redirect ke \/login\

**PR:** #57

---

## 📊 Summary

| Status | Count |
|---|---|
| Open | 10 |
| In Progress | 0 |
| Closed | 2 |

**Total Issues:** 12