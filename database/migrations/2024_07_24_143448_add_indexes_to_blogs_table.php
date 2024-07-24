<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->index('author');
            $table->index('title');
            $table->index('tags');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropIndex(['author']);
            $table->dropIndex(['title']);
            $table->dropIndex(['tags']);
            $table->dropIndex(['created_at']);
        });
    }
};
