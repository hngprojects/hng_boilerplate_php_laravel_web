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
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('salary')->nullable();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('organisation_id')->references('org_id')->on('organisations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['organisation_id']);
            
            $table->dropColumn('salary');
            $table->dropColumn('user_id');
            $table->dropColumn('organisation_id');
        });
    }
};
