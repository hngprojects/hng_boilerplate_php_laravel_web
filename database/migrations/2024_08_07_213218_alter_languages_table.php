<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLanguagesTable extends Migration
{
    public function up()
    {
        Schema::table('languages', function (Blueprint $table) {
            $table->string('language')->unique()->after('id');
            $table->string('code')->unique()->after('language');
            $table->text('description')->nullable()->after('code');
            $table->dropColumn('name');
        });
    }

    public function down()
    {
        Schema::table('languages', function (Blueprint $table) {
            $table->dropUnique(['language']);
            $table->dropUnique(['code']);
            $table->dropColumn(['language', 'code', 'description']);
            $table->string('name')->after('id');
        });
    }
}
