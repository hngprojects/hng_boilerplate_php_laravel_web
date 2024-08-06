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
        Schema::create('squeeze_pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('headline');
            $table->string('sub_headline');
            $table->string('hero_image');
            $table->string('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('squeeze_pages');
    }
};
