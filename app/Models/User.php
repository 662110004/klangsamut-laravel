<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; // (บรรทัดนี้ถูกคอมเมนต์ไว้) ถ้าเปิดใช้งาน User จะต้องยืนยันอีเมลก่อน
use Illuminate\Database\Eloquent\Factories\HasFactory; // 1. นำเข้า Trait "HasFactory"
// ทำให้ Model นี้สามารถใช้ Factory (UserFactory.php)
// เพื่อสร้างข้อมูล User ปลอมสำหรับทดสอบ (Seeding)
use Illuminate\Foundation\Auth\User as Authenticatable; // 2. (สำคัญ) นำเข้า "Authenticatable"
// นี่คือ Class หลักที่ทำให้ Model นี้
// "สามารถใช้ในการล็อกอินได้" (มีฟังก์ชัน Login, Logout ฯลฯ)
use Illuminate\Notifications\Notifiable; // 3. นำเข้า Trait "Notifiable"
// ทำให้ Model นี้สามารถ "รับการแจ้งเตือน" (Notifications)
// เช่น อีเมลรีเซ็ตรหัสผ่าน

// (สำคัญ) คลาส User ของเรา "สืบทอด" (extends) มาจาก Authenticatable
// ทำให้มันเป็น Model ที่ใช้ยืนยันตัวตนได้
class User extends Authenticatable
{
    // "ใช้" Trait ที่เรานำเข้ามา
    // ทำให้ User ของเรามีความสามารถของ HasFactory และ Notifiable
    use HasFactory, Notifiable;

    /**
     * (Mass Assignment Protection)
     * $fillable คือ "Whitelist" (รายการที่อนุญาต)
     * ระบุว่าคอลัมน์ใดบ้างที่ "อนุญาต" ให้กรอกข้อมูลได้พร้อมกัน
     * ผ่านคำสั่งอย่าง User::create(...) หรือ $user->update(...)
     *
     * นี่คือเหตุผลที่เราสามารถอัปเดต 'role' หรือสร้าง User ได้
     * (คอลัมน์ 'role' นี้ถูกเพิ่มเข้ามาโดย Migration)
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // เพิ่ม 'role' ที่นี่ เพื่อให้ Admin (UserController) อัปเดตได้
    ];

    /**
     * (Security)
     * $hidden คือ "Blacklist" (รายการที่จะซ่อน)
     * ระบุว่าคอลัมน์ใดบ้างที่จะต้อง "ซ่อน" อัตโนมัติ
     * เมื่อ Model นี้ถูกแปลงเป็น Array หรือ JSON (เช่น การส่งข้อมูล API)
     * เพื่อป้องกันไม่ให้ข้อมูลสำคัญ (เช่น รหัสผ่าน) รั่วไหล
     */
    protected $hidden = [
        'password',
        'remember_token', // Token สำหรับ "จดจำฉัน"
    ];

    /**
     * (Data Casting)
     * casts() คือฟังก์ชันที่บอก Laravel ให้ "แปลงชนิดข้อมูล" อัตโนมัติ
     * เมื่อดึงข้อมูลจากฐานข้อมูล หรือ บันทึกข้อมูลลงฐานข้อมูล
     */
    protected function casts(): array
    {
        return [
            // 1. แปลง 'email_verified_at' (ที่เป็นข้อความ)
            //    ให้กลายเป็น Object 'datetime' (Carbon) เพื่อให้ง่ายต่อการจัดการเวลา
            'email_verified_at' => 'datetime',

            // 2. (สำคัญมาก) แปลง 'password'
            //    นี่คือฟีเจอร์ใหม่ของ Laravel ที่บอกว่า
            //    "ถ้ามีการส่งข้อความธรรมดา (Plain Text) มาที่ 'password'
            //    ให้ทำการ "Hashed" (เข้ารหัส) อัตโนมัติ"
            //    นี่คือสิ่งที่ทำให้รหัสผ่านปลอดภัย โดยที่เราไม่ต้องสั่ง Hash เอง
            'password' => 'hashed',
        ];
    }
}
