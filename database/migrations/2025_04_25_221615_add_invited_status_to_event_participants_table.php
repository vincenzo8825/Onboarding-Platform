<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifica l'ENUM della colonna status per includere 'invited'
        DB::statement("ALTER TABLE event_participants MODIFY COLUMN status ENUM('registered', 'confirmed', 'cancelled', 'invited') DEFAULT 'registered'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ripristina la definizione originale dell'ENUM
        DB::statement("ALTER TABLE event_participants MODIFY COLUMN status ENUM('registered', 'confirmed', 'cancelled') DEFAULT 'registered'");
    }
};
