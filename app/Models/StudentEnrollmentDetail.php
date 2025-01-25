<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class StudentEnrollmentDetail extends Model
{
    public $timestamps = false;

    protected $fillable = ['student_enrollment_id', 'offered_course_id', 'score', 'grade',
        'comments', 'enrolled_at', 'dropped_at', 'completed_at', 'status'];

    public function studentEnrollment()
    {
        return $this->belongsTo(StudentEnrollment::class, 'student_enrollment_id');
    }

    public function offeredCourse(): belongsTo
    {
        return $this->belongsTo(OfferedCourse::class, 'offered_course_id');
    }

    public function student(): hasOneThrough
    {
        return $this->hasOneThrough(
            Student::class,      // Final model to access
            StudentEnrollment::class, // Intermediate model
            'id',               // Foreign key on studentEnrollment
            'id',               // Foreign key on Student
            'student_enrollment_id', // Local key on StudentEnrollmentDetail
            'student_id'         // Local key on studentEnrollment
        );
    }

    public function programCourse(): hasOneThrough
    {
        return $this->hasOneThrough(
            ProgramCourse::class,
            OfferedCourse::class,
            'id', // Foreign key on offered_courses table
            'id', // Foreign key on program_courses table
            'offered_course_id', // Local key on student_enrollment_details
            'program_course_id' // Local key on offered_courses
        );
    }
}
