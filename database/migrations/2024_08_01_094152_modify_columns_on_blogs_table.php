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
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('category');
            $table->text('image_url');
            $table->dropColumn('blog_category_id');
            $table->foreignUuid('author_id')->constrained('users', 'id')->cascadeOnDelete()->cascadeOnUpdate();
        });
        Schema::dropIfExists('blog_categories');
        Schema::dropIfExists('blog_images');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn('category');
            $table->uuid('blog_category_id');
        });

        Schema::create('blog_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('blog_id');
            $table->string('image_url');
            $table->timestamps();
        });


        Schema::create('blog_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }
};
