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
        // La tabella potrebbe già esistere a causa di migrazioni successive
        // Verifichiamo se la tabella esiste prima di crearla
        if (!Schema::hasTable('event_participants')) {
            Schema::create('event_participants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamp('registered_at')->nullable();
                $table->string('status')->default('registered');
                $table->boolean('attended')->default(false);
                $table->text('feedback')->nullable();
                $table->integer('rating')->nullable();
                $table->timestamps();

                // Composte unique to prevent duplicate registrations
                $table->unique(['event_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non eliminiamo la tabella qui, poiché potrebbe essere necessaria per altre migrazioni
        // Le migrazioni successive hanno già aggiunto altre colonne a questa tabella
    }
};
