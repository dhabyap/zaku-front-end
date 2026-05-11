<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function verifyEmail()
    {
        return view('auth.verify-email');
    }

    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function home()
    {
        return view('dashboard.home');
    }

    public function transactions()
    {
        return view('dashboard.transactions');
    }

    public function transactionDetail($id)
    {
        return view('dashboard.transaction-detail', compact('id'));
    }

    public function profile()
    {
        return view('dashboard.profile');
    }

    public function topup()
    {
        return view('wallet.topup');
    }

    public function withdraw()
    {
        return view('wallet.withdraw');
    }

    public function sendMoney()
    {
        return view('wallet.send-money');
    }
}
