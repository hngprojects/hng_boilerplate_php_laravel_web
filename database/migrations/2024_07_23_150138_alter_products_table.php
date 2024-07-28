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
            $table->string('slug')->unique();
            $table->string('tags');
            $table->string('imageUrl')->nullable();
            $table->enum('status', ['active', 'draft'])->nullable();
            $table->integer('quantity')->default(5);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('slug')->unique();
            $table->dropColumn('tags');
            $table->dropColumn('imageUrl');
            $table->dropColumn('status', ['active', 'draft']);
            $table->dropColumn('quantity');
        });
    }
};
