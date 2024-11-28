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
        Schema::create('instructor_courses', function (Blueprint $table) {
            $table->foreignId('instructor_id')->constrained()->onDelete('cascade'); // Reference to the instructors table
            $table->foreignId('course_id')->constrained()->onDelete('cascade'); // Reference to the courses table
            $table->primary(['instructor_id', 'course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_courses');
    }
};
