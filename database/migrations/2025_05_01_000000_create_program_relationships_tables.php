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
        // Program-Course relationship table
        Schema::create('program_course', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Ensure a course can only be associated with a program once
            $table->unique(['program_id', 'course_id']);
        });

        // Program-Checklist relationship table
        Schema::create('program_checklist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->foreignId('checklist_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Ensure a checklist can only be associated with a program once
            $table->unique(['program_id', 'checklist_id']);
        });

        // Program-Event relationship table
        Schema::create('program_event', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Ensure an event can only be associated with a program once
            $table->unique(['program_id', 'event_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_event');
        Schema::dropIfExists('program_checklist');
        Schema::dropIfExists('program_course');
    }
};
