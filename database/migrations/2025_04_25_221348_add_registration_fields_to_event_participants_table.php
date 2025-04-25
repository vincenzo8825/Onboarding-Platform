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
        Schema::table('event_participants', function (Blueprint $table) {
            // Campo per tracciare chi ha registrato l'utente all'evento
            $table->foreignId('registered_by')->nullable()->after('status')->constrained('users');

            // Campi per tracciare chi ha segnato la presenza e quando
            $table->foreignId('marked_by')->nullable()->after('attended')->constrained('users');
            $table->timestamp('marked_at')->nullable()->after('marked_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_participants', function (Blueprint $table) {
            $table->dropForeign(['registered_by']);
            $table->dropColumn('registered_by');

            $table->dropForeign(['marked_by']);
            $table->dropColumn('marked_by');
            $table->dropColumn('marked_at');
        });
    }
};
