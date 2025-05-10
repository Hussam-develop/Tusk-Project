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
        Schema::create('medical_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dentist_id')->constrained()->onDelete('cascade');
            $table->foreignId('lab_manager_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->string('age');
            $table->boolean('need_trial')->default(0);
            $table->boolean('repeat')->default(0);
            $table->string('shade');
            $table->date('expected_delivery_date');
            $table->text('notes')->nullable();
            $table->unsignedTinyInteger('status');
            $table->boolean('confirm_delivery')->default(0);
            $table->double('cost')->default(0);
            $table->string('teeth_crown')->nullable();
            $table->string('teeth_pontic')->nullable();
            $table->string('teeth_implant')->nullable();
            $table->string('teeth_veneer')->nullable();
            $table->string('teeth_inlay')->nullable();
            $table->string('teeth_denture')->nullable();
            $table->string('bridges_crown')->nullable();
            $table->string('bridges_pontic')->nullable();
            $table->string('bridges_implant')->nullable();
            $table->string('bridges_veneer')->nullable();
            $table->string('bridges_inlay')->nullable();
            $table->string('bridges_denture')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_cases');
    }
};
