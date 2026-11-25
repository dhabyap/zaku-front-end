<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

Route::get('/', [PageController::class, 'landing'])->name('landing');

Route::get('/login', [PageController::class, 'login'])->name('login');
Route::get('/register', [PageController::class, 'register'])->name('register');
Route::get('/verify-email', [PageController::class, 'verifyEmail'])->name('verify-email');
Route::get('/email/verify', [PageController::class, 'verifyEmailProcess'])->name('verify-email.process');
Route::get('/verify-manual', [PageController::class, 'verifyManual'])->name('verify-manual');
Route::get('/forgot-password', [PageController::class, 'forgotPassword'])->name('forgot-password');

Route::middleware(['jwt.session'])->group(function () {
    Route::get('/dashboard', [PageController::class, 'home'])->name('dashboard');
    Route::get('/transactions', [PageController::class, 'transactions'])->name('transactions');
    Route::get('/transactions/{id}', [PageController::class, 'transactionDetail'])->name('transaction-detail');
    Route::get('/chat', [PageController::class, 'chat'])->name('chat');
    Route::get('/manual-input', [PageController::class, 'manualInput'])->name('manual-input');
    Route::get('/categories', [PageController::class, 'categories'])->name('categories');
    Route::get('/profile', [PageController::class, 'profile'])->name('profile');
    Route::get('/changelogs', [PageController::class, 'changelogs'])->name('changelogs');
    Route::get('/monthly-recap', [PageController::class, 'monthlyRecap'])->name('monthly-recap');
    Route::get('/budgets', [PageController::class, 'budgets'])->name('budgets');
    Route::get('/recurring', [PageController::class, 'recurring'])->name('recurring');
    Route::get('/insights', [PageController::class, 'insights'])->name('insights');
    Route::get('/settings', [PageController::class, 'settings'])->name('settings');
    Route::get('/calendar', [PageController::class, 'calendar'])->name('calendar');
    Route::get('/onboarding', [PageController::class, 'onboarding'])->name('onboarding');
});

Route::get('/logout', function () {
    auth()->guard('web')->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    // Call backend API to clear httpOnly cookie
    try {
        $apiBase = config('services.api.base_url') ?? env('VITE_API_BASE_URL', 'https://api-zaku.abysoft.my.id/api');
        $client = new \GuzzleHttp\Client();
        $client->post(rtrim($apiBase, '/') . '/v1/auth/logout', [
            'cookies' => true,
            'headers' => [
                'Cookie' => request()->header('Cookie'),
            ],
        ]);
    } catch (\Exception) {
        // Ignore if backend call fails
    }

    $response = redirect('/login')->with('status', 'Logged out');
    $response->withCookie(cookie()->forget('zaku_token'));

    return $response;
})->name('logout');
