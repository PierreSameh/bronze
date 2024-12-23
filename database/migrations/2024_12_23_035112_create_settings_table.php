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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->mediumText('distinguishes_en')->nullable();
            $table->mediumText('distinguishes_ar')->nullable();
            $table->mediumText('privacy_en')->nullable();
            $table->mediumText('privacy_ar')->nullable();
            $table->mediumText('terms_en')->nullable();
            $table->mediumText('terms_ar')->nullable();
            $table->mediumText('roles_en')->nullable();
            $table->mediumText('roles_ar')->nullable();
            $table->mediumText('about_en')->nullable();
            $table->mediumText('about_ar')->nullable();
            $table->mediumText('services_en')->nullable();
            $table->mediumText('services_ar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
