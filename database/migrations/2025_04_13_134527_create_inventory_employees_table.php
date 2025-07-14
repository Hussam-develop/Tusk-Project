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
        Schema::create('inventory_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_manager_id')->constrained('lab_managers')->cascadeOnDelete();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('email_is_verified')->default(0);
            $table->string('verification_code')->nullable();
            $table->boolean('is_staged')->default(0);
            $table->string('password')->nullable()->default(null);
            $table->rememberToken();
            $table->string('phone');
            $table->boolean('active')->nullable()->default(true);
            $table->date('work_start_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_employees');
    }
};
