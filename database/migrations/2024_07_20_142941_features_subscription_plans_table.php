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
        Schema::create('features_subscription_plans', function (Blueprint $table) {
            $table->foreignUuid('feature_id')->constrained('features');
            $table->foreignUuid('subscription_plan_id')->constrained('subscription_plans');
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('features_subscription_plans');
    }
};
