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
        Schema::create('information', function (Blueprint $table) {
            $table->id('informationId');
            $table->string('title', 100);
            $table->text('content');
            $table->enum('category', ['Berita', 'Acara', 'Pengumuman', 'Pembaruan'])->default('Berita');
            $table->string('image')->nullable()->default(null);
            $table->string('attachment')->nullable()->default(null);
            // $table->json('read_by')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('information');
    }
};
