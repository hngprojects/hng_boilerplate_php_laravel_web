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
        Schema::create('product_variants_size', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('size_id')->references('id')->on('sizes')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignUuid('product_variant_id')->references('id')->constrained('product_variants')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
        Schema::table('product_variants', function (Blueprint $table) {
            $table->foreignUuid('product_id')->references('id')->on('products')->constrained()->onDelete('cascade')->onUpdate('cascade')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants_size');
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('product_id');
        });
    }
};
