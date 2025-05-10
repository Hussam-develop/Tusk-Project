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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('subcategory_id')->constrained('sub_categories')->onDelete('cascade');
            $table->morphs('creatorable');
            $table->string('name');
            $table->unsignedBigInteger('quantity');
            $table->unsignedBigInteger('standard_quantity')->default(0);
            $table->unsignedBigInteger('minimum_quantity');
            $table->boolean('is_static')->default(false);
            $table->string('unit',50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
