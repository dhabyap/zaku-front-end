# Panduan Pembuatan API Backend & Endpoint (Berdasarkan Desain UI Terbaru)

Berikut adalah rekomendasi prompt yang bisa Anda gunakan (copy-paste) ke AI / tim Backend untuk membangun API yang sesuai persis dengan kebutuhan `dompet-design-new2.html`:

***

## 📋 REKOMENDASI PROMPT UNTUK BACKEND 

**Tugas:** Buatkan arsitektur, skema database, dan RESTful API endpoint untuk aplikasi pencatat keuangan "DOMPET". Aplikasi ini menggunakan AI NLP (Natural Language Processing) untuk mencatat transaksi dari chat/input teks, serta memiliki UI brutalist yang dinamis.

Mohon pastikan endpoint mengembalikan struktur data berikut agar sesuai dengan kebutuhan Frontend:

### 1. Modul Autentikasi & Profil (Auth & User)
- **`POST /api/auth/register`**
  - **Payload**: `name`, `email`, `password`, `password_confirmation`.
- **`POST /api/auth/login`**
  - **Payload**: `email`, `password`. 
  - **Response**: Mengembalikan `token` (JWT/Sanctum) & profil user.
- **`GET /api/user/profile`**
  - **Response**: 
    - Detail dasar: `name`, `email`, `avatar_initial` (huruf awal).
    - Badge: `member_status` (misal: "MEMBER AKTIF").
    - **Stats**: `total_transactions`, `transactions_this_month`, `largest_transaction_amount`, `unique_categories_used`.
    - **Budget**: `monthly_budget`, `budget_used`, `budget_remaining`, `budget_percentage` (untuk mengisi *budget tracker bar*).
- **`PUT /api/user/profile`**
  - **Payload**: `name`, `email`.
- **`PUT /api/user/budget`**
  - **Payload**: `monthly_budget` (integer).

### 2. Modul Dashboard (Beranda)
- **`GET /api/dashboard`**
  - **Response (Aggregated Data untuk bulan berjalan)**:
    - `current_month_balance` (Total saldo bulan ini).
    - `total_income` & `total_expense` (Pemasukan & Pengeluaran).
    - `insight_strip`: Data dinamis insight keuangan (contoh: `{ text: "Pengeluaran makanan +23%", subtext: "Dibanding minggu lalu · Rp 385.000", icon: "💡" }`).
    - `recent_transactions`: (Array 3-5 transaksi terakhir dengan format: `id`, `description`, `amount`, `type`, `category_name`, `category_icon`, `date_formatted`).
    - `expense_by_category`: (Array progress bar kategori: `category_name`, `category_icon`, `amount`, `percentage_of_expense`).

### 3. Modul AI Chat (Pencatatan Transaksi via Teks)
Ini adalah *core feature* dari aplikasi DOMPET.
- **`POST /api/transactions/chat`**
  - **Payload**: `{ "message": "Beli kopi di Starbucks 65 ribu" }`
  - **Action**: Backend mem-parsing teks (menggunakan AI/LLM atau regex) menjadi data transaksi dan menyimpannya.
  - **Response**: 
    - `reply_message`: Pesan balasan AI (contoh: "Sip, udah dicatat! ☕")
    - `parsed_data`: 
      - `description`: "Kopi di Starbucks"
      - `amount`: 65000
      - `category`: "MAKANAN"
      - `category_icon`: "☕"
      - `type`: "expense" (atau "income")

### 4. Modul Riwayat Transaksi (History)
- **`GET /api/transactions`**
  - **Query Params**: `?filter=SEMUA|PEMASUKAN|PENGELUARAN|MAKANAN` dll.
  - **Response**: 
    - Data harus dikembalikan dalam bentuk **Grouped by Month** agar mudah dirender frontend.
    - Format JSON yang diharapkan:
      ```json
      {
        "data": [
          {
            "month_label": "MEI 2026",
            "transactions": [ { ... }, { ... } ]
          },
          {
            "month_label": "APRIL 2026",
            "transactions": [ { ... } ]
          }
        ]
      }
      ```

### Spesifikasi Teknis Tambahan:
1. **Standar Response**: Gunakan format JSON terstandar, contoh: `{ "success": true, "data": {...}, "message": "..." }`.
2. **Format Uang**: Semua nominal kembalikan dalam `integer` murni (tanpa format Rp/titik/koma). Biarkan frontend yang melakukan formatting.
3. **Seeder Database**: Wajib sediakan database seeder dengan *dummy data* transaksi minimal 2 bulan terakhir, agar saat frontend di-integrasikan, UI dashboard & grafik bisa langsung terisi penuh seperti di desain.
