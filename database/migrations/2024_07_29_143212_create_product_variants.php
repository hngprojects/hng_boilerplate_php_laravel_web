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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('stock');
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'low_on_stock']);
            $table->integer('price');
            $table->foreignUuid('size_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ensure that the table exists before attempting to drop it
        if (Schema::hasTable('product_variants')) {
            Schema::dropIfExists('product_variants');
        }
    }
};
