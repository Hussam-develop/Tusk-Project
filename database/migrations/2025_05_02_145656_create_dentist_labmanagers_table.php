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
        Schema::create('dentist_labmanagers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_manager_id')->constrained()->onDelete('cascade');
            $table->foreignId('dentist_id')->constrained()->onDelete('cascade');
            $table->boolean('request_is_accepted')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dentist_labmanagers');
    }
};
