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
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_online')->default(false)->after('is_mandatory');
            $table->string('online_link')->nullable()->after('is_online');
            $table->dateTime('registration_deadline')->nullable()->after('online_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['is_online', 'online_link', 'registration_deadline']);
        });
    }
};
