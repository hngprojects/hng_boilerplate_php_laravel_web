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
        Schema::create('organisations', function (Blueprint $table) {
            $table->uuid('org_id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->text('description')->nullable();
            $table->string('industry')->nullable();
            $table->string('type')->nullable();
            $table->string('country')->nullable();
            $table->string('address')->nullable();
            $table->string('state')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisations');
    }
};
