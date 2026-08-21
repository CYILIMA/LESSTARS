<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theatres', function (Blueprint $table): void {
            $table->id(); $table->string('title'); $table->text('description'); $table->string('thumbnail_path')->nullable(); $table->string('audio_path'); $table->string('status')->default('draft'); $table->timestamps();
        });
        Schema::create('theatre_comments', function (Blueprint $table): void {
            $table->id(); $table->foreignId('theatre_id')->constrained()->cascadeOnDelete(); $table->foreignId('parent_id')->nullable()->constrained('theatre_comments')->cascadeOnDelete(); $table->string('name'); $table->string('email')->nullable(); $table->text('body'); $table->string('status')->default('pending'); $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('theatre_comments'); Schema::dropIfExists('theatres'); }
};
