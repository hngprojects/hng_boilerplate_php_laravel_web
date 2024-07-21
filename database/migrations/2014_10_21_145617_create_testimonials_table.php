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
         // Drop the table if it exists
         if (Schema::hasTable('testimonials')) {
            Schema::drop('testimonials');
        }

        // Create the table
        Schema::create('testimonials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('user_id');
            $table->string('name');
            $table->text('content');
            $table->timestamps();
        });
    }
    // }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
