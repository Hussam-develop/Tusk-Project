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
        Schema::create('appointment_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
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
