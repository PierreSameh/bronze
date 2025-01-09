<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('itemNo');
            $table->index('itemNo');
            $table->integer('packQty')->default(0);
            $table->integer('stock')->default(0);
            $table->string('gmtModified');
            $table->decimal('rate', 8, 2)->default(0);
            // Change this line to reference the categories table
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
