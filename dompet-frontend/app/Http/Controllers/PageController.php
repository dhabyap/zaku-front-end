<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function landing()
    {
        return view('landing');
    }

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

    public function verifyEmailProcess()
    {
        return view('auth.process-verify');
    }

    public function verifyManual()
    {
        return view('auth.manual-verify');
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

    public function chat()
    {
        return view('chat.index');
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

    public function changelogs()
    {
        return view('dashboard.changelogs');
    }

    public function monthlyRecap()
    {
        return view('dashboard.monthly_recap');
    }

    public function budgets()
    {
        return view('dashboard.budgets');
    }
}
