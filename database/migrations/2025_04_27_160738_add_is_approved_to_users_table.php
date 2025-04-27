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
        Schema::table('users', function (Blueprint $table) {
            // Aggiungo il campo is_approved se non esiste già
            if (!Schema::hasColumn('users', 'is_approved')) {
                $table->boolean('is_approved')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Rimuovo il campo is_approved se esiste
            if (Schema::hasColumn('users', 'is_approved')) {
                $table->dropColumn('is_approved');
            }
        });
    }
};
