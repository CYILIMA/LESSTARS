<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('updates', function (Blueprint $table): void {
            $table->string('author_name')->nullable()->after('body');
            $table->text('author_bio')->nullable()->after('author_name');
            $table->string('thumbnail_path')->nullable()->after('author_bio');
        });
    }

    public function down(): void
    {
        Schema::table('updates', function (Blueprint $table): void {
            $table->dropColumn(['author_name', 'author_bio', 'thumbnail_path']);
        });
    }
};
