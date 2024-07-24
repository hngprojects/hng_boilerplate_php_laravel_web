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
        Schema::create('category_product', function (Blueprint $table) {
            $table->uuid('category_product_id')->primary();
            $table->foreignUuid('category_id')->references('id')->on('categories')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignUuid('product_id')->constrained('products', 'product_id')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_product');
    }
};
