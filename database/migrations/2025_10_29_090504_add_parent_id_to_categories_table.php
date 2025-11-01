<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // 1. เพิ่มคอลัมน์ 'parent_id'
            // 2. 'nullable()' = อนุญาตให้เป็นค่าว่าง (สำหรับหมวดหมู่หลักที่ไม่มีแม่)
            // 3. 'constrained('categories')' = อ้างอิง ID กลับไปที่ตาราง 'categories'
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories')
                ->onDelete('set null'); // (แนะนำ) ถ้าลบแม่ ให้ลูกเป็น 'null'
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // วิธียกเลิก (สำหรับ rollback)
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
