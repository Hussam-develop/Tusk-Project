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
        Schema::create('files', function (Blueprint $table) {
            $table->id();

            // $table->foreignId('medical_case_id')->constrained('medical_cases')->onDelete('cascade');

            $table->unsignedBigInteger('medical_case_id')->nullable();
            $table->foreign('medical_case_id')->references('id')->on('medical_cases')->onDelete('cascade');


            $table->string('name');
            $table->boolean('is_case_image')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
