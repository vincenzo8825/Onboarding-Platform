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
        Schema::table('tickets', function (Blueprint $table) {
            // Check if the table has a 'message' column but not a 'description' column
            if (Schema::hasColumn('tickets', 'message') && !Schema::hasColumn('tickets', 'description')) {
                // Rename 'message' to 'description'
                $table->renameColumn('message', 'description');
            }
            // If 'description' doesn't exist but we need it (and 'message' doesn't exist either)
            else if (!Schema::hasColumn('tickets', 'description') && !Schema::hasColumn('tickets', 'message')) {
                // Add the 'description' column
                $table->text('description')->after('title');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'description')) {
                $table->renameColumn('description', 'message');
            }
        });
    }
};
