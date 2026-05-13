# Backend API Issues — Frontend Requirements

> **Untuk:** Junior Backend Programmer
> **Base URL:** `http://127.0.0.1:8001/api`
> **Auth:** JWT Bearer Token (header `Authorization: Bearer <token>`)
> **Response format:** `{ "status": "success", "data": { ... } }`
>
> **Legend:** ✅ Sudah ada di backend · ❌ Belum dikerjakan

---

## 🔐 Autentikasi

---

### ISSUE-01: Register ✅

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/auth/register` |
| **Frontend file** | `auth/register.blade.php:71` |
| **Auth required** | No |

**Request body:**
```json
{
  "name": "string",
  "email": "string",
  "password": "string",
  "password_confirmation": "string"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Registrasi berhasil. Silakan cek email untuk verifikasi.",
  "data": {
    "user": { "id": 1, "name": "string", "email": "string" }
  }
}
```

---

### ISSUE-02: Login ✅

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/auth/login` |
| **Frontend file** | `auth/login.blade.php:66` |
| **Auth required** | No |
| **Middleware** | `throttle:login` |

**Request body:**
```json
{
  "email": "string",
  "password": "string"
}
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "access_token": "string",
    "refresh_token": "string",
    "expires_in": 3600,
    "user": {
      "id": 1,
      "name": "string",
      "email": "string"
    }
  }
}
```

---

### ISSUE-03: Refresh Token ✅

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/auth/refresh` |
| **Frontend file** | `api-client.js:33` |
| **Auth required** | Yes (JWT) |

**Request body:**
```json
{
  "refresh_token": "string"
}
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "access_token": "string",
    "refresh_token": "string"
  }
}
```

---

### ISSUE-04: Verify Email ✅

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/auth/verify-email` |
| **Frontend file** | `auth/process-verify.blade.php:57` |
| **Auth required** | No |

**Request body:**
```json
{
  "email": "string",
  "code": "string"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Email berhasil diverifikasi."
}
```

---

### ISSUE-05: Resend Verification Email ✅

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/auth/resend-verification` |
| **Frontend file** | `auth/verify-email.blade.php:44` |
| **Auth required** | No |
| **Middleware** | `throttle:verification` |

**Request body:**
```json
{
  "email": "string"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Kode verifikasi telah dikirim ulang."
}
```

---

### ISSUE-06: Forgot Password ✅

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/auth/forgot-password` |
| **Frontend file** | `auth/forgot-password.blade.php:46` |
| **Auth required** | No |
| **Middleware** | `throttle:password-reset` |

**Request body:**
```json
{
  "email": "string"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Instruksi reset password telah dikirim ke email."
}
```

---

### ISSUE-06-B: Change Password ✅

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/auth/change-password` |
| **Auth required** | Yes (JWT) |
| **Note** | Belum ada tombol/UI di frontend, tapi endpoint sudah siap |

**Request body:**
```json
{
  "current_password": "string",
  "new_password": "string",
  "new_password_confirmation": "string"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Password berhasil diubah."
}
```

---

### ISSUE-06-C: Get Authenticated User ✅

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/auth/me` |
| **Frontend** | Digunakan oleh `auth.js` helper |
| **Auth required** | Yes (JWT) |

**Response:**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "string",
    "email": "string"
  }
}
```

---

### ISSUE-06-D: Logout ✅

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/auth/logout` |
| **Auth required** | Yes (JWT) |
| **Note** | Belum ada tombol/UI di frontend (saat ini pake `clearToken()` lokal) |

**Request body:**
```json
{}
```

**Response:**
```json
{
  "status": "success",
  "message": "Berhasil logout."
}
```

---

## 👤 User / Profile

---

### ISSUE-07: Get Profile ✅

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/user/profile` |
| **Frontend file** | `dashboard/profile.blade.php:128` |
| **Auth required** | Yes (JWT) |

**Response:**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "string",
    "email": "string"
  }
}
```

---

### ISSUE-08: Update Profile ✅

| | |
|---|---|
| **Method** | `PUT` |
| **Endpoint** | `/user/profile` |
| **Frontend file** | `dashboard/profile.blade.php:173` |
| **Auth required** | Yes (JWT) |

**Request body:**
```json
{
  "name": "string",
  "email": "string"
}
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "string",
    "email": "string"
  }
}
```

---

### ISSUE-09: Update Budget ✅

| | |
|---|---|
| **Method** | `PUT` |
| **Endpoint** | `/user/budget` |
| **Frontend file** | `dashboard/profile.blade.php:184` |
| **Auth required** | Yes (JWT) |

**Request body:**
```json
{
  "amount": 6000000
}
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "budget": {
      "limit": 6000000,
      "used": 4025000,
      "left": 1975000,
      "pct": 67
    }
  }
}
```

---

## 📊 Dashboard

---

