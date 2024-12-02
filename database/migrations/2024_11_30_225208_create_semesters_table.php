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
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Spring', 'Summer', 'Fall']);
            $table->integer('year');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('reg_begin_at');
            $table->date('reg_lock_at');
            $table->tinyInteger('min_courses');
            $table->tinyInteger('max_courses');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
