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
        Schema::table('squeeze_pages', function (Blueprint $table) {
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('status', ['offline', 'online'])->default('online');
            $table->boolean('activate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('squeeze_pages', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('slug');
            $table->dropColumn('status');
            $table->dropColumn('activate');
        });
    }
};
