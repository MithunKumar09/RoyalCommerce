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
        if (!Schema::hasTable('product_media_videos')) {
            Schema::create('product_media_videos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->string('target_type', 20)->default('main');
                $table->unsignedBigInteger('target_id')->default(0);
                $table->string('source_type', 20)->nullable();
                $table->text('video_url')->nullable();
                $table->text('video_path')->nullable();
                $table->timestamps();

                $table->index(['product_id', 'target_type', 'target_id'], 'pmv_product_target_idx');
                $table->unique(['product_id', 'target_type', 'target_id'], 'pmv_product_target_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product_media_videos')) {
            Schema::dropIfExists('product_media_videos');
        }
    }
};
