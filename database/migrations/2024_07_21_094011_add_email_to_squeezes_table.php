<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailToSqueezesTable extends Migration
{
    public function up()
    {
        Schema::table('squeezes', function (Blueprint $table) {
            $table->string('email')->unique();
        });
    }

    public function down()
    {
        Schema::table('squeezes', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
}
