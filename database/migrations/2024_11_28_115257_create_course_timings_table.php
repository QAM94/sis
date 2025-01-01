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
        Schema::create('course_timings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offered_course_id')->constrained()->onDelete('cascade'); // Reference to the offered courses table
            $table->string('room_no')->nullable();
            $table->string('day');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_timings');
    }
};
