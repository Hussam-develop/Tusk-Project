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
        Schema::create('doctor_times', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dentist_id')->constrained('dentists')->onDelete('cascade');

            $table->string("day");
            $table->time("start_time")->nullable();
            $table->time("end_time")->nullable();
            $table->time("start_rest")->nullable();
            $table->time("end_rest")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_times');
    }
};
