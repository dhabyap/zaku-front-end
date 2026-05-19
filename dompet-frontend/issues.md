# Issue: Sinkronisasi dan API Contract untuk Fitur AI Chat

## Deskripsi Masalah (Kenapa AI Chat tidak ada respon?)

Saat ini fitur AI Chat tidak merespon dengan benar. Terdapat *mismatch* (ketidakcocokan) antara Frontend (Blade), Laravel Route, Laravel Controller, dan ekspektasi untuk layanan Backend AI utama. 

Ada tiga akar permasalahan yang menyebabkan ini:
1. **Route Tidak Cocok:** Frontend memanggil endpoint `POST /api/transactions/chat`, tetapi di `routes/api.php` didaftarkan sebagai `POST /api/ai/chat`. Hasilnya adalah 404 Not Found.
2. **Format Response (Data Unwrapping):** Kode frontend mengekspektasikan data langsung di *root* objek (memanggil `res.data.response`), namun standardisasi Laravel API mengembalikan data berbalut objek `data` (`['success' => true, 'data' => [...]]`). Sehingga data bernilai `undefined`.
3. **Backend AI Belum Standar:** Belum ada kesepakatan (*API Contract*) yang jelas tentang apa yang harus di-return oleh servis AI *External* agar kompatibel dengan yang diharapkan frontend.

## Tasks for Junior Programmer

Tolong selesaikan task berikut secara berurutan:

- [ ] **1. Perbaiki Route Mismatch**
  - Ubah kode di `resources/views/chat/index.blade.php` baris ke-94.
  - **Dari:** `const res = await window.apiClient.post('/transactions/chat', { message: val });`
  - **Menjadi:** `const res = await window.apiClient.post('/ai/chat', { message: val });`

- [ ] **2. Perbaiki Data Unwrapping di Frontend**
  - Masih di file `resources/views/chat/index.blade.php`, ubah cara membaca variabel data.
  - **Dari:** `const data = res.data;`
  - **Menjadi:** `const data = res.data.data || res.data;` (Tujuannya agar tahan banting jika suatu saat interceptor berubah).

## Dokumentasi API Contract (Untuk Tim Backend AI)

Sampaikan dokumen kontrak API di bawah ini kepada tim Backend AI (Python/Node/Go) yang akan membangun servis `/ai/chat`:

**Endpoint:** `POST /ai/chat`  
**Content-Type:** `application/json`

**Request Payload:**
```json
{
  "message": "Beli makan siang 35rb"
}
```

**Expected Response Schema (Status 200 OK):**
Backend AI *harus* mengembalikan JSON persis seperti format berikut agar frontend bisa menampilkan *Card Transaksi* dengan rapi:

```json
{
  "success": true,
  "data": {
    "response": "🍜 Oke, Beli makan siang udah dicatat ya! 📝",
    "description": "Beli makan siang",
    "amount": 35000,
    "amount_formatted": "-Rp 35.000",
    "category": "🍜 Makanan & Minuman",
    "type": "expense" 
  }
}
```

**Keterangan Field Response:**
- `response` *(string)*: Teks santai/kasual balasan dari AI.
- `description` *(string|null)*: Nama/catatan transaksi hasil ekstrak (contoh: "Beli makan siang").
- `amount` *(integer|null)*: Angka mentah nominal transaksi (contoh: 35000).
- `amount_formatted` *(string|null)*: Nominal yang sudah diformat ke Rupiah, diberi prefix `+` untuk pemasukan dan `-` untuk pengeluaran.
- `category` *(string|null)*: Nama kategori ditambah *emoji* di depannya (contoh: "💰 Pemasukan" atau "🚗 Transportasi").
- `type` *(string|null)*: Wajib diisi dengan `"expense"` atau `"income"`.

Jika AI tidak dapat menemukan angka pada *message*, kembalikan field di atas (selain `response`) dengan nilai `null`.
