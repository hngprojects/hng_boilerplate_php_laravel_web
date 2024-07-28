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
        Schema::create('billing_plans', function (Blueprint $table)
        {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->json('features'); // Store features as JSON
            $table->text('description')->nullable();
            $table->timestamps(); });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_plans');
    }
};
