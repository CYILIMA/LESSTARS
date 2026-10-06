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
        Schema::create('membership_applications', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('membership_application_form_id');
            $table->string('applicant_name');
            $table->string('applicant_email');
            $table->string('status')->default('pending');
            $table->text('decision_notes')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->foreign('membership_application_form_id', 'ma_form_fk')
                ->references('id')->on('membership_application_forms')->cascadeOnDelete();
            $table->foreign('reviewed_by', 'ma_reviewer_fk')
                ->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_applications');
    }
};
