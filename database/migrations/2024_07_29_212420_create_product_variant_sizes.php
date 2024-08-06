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
        Schema::create('product_variant_sizes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('size_id')->references('id')->on('sizes')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignUuid('product_variant_id')->references('id')->on('product_variants')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
        Schema::table('product_variants', function (Blueprint $table) {
            $table->foreignUuid('product_id')->references('product_id')->on('products')->constrained()->onDelete('cascade')->onUpdate('cascade')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ensure that the table exists before attempting to drop it
        if (Schema::hasTable('product_variant_sizes')) {
            Schema::dropIfExists('product_variant_sizes');
        }
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('product_id');
        });
    }
};
