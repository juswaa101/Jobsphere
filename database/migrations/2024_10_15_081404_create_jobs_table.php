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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('company');
            $table->double('salary_from');
            $table->double('salary_to')->nullable();
            $table->text('company_logo');
            $table->tinyInteger('is_active')->comment('0-inactive, 1-active')
                ->default(0);
            $table->date('expiry_date');
            $table->foreignId('user_created_by')->references('id')->on('users')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
