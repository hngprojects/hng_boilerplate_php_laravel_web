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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price');
            $table->enum('size', ['Small', 'Standard', 'Large'])->nullable();
            $table->string('imageUrl')->nullable();
            $table->enum('status', ['in stock', 'out of stock'])->nullable();
            $table->integer('quantity')->default(5);
            $table->string('category')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('size');
            $table->dropColumn('imageUrl');
            $table->dropColumn('status');
            $table->dropColumn('quantity');
            $table->dropColumn('category');
        });
    }
};
