<?php

namespace App\Http\Middleware;

// --- Imports ---
// Closure:    หมายถึง "คำสั่งถัดไป" ที่ Middleware จะต้องเรียก
// Request:    คือข้อมูลคำขอ (Request) ที่ส่งเข้ามา
// Response:   คือข้อมูลที่จะส่งกลับไป (Response)
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request. (จัดการคำขอที่ส่งเข้ามา)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    // 1. (Request $request): คือ Request ที่ผู้ใช้ส่งมา
    // 2. (Closure $next):    คือ "ทางผ่าน" ที่จะไปต่อ (เช่น Controller หรือ Middleware ตัวถัดไป)
    // 3. (string $role):     คือ "บทบาท" (Role) ที่เรากำหนดไว้ใน Route
    //                      เช่น 'admin' จาก `middleware('role:admin')` ใน `routes/web.php`
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 4. ทำการตรวจสอบ
        //    - `$request->user()`: ดึงข้อมูล User ที่กำลังล็อกอินอยู่ (จาก Model User)
        //    - `->role`:          ดึงค่าจากคอลัมน์ 'role' (ที่มาจาก Migration)
        //    - `!== $role`:       เปรียบเทียบว่า "ไม่เท่ากับ" Role ที่ Route ต้องการหรือไม่
        if ($request->user()->role !== $role) {

            // 5. ถ้าไม่เท่ากัน (เช่น 'user' พยายามเข้าหน้า 'admin')
            //    ให้ "ยกเลิก" Request นี้ทันที และส่งหน้าจอ 403 (Forbidden / ห้ามเข้า) กลับไป
            abort(403);
        }

        // 6. ถ้า Role "เท่ากัน" (เช่น 'admin' เข้าหน้า 'admin')
        //    ให้ "อนุญาต" ให้ Request นี้เดินทางต่อไปยัง $next (เช่น Controller ที่เรียก)
        return $next($request);
    }
}
