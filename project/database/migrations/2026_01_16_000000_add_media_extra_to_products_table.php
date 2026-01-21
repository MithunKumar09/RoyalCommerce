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
        // Advanced Product Media (360/hotspots/3D) config is stored as JSON in this column.
        // Use LONGTEXT for safety (hotspots + manifests + viewer configs).
        if (!Schema::hasColumn('products', 'media_extra')) {
            Schema::table('products', function (Blueprint $table) {
                $table->longText('media_extra')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('products', 'media_extra')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('media_extra');
            });
        }
    }
};

