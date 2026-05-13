<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/login', [PageController::class, 'login'])->name('login');
Route::get('/register', [PageController::class, 'register'])->name('register');
Route::get('/verify-email', [PageController::class, 'verifyEmail'])->name('verify-email');
Route::get('/email/verify', [PageController::class, 'verifyEmailProcess'])->name('verify-email.process');
Route::get('/verify-manual', [PageController::class, 'verifyManual'])->name('verify-manual');
Route::get('/forgot-password', [PageController::class, 'forgotPassword'])->name('forgot-password');

Route::get('/dashboard', [PageController::class, 'home'])->name('dashboard');
Route::get('/transactions', [PageController::class, 'transactions'])->name('transactions');
Route::get('/transactions/{id}', [PageController::class, 'transactionDetail'])->name('transaction-detail');
Route::get('/chat', [PageController::class, 'chat'])->name('chat');
Route::get('/profile', [PageController::class, 'profile'])->name('profile');

Route::get('/wallet/topup', [PageController::class, 'topup'])->name('wallet.topup');
Route::get('/wallet/withdraw', [PageController::class, 'withdraw'])->name('wallet.withdraw');
Route::get('/wallet/send', [PageController::class, 'sendMoney'])->name('wallet.send');
