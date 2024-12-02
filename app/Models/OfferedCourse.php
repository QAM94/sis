<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class OfferedCourse extends Model
{
    protected $fillable = ['program_id', 'program_course_id', 'instructor_id', 'semester_id'];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function programCourse()
    {
        return $this->belongsTo(ProgramCourse::class, 'program_course_id');
    }

    public function timings(): HasMany
    {
        return $this->hasMany(CourseTiming::class, 'offered_course_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->BelongsTo(Instructor::class);
    }

    public function semester(): BelongsTo
    {
        return $this->BelongsTo(Semester::class);
    }

    public function studentEnrollments(): hasManyThrough
    {
        return $this->hasManyThrough(
            StudentEnrollment::class,
            StudentEnrollmentDetail::class,
            'offered_course_id',
            'id',
            'id',
            'student_enrollment_id'
        );
    }
}
