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
        Schema::create('job_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->references('id')->on('users')
                ->cascadeOnDelete();
            $table->foreignId('job_id')->references('id')->on('jobs')
                ->cascadeOnDelete();
            $table->text('cover_letter')->nullable();
            $table->text('resume_cv');
            $table->integer('job_status')->comment('0-pending', '1-viewed', '2-interview', '3-processed', '4-hired');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_job_user');
    }
};
