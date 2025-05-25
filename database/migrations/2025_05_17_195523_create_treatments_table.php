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
        Schema::create('treatments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');

            $table->unsignedBigInteger('dentist_id')->nullable();
            $table->foreign('dentist_id')->references('id')->on('dentists')->onDelete('cascade');

            $table->unsignedBigInteger('medical_case_id')->nullable();
            $table->foreign('medical_case_id')->references('id')->on('medical_cases')->onDelete('cascade');

            $table->double('cost')->nullable();
            // $table->string('title')->nullable();
            $table->string('type')->nullable();
            $table->string('details')->nullable();
            $table->date('date')->nullable();
            $table->boolean('is_paid')->default(0);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatments');
    }
};
