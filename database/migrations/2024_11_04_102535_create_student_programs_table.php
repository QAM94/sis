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
        Schema::create('student_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade'); // Reference to the students table
            $table->foreignId('program_id')->constrained()->onDelete('cascade'); // Reference to the programs table
            $table->enum('status', ['Pre-Enrollment', 'Enrolled', 'Graduated', 'Suspended', 'Withdrawn', 'Deferred']);
            $table->date('enrolled_on')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_programs');
    }
};
