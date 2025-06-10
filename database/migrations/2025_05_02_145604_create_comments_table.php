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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_case_id')->nullable()->default(null)->constrained('medical_cases')->onDelete('cascade');
            // $table->foreignId('dentist_id')->nullable()->constrained('dentists')->onDelete('cascade');
            // $table->foreignId('lab_manager_id')->nullable()->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('dentist_id')->nullable();
            $table->unsignedBigInteger('lab_manager_id')->nullable();
            $table->string('comment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
