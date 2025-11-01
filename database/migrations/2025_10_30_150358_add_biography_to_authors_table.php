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
        Schema::table('authors', function (Blueprint $table) {
            // เพิ่ม 'biography' (ประเภท text) และอนุญาตให้เว้นว่าง (nullable)
            $table->text('biography')->nullable()->after('image_path');
        });
    }

    public function down(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->dropColumn('biography');
        });
    }
};
