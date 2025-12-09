// 1. นำเข้าไฟล์ 'bootstrap.js'
// ไฟล์นี้ไม่ได้หมายถึง "Bootstrap (CSS)" แต่เป็นไฟล์ตั้งค่าเริ่มต้นของ Laravel
// ที่ทำหน้าที่โหลด 'axios' (เครื่องมือสำหรับส่งคำขอ HTTP) เข้ามาในโปรเจกต์
import './bootstrap';

// 2. นำเข้า (Import) Library ของ Alpine.js
// นี่คือ 3rd-Party Library (ตามข้อกำหนด) ที่เราใช้สำหรับสร้าง UI แบบ Interactive
// (เช่น Modal, Dropdown, Notification)
import Alpine from 'alpinejs';


// 3. กำหนดให้ Alpine.js เป็นตัวแปร Global (เข้าถึงได้ทั่วทั้งหน้าเว็บ)
// ทำให้เราสามารถเรียกใช้ Alpine.js จากในไฟล์ Blade ได้
window.Alpine = Alpine;


// 4. สั่งให้ Alpine.js เริ่มทำงาน
// Alpine.js จะเริ่มสแกนหน้าเว็บ หา Directive (เช่น x-data, x-show) และเริ่มทำงาน
Alpine.start();