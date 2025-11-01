<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // 1. ลบ Foreign Key ก่อน (ถ้าคุณใช้ SQLite อาจต้องทำใน DB Browser)
            $table->dropForeign(['category_id']);
            // 2. ลบคอลัมน์
            $table->dropColumn('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // (คำสั่งย้อนกลับ)
            $table->foreignId('category_id')->nullable()->constrained();
        });
    }
};
