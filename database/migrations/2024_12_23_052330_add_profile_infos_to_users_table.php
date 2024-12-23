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
        Schema::table('users', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('email_verified_at');
            $table->date('birthday')->nullable()->after('photo');
            $table->enum('gender', ['male', 'female'])->default('male')->after('photo');
            $table->foreignId('city_id')->nullable()->constrain('cities')->onDelete('cascade')->after('gender');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['photo', 'birthday', 'gender', 'city_id']);
        });
    }
};
