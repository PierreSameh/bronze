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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('info_en')->nullable();
            $table->string('info_ar')->nullable();
            $table->decimal('price', 8, 2);
            $table->decimal('wholesale_price', 8, 2)->nullable();
            $table->decimal('sale_percentage', 8, 2)->nullable();
            $table->decimal('rate', 4,2)->nullable();
            $table->integer('quantity');
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
