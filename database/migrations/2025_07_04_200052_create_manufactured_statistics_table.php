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
        Schema::create('manufactured_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_manager_id')->constrained("lab_managers")->onDelete('cascade');
            $table->foreignId('medical_case_id')->constrained("medical_cases")->onDelete('cascade');
            $table->unsignedTinyInteger('piece_number');
            $table->unsignedBigInteger('manufactured_quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufactured_statistics');
    }
};
