<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // nullable() = อนุญาตให้เว้นว่างได้
            $table->text('description')->nullable()->after('image_path');
            $table->text('synopsis')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['description', 'synopsis']);
        });
    }
};
