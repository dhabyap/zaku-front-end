# Issues Tracking

## Issue: Login Response Structure Mismatch - User Tidak Bisa Redirect ke Dashboard

**Priority:** High  
**Assigned to:** Junior Programmer  
**Status:** Pending

### Description
Setelah login berhasil, user tidak diarahkan ke dashboard karena ada ketidaksesuaian antara response API dan kode di `login.blade.php`.

### Actual API Response Structure
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "user": {
    "id": 4,
    "name": "yuhuuu",
    "email": "dhabyap@gmail.com",
    "avatar_initial": "Y",
    "wallet": {
      "id": 3,
      "user_id": 4,
      "balance": "0.00",
      "status": "active",
      "created_at": "2026-05-11T04:32:01.000000Z",
      "updated_at": "2026-05-11T04:32:01.000000Z"
    }
  }
}
```

### Root Cause

1. **Response tidak wrapped dalam `data.data`**
   - Code menggunakan: `response.data.data`
   - Response asli: langsung di `response.data`

2. **Field name `token` (bukan `access_token`)**
   - API mengirim: `token`
   - `auth.js` menyimpan dengan key: `access_token` di localStorage

3. **Tidak ada `refresh_token` di response**
   - Backend hanya mengirim `token` (access token saja)
   - `setToken()` dipanggil dengan 2 parameter padahal hanya 1 yang ada

4. **Redirect ke dashboard dikomentari**
   - Line 87: `// window.location.href = '/dashboard';`

### Current Behavior
- Login berhasil (API return 200)
- Error saat destructuring response karena path salah (`response.data.data` vs `response.data`)
- User stuck di halaman login

### Expected Behavior
- Login berhasil → token disimpan di localStorage dengan key `access_token`
- User otomatis redirect ke `/dashboard` setelah 1.5 detik
- Dashboard bisa mengakses token dari localStorage

### Implementation Steps

1. Buka file `dompet-frontend/resources/views/auth/login.blade.php`
2. Ubah response path dari `response.data.data` menjadi `response.data`
3. Ubah destructuring dari `{ token, refresh_token, user }` menjadi `{ token, user }`
4. Ubah `setToken(token, refresh_token)` menjadi `setToken(token)`
5. Uncomment redirect ke dashboard

```javascript
// SEBELUM (SALAH):
const { token, refresh_token, user } = response.data.data;
window.auth.setToken(token, refresh_token);
// window.location.href = '/dashboard';

// SESUDAH (BENAR):
const { token, user } = response.data;
window.auth.setToken(token);
window.location.href = '/dashboard';
```

### Files to Modify
- `dompet-frontend/resources/views/auth/login.blade.php`
- `dompet-frontend/resources/js/auth.js` (make refresh parameter optional)

### Acceptance Criteria
- [ ] Login berhasil menyimpan token di localStorage dengan key `access_token`
- [ ] User otomatis redirect ke `/dashboard` setelah login
- [ ] Dashboard bisa mengambil data dari API (tidak ada error 401)
- [ ] Logout berfungsi dengan benar
- [ ] Auto-logout on 401 tetap berfungsi

### Notes
- Backend tidak mengirim `refresh_token`, jadi token refresh mechanism perlu disesuaikan
- Pastikan `auth.js` handle kasus tanpa refresh_token

---

## Issue: Auto Logout on Unauthorized / Session Expired

**Priority:** High  
**Assigned to:** Junior Programmer  
**Status:** Pending

### Description
Ketika user melakukan request dan mendapatkan response unauthorized (HTTP 401) atau session/token sudah expired, aplikasi harus secara otomatis melakukan logout dan mengarahkan user ke halaman login.

### Current Behavior
- Ketika token expired atau unauthorized, user tetap berada di halaman saat ini
- Tidak ada aksi otomatis logout
- User mungkin masih bisa melihat halaman lama atau mengalami error yang tidak tertangani

### Expected Behavior
- Ketika API mengembalikan status 401 Unauthorized, aplikasi harus:
  1. Membersihkan token/session dari storage (localStorage/sessionStorage)
  2. Membersihkan state user/auth di aplikasi
  3. Mengarahkan user ke halaman login
  4. Menampilkan notifikasi bahwa session telah berakhir (opsional)

### Implementation Guide

1. **HTTP Interceptor (axios/fetch wrapper)**
   - Tambahkan interceptor untuk menangkap response dengan status 401
   - Pada interceptor, panggil fungsi logout dan redirect ke halaman login

2. **Contoh implementasi (axios interceptor):**
   ```javascript
   axios.interceptors.response.use(
     (response) => response,
     (error) => {
       if (error.response?.status === 401) {
         // Clear auth data
         localStorage.removeItem('token')
         localStorage.removeItem('user')
         
         // Redirect to login
         window.location.href = '/login'
         
         // Optional: show notification
         // toast.error('Session expired, please login again')
       }
       return Promise.reject(error)
     }
   )
   ```

3. **Files to check/modify:**
   - HTTP client configuration (e.g., `src/api/client.js`, `src/services/api.js`, or similar)
   - Auth context/store (if using React Context, Redux, etc.)
   - Route guards / protected routes

### Acceptance Criteria
- [ ] User otomatis logout ketika menerima response 401 dari API
- [ ] Token dan data user dibersihkan dari storage
- [ ] User diarahkan ke halaman login
- [ ] Tidak ada error yang muncul di console setelah logout otomatis
- [ ] User tidak bisa mengakses halaman protected setelah logout

### Notes
- Pastikan semua API request dilindungi oleh interceptor yang sama
- Test dengan cara manually menghapus/expiring token saat user sedang login
