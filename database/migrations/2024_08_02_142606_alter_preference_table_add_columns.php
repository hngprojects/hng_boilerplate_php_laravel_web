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
        Schema::table('preferences', function (Blueprint $table) {
            $table->foreignUuid('language_id')->constrained('languages')->onDelete('cascade')->nullable();
            $table->foreignUuid('region_id')->constrained('regions')->onDelete('cascade')->nullable();
            $table->foreignUuid('timezone_id')->constrained('timezones')->onDelete('cascade')->nullable();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preferences', function (Blueprint $table) {
            $table->dropForeign(['language_id']);
            $table->dropForeign(['region_id']);
            $table->dropForeign(['timezone_id']);
        });
    }
};
