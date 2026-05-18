# Issues Tracking

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
