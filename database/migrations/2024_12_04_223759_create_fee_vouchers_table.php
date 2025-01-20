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
        Schema::create('fee_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_enrollment_id')->constrained()->onDelete('cascade'); // Reference to the student_enrollments table
            $table->string('voucher_number')->unique();
            $table->decimal('total_amount', 10, 2);
            $table->json('fee_breakdown')->nullable(); // Store detailed breakdown (JSON)
            $table->enum('status', ['Pending', 'Confirmed', 'Rejected']);
            $table->date('payment_date')->nullable();
            $table->string('payment_proof')->nullable(); // Path to the uploaded proof image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_vouchers');
    }
};
