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
        Schema::table('posts', function (Blueprint $table) {
            $table->string('type')->default('article')->after('category_id');
            $table->string('youtube_url', 2048)->nullable()->after('featured_image');
            $table->json('gallery_images')->nullable()->after('youtube_url');
            $table->string('infographic_image', 2048)->nullable()->after('gallery_images');

            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn([
                'type',
                'youtube_url',
                'gallery_images',
                'infographic_image',
            ]);
        });
    }
};
