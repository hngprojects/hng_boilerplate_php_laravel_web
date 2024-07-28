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
        Schema::table('organisations', function (Blueprint $table) {
            $table->boolean('deleted')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->boolean('is_activated')->default(true);
            $table->boolean('is_deactivated')->default(false);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->dropColumn('deleted');
            $table->dropColumn('is_archived');
            $table->dropColumn('is_activated');
            $table->dropColumn('is_deactivated');
            $table->dropSoftDeletes();
        });
    }
};
