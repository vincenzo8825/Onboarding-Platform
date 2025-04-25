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
        Schema::table('badges', function (Blueprint $table) {
            // Aggiungi i campi mancanti
            $table->string('icon')->after('description')->nullable();
            $table->string('color')->after('icon')->nullable();
            $table->string('slug')->after('name')->nullable();
            $table->boolean('is_active')->after('color')->default(true);
            $table->string('difficulty')->after('is_active')->nullable();
            $table->string('category')->after('difficulty')->nullable();
            $table->text('criteria')->after('category')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('badges', function (Blueprint $table) {
            // Rimuovi i campi aggiunti
            $table->dropColumn([
                'icon',
                'color',
                'slug',
                'is_active',
                'difficulty',
                'category',
                'criteria'
            ]);
        });
    }
};
