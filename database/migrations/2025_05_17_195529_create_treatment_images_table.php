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
        Schema::create('treatment_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_id')->constrained("treatments")->onDelete('cascade');
            // $table->unsignedBigInteger('treatment_id')->nullable();
            // $table->foreign('treatment_id')->references('id')->on('treatments')->onDelete('cascade');


            $table->string('name');
            $table->boolean('is_diagram')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_images');
    }
};
