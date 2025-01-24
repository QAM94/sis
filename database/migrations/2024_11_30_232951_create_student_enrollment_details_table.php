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
        Schema::create('student_enrollment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_enrollment_id')->constrained()->onDelete('cascade'); // Reference to the student enrollments table
            $table->foreignId('offered_course_id')->constrained()->onDelete('cascade'); // Reference to the offered courses table
            $table->decimal('score', 4, 1)->nullable(); // E.g., 90.0, 94.5
            $table->string('grade', 9)->nullable(); // E.g., A, B, C
            $table->text('comments')->nullable(); // Optional remarks
            $table->date('enrolled_at')->nullable(); // Date when the student enrolled in the course
            $table->date('dropped_at')->nullable(); // Date when the student dropped the course
            $table->date('completed_at')->nullable(); // Date when the student completed the course
            $table->enum('status', ['Enrolled', 'Dropped', 'Completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_enrollment_details');
    }
};
