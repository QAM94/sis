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
        Schema::create('program_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->decimal('admission_fee', 8, 2);
            $table->decimal('security_deposit', 8, 2);
            $table->decimal('reg_fee', 8, 2);
            $table->decimal('tution_fee', 8, 2);
            $table->decimal('transport_fee', 8, 2);
            $table->decimal('other_fee', 8, 2);
            $table->enum('fee_by', ['course', 'credit', 'semester'])->default('course');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_fees');
    }
};
