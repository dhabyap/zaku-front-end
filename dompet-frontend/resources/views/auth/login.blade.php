@extends('layouts.guest')

@section('content')
    <div x-data="loginForm()"
        style="height:100dvh;display:flex;flex-direction:column;background:var(--punch);justify-content:flex-end;position:relative;">
        <div style="position:absolute;inset:0;overflow:hidden;pointer-events:none;">
            <div
                style="position:absolute;inset:0;background-image:linear-gradient(var(--ink) 1px, transparent 1px),linear-gradient(90deg, var(--ink) 1px, transparent 1px);background-size:40px 40px;opacity:.08;">
            </div>
        </div>

        <div
            style="position:absolute;top:0;left:0;right:0;padding:52px 28px 0;display:flex;flex-direction:column;gap:10px;">
            <div style="display:flex;align-items:center;gap:14px;">
                <div class="wordmark-icon">💸</div>
                <div class="wordmark-text">ZAKU</div>
            </div>
            <div
                style="font-family:var(--font-mono);font-size:12px;letter-spacing:2px;color:rgba(17,16,16,.55);padding-left:2px;">
                TRACK · CONTROL · THRIVE</div>
        </div>

        <div
            style="font-family:var(--font-serif);font-size:96px;font-weight:300;font-style:italic;color:rgba(17,16,16,.12);line-height:1;letter-spacing:-4px;position:absolute;right:20px;top:44px;pointer-events:none;">
            Rp</div>

        <div class="login-sheet">
            <div class="sheet-label">// MASUK KE AKUN</div>

            <form @submit.prevent="submit">
                <div class="field">
                    <label>EMAIL</label>
                    <input type="email" placeholder="kamu@email.com" x-model="formData.email">
                </div>
                <div class="field">
                    <label>PASSWORD</label>
                    <div class="pw-row">
                        <input type="password" placeholder="••••••••" id="li-pw" x-model="formData.password" @keydown="e => { if (e.key === 'Enter') submit() }">
                        <button type="button" class="pw-eye" @click="showPassword = !showPassword" x-text="showPassword ? 'SEMBUNYIKAN' : 'LIHAT'"></button>
                    </div>
                </div>

                <label for="remember-login" style="display:flex;align-items:center;gap:8px;margin:4px 0 16px;font-size:13px;cursor:pointer;">
                    <input id="remember-login" type="checkbox" x-model="formData.remember" style="width:16px;height:16px;accent-color:var(--ink);">
                    <span>Ingat saya <span style="opacity:.65">(tetap masuk hingga 30 hari)</span></span>
                </label>

                <button type="submit" class="btn-main" :disabled="loading">
                    <span x-show="!loading">MASUK SEKARANG →</span>
                    <span x-show="loading" x-cloak>MEMPROSES...</span>
                </button>
            </form>

            <button class="btn-main alt" onclick="window.location.href='/register'">BUAT AKUN BARU</button>

            <div class="auth-link">Demo? <a @click="demoLogin()">Langsung masuk →</a></div>
        </div>
    </div>
@endsection