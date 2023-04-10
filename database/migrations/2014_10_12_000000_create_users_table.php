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
            $table->enum('job', ['chairman', 'vice chairman', 'secretary', 'treasurer', 'member']);
            $table->string('password');
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
