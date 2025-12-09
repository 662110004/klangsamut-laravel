<?php

namespace App\Http\Controllers;

// --- Imports ---
// (สำคัญ) นำเข้า Form Request นี้เพื่อใช้ Validation อัตโนมัติ
use App\Http\Requests\ProfileUpdateRequest;
// ใช้สำหรับ Redirect Response
use Illuminate\Http\RedirectResponse;
// (Request) ใช้สำหรับดึงข้อมูลผู้ใช้ที่ล็อกอิน
use Illuminate\Http\Request;
// (Auth) Facade สำหรับการจัดการการยืนยันตัวตน
use Illuminate\Support\Facades\Auth;
// (Redirect) Facade สำหรับการ Redirect
use Illuminate\Support\Facades\Redirect;
// (View) ใช้สำหรับ Type-hint ว่าฟังก์ชันนี้จะคืนค่าเป็น View
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * แสดงหน้าฟอร์มโปรไฟล์ของผู้ใช้ (Display the user's profile form)
     *
     * (ฟังก์ชันนี้ถูกเรียกโดย Route: GET /profile)
     *
     */
    public function edit(Request $request): View
    {
        // 1. คืนค่า View 'profile.edit' (ไฟล์: resources/views/profile/edit.blade.php)
        // 2. ส่งข้อมูลผู้ใช้ที่กำลังล็อกอิน ($request->user())
        //    ไปให้ View นั้นในชื่อตัวแปร 'user'
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * อัปเดตข้อมูลโปรไฟล์ของผู้ใช้ (Update the user's profile information)
     *
     * (ฟังก์ชันนี้ถูกเรียกโดย Route: PATCH /profile)
     *
     */
    // (สำคัญ) สังเกตว่าใช้ 'ProfileUpdateRequest' ไม่ใช่ 'Request' ธรรมดา
    // นี่คือ Form Request ที่จะทำการ "Validate" ข้อมูล (เช่น ชื่อ, อีเมล)
    // โดยอัตโนมัติ *ก่อน* ที่โค้ดในฟังก์ชันนี้จะเริ่มทำงาน
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // 1. นำข้อมูลที่ "ผ่านการ Validate แล้ว" ($request->validated())
        //    มาเติม (fill) ลงใน Model 'user' ที่ล็อกอินอยู่
        //    (นี่คือ Mass Assignment ที่ปลอดภัย เพราะใช้ 'validated()'
        //    ซึ่งถูกกำหนดไว้ใน 'ProfileUpdateRequest.php')
        $request->user()->fill($request->validated());

        // 2. รวจสอบว่าฟิลด์ 'email' มีการเปลี่ยนแปลงหรือไม่
        if ($request->user()->isDirty('email')) {
            // 3. ถ้า 'email' ถูกเปลี่ยน, ให้ล้างค่า 'email_verified_at' เป็น null
            //    เพื่อบังคับให้ผู้ใช้ต้อง "ยืนยันอีเมลใหม่" (Re-verify)
            $request->user()->email_verified_at = null;
        }

        // 4. บันทึกข้อมูลที่เปลี่ยนแปลงทั้งหมด (เช่น name, email, email_verified_at)
        //    ลงในฐานข้อมูล
        $request->user()->save();

        // 5. Redirect กลับไปที่หน้า 'profile.edit'
        //    พร้อมกับส่ง 'status' (session flash data) ชื่อ 'profile-updated'
        //    (ซึ่งไฟล์ profile.edit.blade.php จะใช้แสดงข้อความ "Saved.")
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
}
