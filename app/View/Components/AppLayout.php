<?php

// (Namespace) ระบุว่าไฟล์นี้อยู่ใน "โฟลเดอร์" App\View\Components
namespace App\View\Components;

// --- Imports ---
// (Component) นำเข้า Class แม่ (Base Class) สำหรับ View Component
use Illuminate\View\Component;
// (View) นำเข้า Class View เพื่อใช้เป็น Type-hint (บอกว่าฟังก์ชัน render จะคืนค่าเป็น View)
use Illuminate\View\View;

// นี่คือ Class (ส่วนตรรกะ) ของ Blade Component
// ชื่อคลาส "AppLayout" จะถูกเรียกใช้งานในไฟล์ Blade (เช่น dashboard.blade.php)
// ผ่านแท็ก <x-app-layout>
class AppLayout extends Component
{
    /**
     * (Docblock) คำอธิบายมาตรฐานของฟังก์ชัน render
     * Get the view / contents that represents the component.
     * (ดึง View หรือเนื้อหาที่เป็นตัวแทนของ Component นี้)
     */

    // (ฟังก์ชัน render) นี่คือฟังก์ชัน "หลัก" ของ Component
    // Laravel จะเรียกฟังก์ชันนี้อัตโนมัติเมื่อเจอแท็ก <x-app-layout>
    // เพื่อถามว่า "ฉันควรจะใช้ไฟล์ Blade (HTML) ไฟล์ไหน?"
    public function render(): View
    {
        // (คำตอบ) คืนค่า View ที่ชื่อ 'layouts.app'
        // Laravel จะไปหาไฟล์ที่: resources/views/layouts/app.blade.php
        // และนำมาแสดงผล
        //
        // (สรุป) ไฟล์นี้ทำหน้าที่แค่ "เชื่อมโยง"
        // แท็ก <x-app-layout> 
        // ให้ไปหาไฟล์ resources/views/layouts/app.blade.php
        return view('layouts.app');
    }
}
