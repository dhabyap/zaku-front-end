# Issues

## Riwayat transaksi gagal dimuat di local

**Status:** Resolved  
**Prioritas:** High  
**Area:** Frontend - halaman riwayat transaksi  
**Halaman terkait:** `/transactions`  
**File terkait:** `dompet-frontend/resources/views/dashboard/transactions.blade.php`

### Ringkasan

API `GET /transactions` berhasil dipanggil dan mengembalikan response sukses, tetapi halaman local tetap menampilkan error `Gagal memuat riwayat transaksi` dan data transaksi tidak muncul.

Di production, riwayat transaksi berhasil tampil. Commit terakhir di local dan production sudah dicek dan terlihat sama, tetapi behavior berbeda antara production dan local.

### Bukti

Response API dari Network tab menunjukkan:

```json
{
  "success": true,
  "status": "success",
  "message": "Riwayat transaksi berhasil diambil",
  "data": {
    "groups": [
      {
        "month_label": "JUNI 2026",
        "transactions": []
      },
      {
        "month_label": "MEI 2026",
        "transactions": []
      }
    ],
    "meta": {
      "total": 8,
      "page": 1,
      "limit": 100,
      "has_more": false
    }
  }
}
```

### Dugaan penyebab

Kode local di `fetchTransactions()` saat ini membaca:

```js
const rawData = res.data.data || [];

rawData.forEach(group => {
    if (group.transactions) {
        flatTx = flatTx.concat(group.transactions);
    }
});
```

Namun response API terbaru mengembalikan `res.data.data` sebagai object, bukan array. Data transaksi sebenarnya ada di `res.data.data.groups`.

Akibatnya, saat local menjalankan:

```js
rawData.forEach(...)
```

kemungkinan terjadi error karena `rawData` adalah object dan tidak punya method `forEach`. Error tersebut masuk ke blok `catch`, lalu toast `Gagal memuat riwayat transaksi` muncul.

### Kenapa production bisa berhasil

Kemungkinan yang perlu dicek:

- Production masih memakai hasil build JavaScript lama yang parsing response-nya berbeda.
- Production mengarah ke API/base URL yang response shape-nya masih array.
- Local sudah memakai API dengan response baru `{ data: { groups: [...] } }`, tetapi frontend local belum menyesuaikan parser.
- Cache asset/build local atau production berbeda walaupun commit sama.

### Acceptance Criteria

- Halaman `/transactions` local bisa menampilkan riwayat transaksi dari response `data.groups`.
- Tidak muncul toast `Gagal memuat riwayat transaksi` ketika API sukses.
- Parser tetap aman jika response lama berupa array masih dikirim.
- Filter `SEMUA`, `PEMASUKAN`, `PENGELUARAN`, dan kategori tetap berjalan.

### Rekomendasi perbaikan

Update parser di `fetchTransactions()` agar mendukung dua bentuk response:

- Bentuk lama: `data` berupa array group.
- Bentuk baru: `data.groups` berupa array group.

Contoh arah perbaikan:

```js
const payload = res.data.data || {};
const groups = Array.isArray(payload) ? payload : (payload.groups || []);
let flatTx = [];

groups.forEach(group => {
    if (Array.isArray(group.transactions)) {
        flatTx = flatTx.concat(group.transactions);
    }
});

this.transactions = flatTx;
```

### Implementasi

Sudah diterapkan di `dompet-frontend/resources/views/dashboard/transactions.blade.php`.

Parser `fetchTransactions()` sekarang:

- Membaca response lama ketika `res.data.data` langsung berupa array.
- Membaca response baru ketika transaksi berada di `res.data.data.groups`.
- Menghindari error `rawData.forEach is not a function` saat API sukses tetapi payload berbentuk object.

### Catatan tambahan

Worktree saat dicek memiliki perubahan build asset:

```text
D  dompet-frontend/public/build/assets/app-CKGFfmYU.js
M  dompet-frontend/public/build/manifest.json
?? dompet-frontend/public/build/assets/app-CTljsZ5e.js
```

Perlu hati-hati saat membandingkan local dan production karena perbedaan asset hasil build bisa membuat behavior runtime berbeda meskipun source code terlihat sama.
