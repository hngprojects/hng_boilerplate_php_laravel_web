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
            $table->foreignUuid('language_id')->nullable()->constrained('languages')->onDelete('cascade');
            $table->foreignUuid('region_id')->nullable()->constrained('regions')->onDelete('cascade');
            $table->foreignUuid('timezone_id')->nullable()->constrained('timezones')->onDelete('cascade');
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
