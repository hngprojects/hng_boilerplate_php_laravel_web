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
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->string('paystack_plan_code')->nullable()->after('description');
            $table->string('flutterwave_plan_code')->nullable()->after('paystack_plan_code');
            $table->integer('price')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn('paystack_plan_code');
            $table->dropColumn('flutterwave_plan_code');
            $table->decimal('price')->change();

        });
    }
};
