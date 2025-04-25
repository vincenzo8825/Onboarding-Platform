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
        // Questa migrazione verrà eseguita dopo la creazione della tabella events
        Schema::table('event_feedback', function (Blueprint $table) {
            // Aggiungi la chiave esterna per event_id che fa riferimento alla tabella events
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_feedback', function (Blueprint $table) {
            // Rimuovi la chiave esterna
            $table->dropForeign(['event_id']);
        });
    }
};
