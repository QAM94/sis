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
            $table->date('enrolled_at')->default(now()); // Date when the student enrolled in the course
            $table->date('dropped_at')->nullable(); // Date when the student dropped the course
            $table->enum('status', ['Enrolled', 'Dropped', 'Completed'])->default('Enrolled');
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
