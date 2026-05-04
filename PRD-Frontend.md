# PRD - DOMPET Frontend Application
## Laravel Blade Application with Alpine.js

**Project Name:** DOMPET Frontend Application  
**Language:** PHP with Laravel Framework  
**UI/Styling:** HTML, CSS, JavaScript, Alpine.js  
**API Integration:** Axios + JWT Bearer Token  
**Hosting:** Shared Hosting (separate domain from backend)  
**Target Audience:** Frontend Developer / UI Developer

---

## 🏗️ Tech Stack

- **Framework:** Laravel (for routing & templating)
- **Templating:** Laravel Blade
- **Styling:** CSS3 (dari dompet-design.html)
- **Interactivity:** Alpine.js
- **HTTP Client:** Axios
- **Build Tool:** Laravel Mix / Vite
- **Package Manager:** Composer (PHP), NPM (Node)
- **Storage:** localStorage untuk JWT tokens

---

## 📁 Project Setup

### Initial Setup Command
```bash
laravel new dompet-frontend
cd dompet-frontend
npm install alpinejs axios
npm run dev
```

### Folder Structure
```
/dompet-frontend
├── /resources
│   ├── /views
│   │   ├── /auth
│   │   │   ├── login.blade.php
│   │   │   ├── register.blade.php
│   │   │   ├── verify-email.blade.php
│   │   │   └── forgot-password.blade.php
│   │   ├── /dashboard
│   │   │   ├── home.blade.php
│   │   │   ├── transactions.blade.php
│   │   │   ├── transaction-detail.blade.php
│   │   │   └── profile.blade.php
│   │   ├── /wallet
│   │   │   ├── topup.blade.php
│   │   │   ├── withdraw.blade.php
│   │   │   └── send-money.blade.php
│   │   ├── /components
│   │   │   ├── navigation.blade.php
│   │   │   ├── header.blade.php
│   │   │   ├── loading-skeleton.blade.php
│   │   │   └── toast-notification.blade.php
│   │   └── /layouts
│   │       ├── app.blade.php
│   │       └── guest.blade.php
│   ├── /css
│   │   ├── app.css (dari dompet-design.html)
│   │   └── custom.css
│   └── /js
│       ├── app.js
│       ├── api-client.js (axios instance & interceptors)
│       ├── auth.js (authentication logic)
│       └── utils.js (helper functions)
├── /routes
│   ├── web.php (frontend routing)
│   └── api.php (internal routes jika diperlukan)
├── /app/Http/Controllers
│   ├── PageController.php (untuk render pages)
│   └── AuthController.php (untuk local auth state)
├── /config
│   └── api.php (API configuration)
├── .env (environment variables)
├── webpack.mix.js (atau vite.config.js)
├── composer.json
└── package.json
```

### Environment Configuration (.env)
```env
APP_NAME="DOMPET Frontend"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://dompet.com

VITE_API_BASE_URL=https://api.dompet.com/api
VITE_API_TIMEOUT=30000

# Local Development (optional)
# VITE_API_BASE_URL=http://localhost:8000/api
```

---

## 🎨 Design System (dari dompet-design.html)

### Color Palette
```css
--ink:     #111010;      /* Dark background */
--paper:   #F5F0E8;      /* Light background */
--cream:   #EDE8DC;      /* Secondary background */
--punch:   #FF4D00;      /* Primary / CTA buttons */
--punch-2: #FFD600;      /* Secondary actions */
--mint:    #00E5A0;      /* Success states */
--sky:     #C8F0FF;      /* Info / Light states */
```

### Typography
```css
--font-display: 'Syne', sans-serif;       /* Headers */
--font-mono:    'DM Mono', monospace;     /* Numbers */
--font-serif:   'Fraunces', serif;        /* Accents */
```

### Shadows & Effects
```css
--bs:      4px 4px 0 var(--ink);         /* Small shadow */
--bs-lg:   6px 6px 0 var(--ink);         /* Large shadow */
--bs-xl:   8px 8px 0 var(--ink);         /* Extra large shadow */
```

### Layout
```css
.shell {
  max-width: 420px;                       /* Mobile reference */
  height: 100dvh;
  display: flex;
  flex-direction: column;
}
```

---

## 🔐 Phase 1: Authentication Pages

### 1.1 Login Page
**File:** `resources/views/auth/login.blade.php`  
**Route:** `GET /login`  

**Page Structure:**
```blade
@extends('layouts.guest')

@section('content')
<div x-data="loginForm()" x-init="init()" class="login-screen">
  <h1>Login</h1>
  
  <form @submit.prevent="handleLogin" class="login-form">
    <!-- Email Input -->
    <div class="form-group">
      <input 
        type="email" 
        x-model="form.email" 
        placeholder="Email"
        required
      >
      <span x-show="errors.email" class="error" x-text="errors.email"></span>
    </div>
    
    <!-- Password Input -->
    <div class="form-group">
      <input 
        type="password" 
        x-model="form.password" 
        placeholder="Password"
        required
      >
      <span x-show="errors.password" class="error" x-text="errors.password"></span>
    </div>
    
    <!-- Remember Me -->
    <div class="checkbox">
      <input type="checkbox" x-model="form.remember" id="remember">
      <label for="remember">Remember me</label>
    </div>
    
    <!-- Login Button -->
    <button type="submit" :disabled="loading" class="btn-primary">
      <span x-show="!loading">Login</span>
      <span x-show="loading">Loading...</span>
    </button>
  </form>
  
  <!-- Links -->
  <div class="links">
    <a href="/forgot-password">Forgot password?</a>
    <p>Don't have account? <a href="/register">Register</a></p>
  </div>
</div>

<script>
function loginForm() {
  return {
    form: { email: '', password: '', remember: false },
    errors: {},
    loading: false,
    
    async handleLogin() {
      this.errors = {};
      this.loading = true;
      
      try {
        const response = await apiClient.post('/auth/login', this.form);
        
        // Store tokens
        localStorage.setItem('access_token', response.data.data.access_token);
        localStorage.setItem('refresh_token', response.data.data.refresh_token);
        
        // Store user data
        sessionStorage.setItem('user', JSON.stringify(response.data.data));
        
        // Redirect to dashboard
        window.location.href = '/dashboard';
      } catch (error) {
        if (error.response?.data?.message) {
          this.errors.general = error.response.data.message;
        } else {
          this.errors.general = 'Login failed. Please try again.';
        }
        showToast('error', this.errors.general);
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>
@endsection
```

**Functionality:**
- Form validation (email format, password not empty)
- Show loading state on submit
- Display error messages
- Store JWT tokens in localStorage
- Remember me option (optional)
- Redirect to dashboard on success
- Show toast notification on error

**API Integration:**
- `POST /api/auth/login`
- Store access_token & refresh_token
- Store user data in sessionStorage

---

### 1.2 Register Page
**File:** `resources/views/auth/register.blade.php`  
**Route:** `GET /register`  

**Page Structure:**
```blade
@extends('layouts.guest')

@section('content')
<div x-data="registerForm()" x-init="init()" class="register-screen">
  <h1>Create Account</h1>
  
  <form @submit.prevent="handleRegister" class="register-form">
    <!-- Name Input -->
    <div class="form-group">
      <input 
        type="text" 
        x-model="form.nama" 
        placeholder="Full Name"
        required
      >
    </div>
    
    <!-- Email Input -->
    <div class="form-group">
      <input 
        type="email" 
        x-model="form.email" 
        placeholder="Email"
        required
      >
    </div>

...existing code...
