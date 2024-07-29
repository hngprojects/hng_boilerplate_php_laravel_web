

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
            $table->string('experience_level')->nullable();
            $table->string('work_mode')->nullable();
            $table->text('benefits')->nullable();
            $table->date('deadline')->nullable();
            $table->text('key_responsibilities')->nullable();
            $table->text('qualifications')->nullable();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            
            $table->dropColumn('salary');
            $table->dropColumn('experience_level');
            $table->dropColumn('work_mode');
            $table->dropColumn('benefits');
            $table->dropColumn('deadline');
            $table->dropColumn('key_responsibilities');
            $table->dropColumn('qualifications');
            $table->dropColumn('user_id');
        });
    }
};
