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
        Schema::create('api_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('api_group')->nullable();
            $table->string('method')->nullable();
            $table->string('status')->nullable();
            $table->string('response_time')->nullable();
            $table->string('last_checked')->timestamps();
            $table->string('details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_statuses');
    }
};
