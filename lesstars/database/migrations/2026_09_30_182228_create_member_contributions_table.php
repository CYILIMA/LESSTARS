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
        Schema::create('member_contributions', function (Blueprint $table): void {
            $table->id();
            $table->string('purpose');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 8)->default('RWF');
            $table->string('mobile_money_number')->nullable();
            $table->string('mobile_money_code')->nullable();
            $table->text('payment_instructions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_contributions');
    }
};
