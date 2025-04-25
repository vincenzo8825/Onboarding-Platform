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
        // Assicuriamoci che la tabella badges abbia tutti i campi necessari
        // Questo è più sicuro rispetto a modifiche separate che potrebbero creare conflitti

        if (Schema::hasTable('badges')) {
            Schema::table('badges', function (Blueprint $table) {
                // Verifichiamo se le colonne esistono prima di aggiungerle
                if (!Schema::hasColumn('badges', 'slug')) {
                    $table->string('slug')->nullable()->after('name');
                }

                if (!Schema::hasColumn('badges', 'icon')) {
                    $table->string('icon')->nullable()->after('description');
                }

                if (!Schema::hasColumn('badges', 'color')) {
                    $table->string('color')->nullable()->after('icon');
                }

                if (!Schema::hasColumn('badges', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('color');
                }

                if (!Schema::hasColumn('badges', 'difficulty')) {
                    $table->string('difficulty')->nullable()->after('is_active');
                }

                if (!Schema::hasColumn('badges', 'category')) {
                    $table->string('category')->nullable()->after('difficulty');
                }

                if (!Schema::hasColumn('badges', 'criteria')) {
                    $table->text('criteria')->nullable()->after('category');
                }

                if (!Schema::hasColumn('badges', 'xp_value')) {
                    $table->integer('xp_value')->default(0)->after('criteria');
                }
            });
        } else {
            // Creiamo la tabella da zero con tutti i campi necessari
            Schema::create('badges', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->nullable();
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->string('color')->nullable();
                $table->boolean('is_active')->default(true);
                $table->string('difficulty')->nullable();
                $table->string('category')->nullable();
                $table->text('criteria')->nullable();
                $table->integer('xp_value')->default(0);
                $table->string('image_path')->nullable();
                $table->enum('type', ['achievement', 'completion', 'special'])->default('achievement');
                $table->json('requirements')->nullable()->comment('Criteri per ottenere il badge');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non facciamo niente qui, non vogliamo rimuovere colonne
    }
};
