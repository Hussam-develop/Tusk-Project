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
        Schema::create('lab_managers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('email_is_verified');
            $table->string('verification_code');
            $table->string('password');
            $table->string('remember_token', 100)->nullable();
            $table->string('phone');
            $table->string('lab_name');
            $table->char('lab_address', 50);
            $table->string('lab_province');
            $table->char('lab_phone', 14);
            $table->dateTime('lab_register_date');
            $table->boolean('subscription_is_valid_now');
            $table->boolean('register_accepted');
            $table->time('lab_from_hour');
            $table->time('lab_to_hour');
            $table->string('lab_logo');
            $table->string('lab_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_managers');
    }
};