### ISSUE-10: Dashboard Summary ✅

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/dashboard` |
| **Auth required** | Yes (JWT) |

> Endpoint sudah ada. Saat ini frontend masih fetch data dari beberapa endpoint terpisah (`/wallet/balance`, `/transactions?limit=5`, `/transactions/categories`). Bisa di-consolidate lewat endpoint ini.

**Response (saran):**
```json
{
  "status": "success",
  "data": {
    "balance": 3250000,
    "total_income": 7500000,
    "total_expense": 4250000,
    "recent_transactions": [],
    "category_breakdown": []
  }
}
```

---

## 💳 Transaksi

---

### ISSUE-11: List Transactions ✅

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/transactions` |
| **Query params** | `?limit=N` (optional, utk recent transactions di homepage) |
| **Frontend files** | `dashboard/transactions.blade.php:98`, `dashboard/home.blade.php:167` |
| **Auth required** | Yes (JWT) |

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "type": "expense" | "income",
      "amount": 35000,
      "description": "Beli makan siang",
      "category": "MAKANAN",
      "created_at": "2025-01-15T12:00:00Z"
    }
  ]
}
```

---

### ISSUE-12: Transaction Detail ❌

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/transactions/{id}` |
| **Frontend file** | `dashboard/transaction-detail.blade.php:77` |
| **Auth required** | Yes (JWT) |

**Response:**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "type": "expense" | "income",
    "amount": 35000,
    "description": "Beli makan siang",
    "category": "MAKANAN",
    "created_at": "2025-01-15T12:00:00Z"
  }
}
```

---

### ISSUE-13: Transaction Stats ❌

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/transactions/stats` |
| **Frontend file** | `dashboard/profile.blade.php:144` |
| **Auth required** | Yes (JWT) |

**Response:**
```json
{
  "status": "success",
  "data": {
    "total": 47,
    "this_month": 12,
    "biggest": 7500000,
    "categories": 8
  }
}
```

---

### ISSUE-14: Transaction Categories ❌

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/transactions/categories` |
| **Frontend file** | `dashboard/home.blade.php:177` |
| **Auth required** | Yes (JWT) |

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "name": "MAKANAN",
      "amount": 1500000,
      "pct": 35
    },
    {
      "name": "TRANSPORTASI",
      "amount": 500000,
      "pct": 12
    }
  ]
}
```

---

### ISSUE-15: Transaction Chat (AI) ✅

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/transactions/chat` |
| **Frontend file** | `chat/index.blade.php:94` |
| **Auth required** | Yes (JWT) |

**Request body:**
```json
{
  "message": "Beli makan siang 35rb"
}
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "response": "Oke, saya catat ya!",
    "description": "Beli makan siang",
    "amount": 35000,
    "amount_formatted": "Rp 35.000",
    "type": "expense",
    "category": "MAKANAN"
  }
}
```

---

## 💰 Wallet ❌ (Belum ada satupun di backend)

---

### ISSUE-16: Wallet Balance ❌

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/wallet/balance` |
| **Frontend files** | `dashboard/home.blade.php:154`, `wallet/send-money.blade.php:54`, `wallet/withdraw.blade.php:48`, `wallet/topup.blade.php:51` |
| **Auth required** | Yes (JWT) |

**Response:**
```json
{
  "status": "success",
  "data": {
    "balance": 3250000,
    "total_income": 7500000,
    "total_expense": 4250000
  }
}
```

---

### ISSUE-17: Top Up ❌

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/wallet/topup` |
| **Frontend file** | `wallet/topup.blade.php:62` |
| **Auth required** | Yes (JWT) |

**Request body:**
```json
{
  "amount": 100000
}
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "balance": 3350000,
    "message": "Top up berhasil."
  }
}
```

---

### ISSUE-18: Withdraw ❌

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/wallet/withdraw` |
| **Frontend file** | `wallet/withdraw.blade.php:59` |
| **Auth required** | Yes (JWT) |

**Request body:**
```json
{
  "amount": 200000,
  "account_number": "1234567890"
}
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "balance": 3050000,
    "message": "Penarikan berhasil diproses."
  }
}
```

---

### ISSUE-19: Send Money ❌

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/wallet/send` |
| **Frontend file** | `wallet/send-money.blade.php:65` |
| **Auth required** | Yes (JWT) |

**Request body:**
```json
{
  "recipient_email": "user@email.com",
  "amount": 50000,
  "note": "string (opsional)"
}
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "balance": 3000000,
    "message": "Uang berhasil dikirim."
  }
}
```

---

## 📋 Ringkasan

| Status | Jumlah | Endpoint |
|--------|--------|----------|
| ✅ Done | 13 | `/auth/*` (register, login, refresh, verify-email, resend-verification, forgot-password, change-password, me, logout), `/user/profile` (GET+PUT), `/user/budget`, `/dashboard`, `/transactions`, `/transactions/chat` |
| ❌ Pending | 7 | `/transactions/{id}`, `/transactions/stats`, `/transactions/categories`, `/wallet/balance`, `/wallet/topup`, `/wallet/withdraw`, `/wallet/send` |

---

## 📝 Catatan untuk Developer

1. **Response format**: Selalu gunakan `{ "status": "success"|"error", "data": {...}, "message": "..." }`
2. **Error response**: 
   ```json
   { "status": "error", "message": "Deskripsi error", "errors": {...} }
   ```
3. **Auth**: Semua endpoint kecuali auth harus me-return `401` jika token tidak valid
4. **JWT middleware**: Gunakan middleware `jwt.auth` untuk proteksi endpoint
5. **Throttle**: Terapkan rate limiting (`throttle:`) pada endpoint login, resend-verification, dan forgot-password
