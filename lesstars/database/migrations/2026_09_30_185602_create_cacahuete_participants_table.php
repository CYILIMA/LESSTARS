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
        Schema::create('cacahuete_participants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cacahuete_game_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->foreignId('recipient_id')->nullable()->constrained('cacahuete_participants')->nullOnDelete();
            $table->timestamp('picked_at')->nullable();
            $table->timestamps();

            $table->unique(['cacahuete_game_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cacahuete_participants');
    }
};
