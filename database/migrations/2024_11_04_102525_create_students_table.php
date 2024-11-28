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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Reference to the users table
            $table->foreignId('program_id')->constrained()->onDelete('cascade'); // Reference to the programs table
            $table->string('reg_no')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('gender', ['Male', 'Female']);
            $table->string('contact');
            $table->string('email');
            $table->string('address');
            $table->string('postcode');
            $table->string('nationality');
            $table->string('sin');
            $table->date('dob');
            $table->date('enrollment_date');
            $table->enum('status', ['Enrolled', 'Completed', 'Suspended', 'Withdrawn']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
