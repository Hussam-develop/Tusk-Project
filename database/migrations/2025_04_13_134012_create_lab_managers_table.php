<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /*
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lab_managers', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('register_subscription_duration');
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('email_is_verified')->default(0);
            $table->string('verification_code')->nullable();
            $table->rememberToken();
            $table->string('lab_type');
            $table->string('lab_name');
            $table->string('lab_address', 100);
            $table->string('lab_province');
            $table->json('lab_phone');
            $table->string('lab_logo')->nullable();
            $table->time('work_from_hour');
            $table->time('work_to_hour');
            $table->dateTime('register_date')->nullable();
            $table->boolean('subscription_is_valid_now')->nullable()->default(null);
            $table->boolean('register_accepted')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /*
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_managers');
    }
};
