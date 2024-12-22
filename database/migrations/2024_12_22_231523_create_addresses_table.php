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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('receipent_name');
            $table->string('City');
            $table->string('district')->nullable();
            $table->string('company')->nullable();
            $table->string('id_number')->nullable();
            $table->string('address_one');
            $table->string('address_two')->nullable();
            $table->integer('zipcode');
            $table->string('country')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
