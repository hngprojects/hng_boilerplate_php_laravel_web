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
        Schema::table('timezones', function (Blueprint $table) {
            $table->renameColumn('name', 'timezone');
            $table->renameColumn('offset', 'gmtoffset');
            $table->string('description')->after('gmtoffset');
        });
    }

    public function down(): void
    {
        Schema::table('timezones', function (Blueprint $table) {
            $table->dropColumn('timezone');
            $table->dropColumn('gmtoffset');
            $table->dropColumn('description');
        });
    }
};
