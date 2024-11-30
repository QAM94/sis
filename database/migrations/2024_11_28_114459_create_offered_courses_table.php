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
        Schema::create('offered_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade'); // Reference to the programs table
            $table->foreignId('program_course_id')->constrained()->onDelete('cascade'); // Reference to the program courses table
            $table->foreignId('instructor_id')->constrained()->onDelete('cascade'); // Reference to the instructors table
            $table->enum('type', ['Spring', 'Summer', 'Fall']);
            $table->integer('year');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offered_courses');
    }
};
