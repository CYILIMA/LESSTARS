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
        Schema::table('contribution_payments', function (Blueprint $table) {
            $table->boolean('is_refunded')->default(false)->after('is_paid');
            $table->timestamp('refunded_at')->nullable()->after('paid_at');
            $table->text('refund_reason')->nullable()->after('refunded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contribution_payments', function (Blueprint $table) {
            $table->dropColumn(['is_refunded', 'refunded_at', 'refund_reason']);
        });
    }
};
