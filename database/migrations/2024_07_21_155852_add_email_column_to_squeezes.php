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
        if (!Schema::hasColumn('squeezes', 'email')) {
            Schema::table('squeezes', function (Blueprint $table) {
                $table->string('email')->unique();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('squeezes', 'email')) {
            Schema::table('squeezes', function (Blueprint $table) {
                $table->dropColumn('email');
            });
        }
    }
};
