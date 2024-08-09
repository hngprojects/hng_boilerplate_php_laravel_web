<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE TYPE size_enum AS ENUM ('small', 'standard', 'large')");

        Schema::create('sizes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('size', ['small', 'standard', 'large'])->default('standard');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sizes');
    }
};
