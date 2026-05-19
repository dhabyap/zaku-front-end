# Issue: Fix Alpine JS Reference Errors on Profile Page

## Deskripsi Masalah (Error Explanation)
Terjadi `Uncaught ReferenceError: formatNumber is not defined` (serta error serupa untuk `editForm`, `budgetInput`, dan `logout()`) pada halaman `profile.blade.php`. 

Error ini terjadi karena function component Alpine `profilePage()` **gagal melakukan inisialisasi (crash)** di awal, sehingga Alpine menggunakan scope kosong. Akibatnya, saat Alpine mencoba merender fungsi atau variabel seperti `formatNumber` atau `logout`, ia tidak dapat menemukannya.

Penyebab kegagalan inisialisasi ini utamanya adalah:
1. **Crash pada Auth `getUser()` (JSON Parsing Error):** Jika `res.data.data` dari API undefined/null, fungsi `auth.setUser(user)` akan memasukkan string `"undefined"` ke dalam `sessionStorage`. Saat halaman direfresh, `window.auth.getUser()` akan menjalankan `JSON.parse("undefined")` yang menyebabkan `SyntaxError` dan mematikan inisialisasi Alpine.
2. **Crash pada rendering huruf kapital avatar (TypeError):** Ekspresi `user?.name?.charAt(0).toUpperCase()` akan menyebabkan `TypeError` jika `user` bernilai null atau nama kosong, karena `.toUpperCase()` tidak bisa dipanggil pada `undefined`.

## Tasks for Junior Programmer

Tolong perbaiki bug ini dengan melakukan langkah-langkah berikut:

- [ ] **1. Perbaiki file `resources/js/auth.js`**
  - Pada fungsi `setUser`, pastikan kita tidak menyimpan `"undefined"`. Jika user tidak ada, hapus user dari session.
  - Pada fungsi `getUser`, gunakan try-catch untuk menghindari crash jika data JSON di `sessionStorage` tidak valid atau bernilai `"undefined"`.
  - **Petunjuk:** 
    ```javascript
    export const setUser = (user) => {
        if (!user) {
            sessionStorage.removeItem('user');
            return;
        }
        sessionStorage.setItem('user', JSON.stringify(user));
    };

    export const getUser = () => {
        try {
            const user = sessionStorage.getItem('user');
            return user && user !== 'undefined' ? JSON.parse(user) : null;
        } catch (e) {
            return null; // Fallback jika format JSON rusak
        }
    };
    ```

- [ ] **2. Perbaiki fungsi kapitalisasi nama di `resources/views/dashboard/profile.blade.php`**
  - Cari elemen `<div class="prof-av" x-text="user?.name?.charAt(0).toUpperCase() || '?'"></div>`.
  - Ubah menjadi aman dari `undefined` dengan membungkusnya dalam kurung.
  - **Ubah menjadi:** 
    `x-text="(user?.name?.charAt(0) || '?').toUpperCase()"`

- [ ] **3. (Opsional/Best Practice) Berikan fallback default pada Alpine Profile Init**
  - Di `profile.blade.php` dalam script `function profilePage()`, saat set default state `user: window.auth.getUser() || null,`, pastikan kita selalu mendapat obyek atau null, dan tambahkan validasi pada `init()` ketika menangkap `this.user` dari API.

Kerjakan langkah di atas dan periksa kembali di browser apakah menu edit, budget, dan logout sudah kembali berfungsi dengan normal.
