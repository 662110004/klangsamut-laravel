<?php

// --- Imports ---
// นำเข้า Controller ทั้งหมดที่จำเป็นสำหรับระบบยืนยันตัวตน
use App\Http\Controllers\Auth\AuthenticatedSessionController; // (จัดการ Login/Logout)
use App\Http\Controllers\Auth\ConfirmablePasswordController; // (จัดการการยืนยันรหัสผ่านก่อนทำธุรกรรมสำคัญ)
use App\Http\Controllers\Auth\EmailVerificationNotificationController; // (จัดการการส่งอีเมลยืนยันซ้ำ)
use App\Http\Controllers\Auth\EmailVerificationPromptController; // (จัดการการแสดงหน้า "โปรดยืนยันอีเมล")
use App\Http\Controllers\Auth\NewPasswordController; // (จัดการการตั้งรหัสผ่านใหม่)
use App\Http\Controllers\Auth\PasswordController; // (จัดการการอัปเดตรหัสผ่านจากหน้า Profile)
use App\Http\Controllers\Auth\PasswordResetLinkController; // (จัดการการส่งลิงก์ลืมรหัสผ่าน)
use App\Http\Controllers\Auth\RegisteredUserController; // (จัดการการสมัครสมาชิก)
use App\Http\Controllers\Auth\VerifyEmailController; // (จัดการการยืนยันอีเมลเมื่อคลิกลิงก์)
use Illuminate\Support\Facades\Route;

// --- 1. GUEST ROUTES ---
// กลุ่ม Route นี้สำหรับ "ผู้ใช้ที่ยังไม่ล็อกอิน" (Guest) เท่านั้น
// ถ้าล็อกอินแล้ว จะถูก redirect ไปที่หน้า 'home' (ตามที่ตั้งค่าใน Middleware)
Route::middleware('guest')->group(function () {

    // --- Register (สมัครสมาชิก) ---
    // (GET) แสดงหน้าฟอร์มสมัครสมาชิก
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register'); // ตั้งชื่อ Route ว่า 'register'

    // (POST) รับข้อมูลจากฟอร์มสมัครสมาชิก (เพื่อสร้าง User)
    Route::post('register', [RegisteredUserController::class, 'store']);

    // --- Login (เข้าสู่ระบบ) ---
    // (GET) แสดงหน้าฟอร์มล็อกอิน
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    // (POST) รับข้อมูลจากฟอร์มล็อกอิน (เพื่อตรวจสอบและสร้าง Session)
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // --- Forgot Password (ลืมรหัสผ่าน) ---
    // (GET) แสดงหน้าฟอร์ม "ลืมรหัสผ่าน" (ที่ให้กรอกอีเมล)
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    // (POST) รับอีเมลจากฟอร์ม "ลืมรหัสผ่าน" (เพื่อส่งลิงก์รีเซ็ตไปให้)
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    // --- Reset Password (ตั้งรหัสผ่านใหม่) ---
    // (GET) แสดงหน้าฟอร์ม "ตั้งรหัสผ่านใหม่" (หลังจากคลิกลิงก์ในอีเมล)
    // {token} คือ Token ที่ใช้ยืนยันว่ามาจากอีเมลจริง
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    // (POST) รับข้อมูลรหัสผ่านใหม่จากฟอร์ม (เพื่ออัปเดตลงฐานข้อมูล)
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// --- 2. AUTHENTICATED ROUTES ---
// กลุ่ม Route นี้สำหรับ "ผู้ใช้ที่ล็อกอินแล้ว" (Auth) เท่านั้น
Route::middleware('auth')->group(function () {

    // --- Email Verification (ยืนยันอีเมล) ---
    // (GET) แสดงหน้า "โปรดยืนยันอีเมลของคุณ" (กรณีที่ยังไม่ยืนยัน)
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    // (GET) URL ที่ผู้ใช้คลิกจากในอีเมลเพื่อยืนยันตัวตน
    // {id} คือ User ID, {hash} คือ hash ที่ใช้ตรวจสอบความถูกต้อง
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        // 'signed' = ป้องกันการปลอมแปลง URL
        // 'throttle:6,1' = ป้องกันการยิง URL นี้ซ้ำๆ (จำกัด 6 ครั้งต่อ 1 นาที)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // (POST) สำหรับปุ่ม "ส่งอีเมลยืนยันอีกครั้ง"
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1') // ป้องกันการกดส่งซ้ำๆ
        ->name('verification.send');

    // --- Confirm Password (ยืนยันรหัสผ่านอีกครั้ง) ---
    // (GET) แสดงหน้าให้กรอกรหัสผ่านอีกครั้ง (ก่อนทำธุรกรรมสำคัญ เช่น ลบบัญชี)
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    // (POST) รับรหัสผ่านที่กรอกมาตรวจสอบ
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // --- Password Update (อัปเดตรหัสผ่าน) ---
    // (PUT) รับข้อมูลการเปลี่ยนรหัสผ่านจากหน้า Profile
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // --- Logout (ออกจากระบบ) ---
    // (POST) ทำการออกจากระบบ (ทำลาย Session)
    // (ใช้ POST/Button แทน GET/Link เพื่อป้องกันปัญหา CSRF)
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
