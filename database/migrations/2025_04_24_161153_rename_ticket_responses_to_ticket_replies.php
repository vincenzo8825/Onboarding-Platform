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
        if (Schema::hasTable('ticket_responses')) {
            Schema::rename('ticket_responses', 'ticket_replies');
        }

        if (Schema::hasTable('ticket_replies')) {
            Schema::table('ticket_replies', function (Blueprint $table) {
                if (!Schema::hasColumn('ticket_replies', 'attachment_path')) {
                    $table->string('attachment_path')->nullable()->after('is_internal_note');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ticket_replies')) {
            Schema::table('ticket_replies', function (Blueprint $table) {
                if (Schema::hasColumn('ticket_replies', 'attachment_path')) {
                    $table->dropColumn('attachment_path');
                }
            });

            Schema::rename('ticket_replies', 'ticket_responses');
        }
    }
};
