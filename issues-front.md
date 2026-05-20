# Zaku Frontend Issues

Last updated: 2026-05-20

Dokumen ini untuk dikirim ke tim frontend. Isinya sudah diselaraskan dengan `issues-back.md`.

## Prinsip Penting

Zaku hanya untuk tracking pemasukan dan pengeluaran. Jangan buat atau lanjutkan UI untuk:
- Top up.
- Withdraw/tarik saldo.
- Send money/kirim uang.
- Transfer uang antar user.
- Saldo wallet sebagai uang sungguhan.

Frontend harus fokus ke:
- Auth.
- Dashboard cashflow.
- Riwayat transaksi.
- Detail dan hapus transaksi.
- Catat pemasukan/pengeluaran via chat/parser.
- Profile dan budget.

## Kontrak Backend yang Harus Diikuti

Base URL local:

```env
VITE_API_BASE_URL=http://127.0.0.1:8000/api
```

Header auth:

```http
Authorization: Bearer {access_token}
```

Response utama dibaca dari:

```js
response.data.data
```

Login response:

```js
const payload = response.data.data;
const token = payload.token;
const user = payload.user;
```

Simpan token backend sebagai `access_token` di localStorage.

## High Priority

### FE-001 - Hapus semua UI wallet transfer

Status: Open
Related backend: BE-005

Problem:
UI frontend lama masih mengarah ke top up, withdraw, dan send money. Ini salah scope. Zaku bukan aplikasi transfer uang.

Files to check:
- `dompet-frontend/resources/views/wallet/topup.blade.php`
- `dompet-frontend/resources/views/wallet/withdraw.blade.php`
- `dompet-frontend/resources/views/wallet/send-money.blade.php`
- `dompet-frontend/resources/views/components/navigation.blade.php`
- `dompet-frontend/routes/web.php`

Yang harus dilakukan:
- Hapus menu Top Up.
- Hapus menu Withdraw/Tarik Saldo.
- Hapus menu Send Money/Kirim Uang.
- Jangan panggil endpoint `/wallet/balance`, `/wallet/topup`, `/wallet/withdraw`, `/wallet/send`.
- Jika halaman lama belum dihapus, jangan tampilkan di navigasi dan jangan jadikan flow utama.

Acceptance criteria:
- [ ] Tidak ada menu/CTA Top Up.
- [ ] Tidak ada menu/CTA Withdraw/Tarik Saldo.
- [ ] Tidak ada menu/CTA Send Money/Kirim Uang.
- [ ] Tidak ada call frontend ke endpoint `/wallet/*`.
- [ ] Flow utama mengarah ke catat pemasukan/pengeluaran.

### FE-002 - Ganti branding dan copy menjadi Zaku tracking

Status: Open

Problem:
Masih ada copy lama seperti DOMPET dan wording yang membuat aplikasi terasa seperti e-wallet.

Files to check:
- `dompet-frontend/resources/views/auth/login.blade.php`
- `dompet-frontend/resources/views/auth/register.blade.php`
- `dompet-frontend/resources/views/chat/index.blade.php`
- `dompet-frontend/resources/views/layouts/app.blade.php`
- `dompet-frontend/resources/views/layouts/guest.blade.php`
- `dompet-frontend/resources/css/app.css`
- `PRD-Frontend.md`
- `TASKS.md`
- `dompet-frontend/README.md`

Acceptance criteria:
- [ ] Nama aplikasi user-facing adalah Zaku.
- [ ] Tidak ada teks DOMPET pada UI.
- [ ] Copy menjelaskan tracking pemasukan/pengeluaran, bukan dompet digital/transfer uang.
- [ ] Browser title fallback memakai Zaku.

### FE-003 - Set API base URL ke backend local

Status: Open

Problem:
Frontend harus memanggil backend Laravel Zaku, bukan route `/api` internal frontend.

Files to update:
- `dompet-frontend/.env.example`
- `dompet-frontend/README.md`

Acceptance criteria:
- [ ] `.env.example` berisi `VITE_API_BASE_URL=http://127.0.0.1:8000/api`.
- [ ] README menjelaskan backend jalan dengan `php artisan serve` di port 8000.
- [ ] Semua request lewat `window.apiClient` memakai base URL tersebut.

### FE-004 - Sesuaikan auth login/register dengan response backend

Status: Open
Related backend: BE-001

Backend login/register mengirim token di:

```js
response.data.data.token
```

Dan user di:

```js
response.data.data.user
```

Files to check:
- `dompet-frontend/resources/views/auth/login.blade.php`
- `dompet-frontend/resources/views/auth/register.blade.php`
- `dompet-frontend/resources/js/auth.js`

Acceptance criteria:
- [ ] Login membaca `response.data.data.token`.
- [ ] Token disimpan sebagai `access_token`.
- [ ] User disimpan dari `response.data.data.user`.
- [ ] Register mengirim `name`, `email`, `password`, `password_confirmation`.
- [ ] Setelah register, user diarahkan ke flow verifikasi email.
- [ ] Tidak ada asumsi `access_token` langsung dari response backend.

### FE-005 - Perbaiki flow verify email

Status: Open

Backend verify email menerima:

```json
{
  "email": "user@example.com",
  "code": "123456"
}
```

Files to check:
- `dompet-frontend/resources/views/auth/verify-email.blade.php`
- `dompet-frontend/resources/views/auth/manual-verify.blade.php`
- `dompet-frontend/resources/views/auth/process-verify.blade.php`

Acceptance criteria:
- [ ] Frontend mengirim field `email`.
- [ ] Frontend mengirim field `code`.
- [ ] Jangan kirim field `token` untuk verify email.
- [ ] Jangan kirim field `kode`.
- [ ] Resend verification mengirim `{ email }`.

