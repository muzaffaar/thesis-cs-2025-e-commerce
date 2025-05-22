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
        Schema::create('catalog_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('catalog_id');
            $table->string('locale');
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
            $table->unique(['catalog_id', 'locale']);
            $table->foreign('catalog_id')->references('id')->on('catalogs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_translations');
    }
};
