<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentEnrollment extends Model
{
    protected $fillable = ['student_id', 'program_id', 'semester_id', 'status', 'course_count'];

    public function enrollmentDetails(): HasMany
    {
        return $this->hasMany(StudentEnrollmentDetail::class, 'student_enrollment_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function studentProgram(): BelongsTo
    {
        return $this->belongsTo(StudentProgram::class, 'program_id', 'program_id');
    }
}
