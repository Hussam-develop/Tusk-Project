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
        Schema::create('account_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dentist_id')->constrained()->onDelete('cascade');
            $table->foreignId('lab_manager_id')->constrained()->onDelete('cascade');
            $table->foreignId('bill_id')->nullable()->constrained()->onDelete('cascade');
            $table->morphs('creatorable');
            $table->double('signed_value');
            $table->string('note')->nullable();
            $table->string('type');
            $table->double('current_account');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_records');
    }
};
