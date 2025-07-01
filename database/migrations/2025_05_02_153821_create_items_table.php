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
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->foreignId('subcategory_id')->nullable()->constrained('sub_categories')->onDelete('cascade');
            $table->morphs('creatorable');
            $table->string('name');
            $table->bigInteger('quantity')->nullable();
            $table->unsignedBigInteger('standard_quantity')->default(0)->nullable();
            $table->unsignedBigInteger('minimum_quantity')->nullable();
            $table->boolean('is_static')->default(false);
            $table->string('unit', 50)->nullable();
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
