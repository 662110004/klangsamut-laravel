<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // ===== เริ่มโค้ดใหม่ (แทนที่บรรทัดเดิม) =====

        $user = Auth::user(); // ดึงข้อมูล user ที่เพิ่ง login

        // ...
        if ($user->role === 'admin') {
            return redirect()->intended(route('dashboard'));
        }
        // ส่ง User ธรรมดาไปหน้า 'user.home' ใหม่
        return redirect()->intended(route('user.home'));
        // ...
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
