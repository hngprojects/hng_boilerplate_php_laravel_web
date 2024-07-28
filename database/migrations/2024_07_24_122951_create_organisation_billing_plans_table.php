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
        Schema::create('organisation_billing_plans', function (Blueprint $table)
        {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->foreignUuid('organisation_id')->references('org_id')->on('organisations')->onDelete('cascade')->cascadeOnUpdate();
            $table->foreignUuid('billing_plan_id')->references('id')->on('billing_plans')->onDelete('cascade')->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisation_billing_plans');
    }
};
