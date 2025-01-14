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
        Schema::create('program_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade'); // Reference to the programs table
            $table->foreignId('domain_id')->constrained()->onDelete('cascade'); // Reference to the domains table
            $table->foreignId('course_id')->constrained()->onDelete('cascade'); // Reference to the courses table
            $table->integer('semester')->default(1); // Semester Number usually 1-8
            $table->integer('hours'); // Total Course Hours
            $table->decimal('units');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_courses');
    }
};
