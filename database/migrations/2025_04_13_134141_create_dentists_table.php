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
        Schema::create('dentists', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->integer('phone');
            $table->string('address');

            $table->time('work_from_hour');
            $table->time('work_to_hour');

            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('email_is_verified')->default(0);
            $table->string('verification_code')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('register_accepted')->nullable()->default(null);
            $table->date('register_date')->nullable();
            $table->boolean('subscription_is_valid_now')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dentists');
    }
};
