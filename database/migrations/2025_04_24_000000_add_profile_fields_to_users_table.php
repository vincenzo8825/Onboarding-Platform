<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Verifica se la colonna phone esiste già prima di aggiungerla
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            
            // Aggiungi qui altri campi del profilo se necessario
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('users', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('address');
            }
            
            if (!Schema::hasColumn('users', 'hire_date')) {
                $table->date('hire_date')->nullable()->after('birth_date');
            }
            
            if (!Schema::hasColumn('users', 'profile_picture')) {
                $table->string('profile_picture')->nullable()->after('hire_date');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Rimuovi i campi aggiunti
            $table->dropColumn([
                'phone',
                'address',
                'birth_date',
                'hire_date',
                'profile_picture'
            ]);
        });
    }
}