@extends('layouts.guest')

@section('content')
<div x-data="forgotPasswordForm" style="height:100dvh;display:flex;flex-direction:column;background:var(--ink);justify-content:center;padding:24px;">
    <div style="max-width:400px;width:100%;margin:0 auto;background:var(--paper);border:var(--border);box-shadow:var(--bs-xl);padding:28px 24px;">
        <div style="text-align:center;margin-bottom:20px;">
            <div style="width:64px;height:64px;background:var(--punch-2);border:var(--border);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--ink)" stroke-width="2.5">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
            </div>
            <div class="reg-title" style="font-size:24px;color:var(--ink);margin-bottom:8px;">Atur Ulang Password</div>
            <div style="font-family:var(--font-mono);font-size:11px;color:rgba(17,16,16,.5);line-height:1.6;">
                Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
            </div>
        </div>

        <form @submit.prevent="submit">
            <div class="field">
                <label>EMAIL</label>
                <input type="email" x-model="email" placeholder="nama@email.com">
            </div>
            <button type="submit" class="btn-main" :disabled="loading">
                <span x-show="!loading">KIRIM TAUTAN RESET →</span>
                <span x-show="loading" x-cloak>MEMPROSES...</span>
            </button>
        </form>

        <div style="margin-top:20px;padding-top:16px;border-top:var(--border);text-align:center;">
            <div class="auth-link">
                <a href="/login">Kembali ke Halaman Login</a>
            </div>
        </div>
    </div>
</div>

@endsection
