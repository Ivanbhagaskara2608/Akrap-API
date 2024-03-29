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
        Schema::create('users', function (Blueprint $table) {
            $table->id('userId');
            $table->string('fullName', 60);
            $table->string('phoneNumber', 13)->unique();
            $table->date('birthdate');
            $table->enum('gender', ['male', 'female']);
            $table->string('username', 16)->unique();
            $table->enum('role', ['member', 'admin'])->default('member');
            $table->enum('status', ["active", "inactive"])->default("active");
            $table->string('password');
            $table->string('privacyCode', 6)->nullable()->default(null);
            $table->string('api_token')->unique()->nullable()->default(null);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
