<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homepage_photos', function (Blueprint $table): void {
            $table->string('image_path')->nullable()->after('id');
            $table->string('description', 180)->nullable()->after('image_path');
            $table->unsignedInteger('display_order')->default(0)->after('description');
            $table->string('status')->default('draft')->after('display_order');
        });
    }

    public function down(): void
    {
        Schema::table('homepage_photos', function (Blueprint $table): void {
            $table->dropColumn(['image_path', 'description', 'display_order', 'status']);
        });
    }
};
