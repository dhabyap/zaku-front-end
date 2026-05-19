# Issue: Fitur Hapus Transaksi (DELETE /transactions/{id})

## Deskripsi Masalah

Saat ini, aplikasi belum memiliki antarmuka untuk menghapus transaksi, padahal backend Laravel sudah menyediakan API `DELETE /transactions/{id}`. 

Fitur hapus transaksi ini sangat penting untuk memberikan kontrol penuh bagi pengguna untuk membatalkan atau membetulkan catatan keuangan yang salah.

Untuk kemudahan dan keamanan pengguna, tombol hapus akan ditempatkan di halaman **Detail Transaksi** (`transaction-detail.blade.php`) lengkap dengan konfirmasi sebelum proses penghapusan dilakukan.

## Tasks for Junior Programmer

Tolong selesaikan tugas-tugas di bawah ini:

- [ ] **1. Tambahkan Tombol Hapus pada Halaman Detail Transaksi**
  - Buka file `resources/views/dashboard/transaction-detail.blade.php`.
  - Di bawah tombol cetak struk (~baris 50), tambahkan tombol "HAPUS TRANSAKSI 🗑️" dengan estetika Brutalist berwarna merah (`var(--punch)`).
  - **Ubah bagian HTML tombol:**
    ```html
    <div style="padding:0 16px; display:flex; flex-direction:column; gap:12px; margin-top:16px;">
        <button @click="window.print()" class="btn-main" style="background:var(--paper);color:var(--ink);margin-top:0;">CETAK STRUK →</button>
        <button @click="deleteTransaction()" class="btn-main" style="background:var(--punch);color:var(--paper);margin-top:0;border:var(--border);box-shadow:var(--bs);">HAPUS TRANSAKSI 🗑️</button>
    </div>
    ```

- [ ] **2. Implementasikan Fungsi `deleteTransaction()` di Alpine.js**
  - Pada bagian `<script>` di `transaction-detail.blade.php`, tambahkan method `deleteTransaction()`.
  - Fungsi ini harus memunculkan konfirmasi dialog browser (`confirm`). Jika disetujui, kirimkan request `DELETE` menggunakan `window.apiClient.delete('/transactions/' + this.id)`.
  - Setelah berhasil dihapus, tampilkan toast sukses dan arahkan pengguna kembali ke halaman daftar riwayat transaksi (`/transactions`).
  - **Contoh Fungsi:**
    ```javascript
    async deleteTransaction() {
        if (!confirm('Apakah kamu yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan.')) return;
        try {
            await window.apiClient.delete('/transactions/' + this.id);
            window.utils.showToast('success', 'Transaksi berhasil dihapus!');
            window.location.href = '/transactions';
        } catch (e) {
            console.error('Delete transaction error:', e);
            window.utils.showToast('error', 'Gagal menghapus transaksi. Coba lagi!');
        }
    }
    ```

- [ ] **3. Sesuaikan Key Kategori yang Baru**
  - Ubah juga key pemanggilan kategori di halaman detail dari `transaction.category` menjadi `transaction.category_name || transaction.category || 'UMUM'` demi menyelaraskan perubahan API terbaru yang mengirimkan `category_name`.
