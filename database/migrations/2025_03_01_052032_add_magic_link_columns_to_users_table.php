<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMagicLinkColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('magic_link_token', 100)->nullable()->after('remember_token');
            $table->timestamp('magic_link_expires_at')->nullable()->after('magic_link_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('magic_link_token');
            $table->dropColumn('magic_link_expires_at');
        });
    }
}