### FE-006 - Perbaiki refresh token / expired session handling

Status: Open
Related backend: BE-001

Problem:
Backend refresh saat ini memakai Bearer token aktif, bukan body `{ refresh_token }`.

Files to check:
- `dompet-frontend/resources/js/api-client.js`
- `dompet-frontend/resources/js/auth.js`

Recommended implementation:
- Saat request dapat HTTP 401, panggil `clearToken()`.
- Redirect ke `/login?session=expired`.
- Jangan kirim request refresh dengan body `{ refresh_token }` kecuali backend sudah mengubah kontraknya.

Acceptance criteria:
- [ ] Tidak ada request refresh token dengan body `refresh_token`.
- [ ] 401 membersihkan localStorage/sessionStorage.
- [ ] User diarahkan ke login dengan pesan session expired.
- [ ] Tidak terjadi redirect loop di halaman login.

### FE-007 - Perbaiki payload update budget

Status: Open

Backend menerima:

```json
{
  "monthly_budget": 3000000
}
```

Frontend lama salah jika mengirim:

```json
{
  "amount": 3000000
}
```

File:
- `dompet-frontend/resources/views/dashboard/profile.blade.php`

Acceptance criteria:
- [ ] Request update budget memakai `monthly_budget`.
- [ ] UI membaca response budget dari `GET /user/profile`.
- [ ] Error validation tampil jelas di toast.

### FE-008 - Sesuaikan dashboard dengan cashflow, bukan saldo wallet

Status: Open

Backend dashboard:
- `current_month_balance`
- `total_income`
- `total_expense`
- `recent_transactions`
- `expense_by_category`

File:
- `dompet-frontend/resources/views/dashboard/home.blade.php`

Acceptance criteria:
- [ ] Dashboard tidak menampilkan istilah "saldo wallet".
- [ ] `current_month_balance` tampil sebagai "Selisih bulan ini" atau "Saldo tercatat".
- [ ] Income dan expense tampil jelas.
- [ ] Recent transactions memakai data dari `/dashboard` atau `/transactions`.
- [ ] Tidak memanggil `/wallet/balance`.

### FE-009 - Sesuaikan daftar dan detail transaksi

Status: Open

Backend list transaksi:
- `GET /transactions`
- Data list memakai `category_name`.

Backend detail transaksi:
- `GET /transactions/{id}`
- Data detail memakai `category`.

Files:
- `dompet-frontend/resources/views/dashboard/transactions.blade.php`
- `dompet-frontend/resources/views/dashboard/transaction-detail.blade.php`

Acceptance criteria:
- [ ] List transaksi membaca `category_name`.
- [ ] Detail transaksi membaca `category`.
- [ ] Filter yang dipakai hanya `SEMUA`, `PEMASUKAN`, `PENGELUARAN`, atau nama kategori.
- [ ] Hapus transaksi memanggil `DELETE /transactions/{id}`.
- [ ] Setelah hapus, user kembali ke riwayat transaksi dan data refresh.

### FE-010 - Sesuaikan chat untuk catat pemasukan/pengeluaran

Status: Open

Endpoint yang boleh dipakai:
- `POST /transactions/chat`
- `POST /ai/chat`

File:
- `dompet-frontend/resources/views/chat/index.blade.php`

Acceptance criteria:
- [ ] Chat copy menjelaskan "catat pemasukan/pengeluaran".
- [ ] Request body hanya `{ "message": "..." }`.
- [ ] UI menangani response income dan expense.
- [ ] Jika amount tidak ditemukan, tampilkan pesan backend ke user.

## Medium Priority

### FE-011 - Lengkapi reset password setelah backend siap

Status: Blocked by BE-002

Problem:
Backend belum punya `POST /auth/reset-password`.

Acceptance criteria setelah backend siap:
- [ ] User bisa request reset token via `/auth/forgot-password`.
- [ ] User bisa submit email, token, password, dan confirmation.
- [ ] Success redirect ke login.
- [ ] Error invalid/expired token tampil jelas.

### FE-012 - Manual smoke test sesuai scope tracking

Status: Open

Checklist:
- [ ] Register.
- [ ] Verify email dengan `{ email, code }`.
- [ ] Login.
- [ ] Dashboard cashflow tampil.
- [ ] Catat pengeluaran lewat chat.
- [ ] Catat pemasukan lewat chat.
- [ ] Lihat riwayat transaksi.
- [ ] Lihat detail transaksi.
- [ ] Hapus transaksi.
- [ ] Update profile.
- [ ] Update budget dengan `monthly_budget`.
- [ ] Logout.
- [ ] Pastikan tidak ada menu/call Top Up, Withdraw, Send Money.

### FE-013 - Loading, error handling, dan empty state

Status: Open

Acceptance criteria:
- [ ] Semua API call punya loading state.
- [ ] Semua API error memakai toast/message yang user-friendly.
- [ ] Empty transaction list punya empty state.
- [ ] Offline/API down ditampilkan jelas.

## Endpoint yang Tidak Boleh Dipakai Frontend

Jangan gunakan endpoint ini:

```text
GET /wallet/balance
POST /wallet/topup
POST /wallet/withdraw
POST /wallet/send
```

Alasan: tidak sesuai scope Zaku sebagai aplikasi tracking pemasukan/pengeluaran.

## Urutan Pengerjaan untuk Tim Frontend

1. FE-001, FE-002, FE-003: bersihkan scope, branding, dan koneksi API.
2. FE-004, FE-005, FE-006: rapikan auth agar tidak error.
3. FE-007, FE-008, FE-009, FE-010: sesuaikan fitur inti dengan backend.
4. FE-012: smoke test end-to-end.
5. FE-011 dan FE-013 setelah kontrak reset password dan UX error final.
