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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dentist_id')->constrained('dentists')->onDelete('cascade');
            $table->string('full_name');
            $table->char('address', 50);
            $table->char('phone', 14);
            $table->date('birthday');
            $table->double('current_balance')->nullable();
            $table->boolean('is_smoker')->default(0); // 0 is not smoker
            $table->enum('gender', ['ذكر', 'أنثى']);
            $table->string('medicine_name')->nullable();
            $table->string('illness_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
