<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToTicketsTable extends Migration
{
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Aggiungi qui i campi aggiuntivi che vuoi aggiungere alla tabella tickets
            if (!Schema::hasColumn('tickets', 'category')) {
                $table->string('category')->nullable()->after('priority');
            }
            
            // Altri campi che vuoi aggiungere
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Rimuovi i campi aggiunti
            $table->dropColumn(['category']);
            // Altri campi da rimuovere
        });
    }
}