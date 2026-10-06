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
        Schema::create('membership_application_answers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('membership_application_id');
            $table->unsignedBigInteger('membership_application_question_id');
            $table->text('answer')->nullable();
            $table->timestamps();

            $table->unique(['membership_application_id', 'membership_application_question_id'], 'membership_application_answer_question_unique');
            $table->foreign('membership_application_id', 'maa_application_fk')
                ->references('id')->on('membership_applications')->cascadeOnDelete();
            $table->foreign('membership_application_question_id', 'maa_question_fk')
                ->references('id')->on('membership_application_questions')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_application_answers');
    }
};
