<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class OfferedCourse extends Model
{
    protected $fillable = ['program_id', 'program_course_id', 'instructor_id', 'semester_id', 'status'];

    protected $appends = ['studentCount'];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function programCourse()
    {
        return $this->belongsTo(ProgramCourse::class, 'program_course_id');
    }

    public function course()
    {
        return $this->hasOneThrough(
            Course::class,      // Final model to access
            ProgramCourse::class, // Intermediate model
            'id',               // Foreign key on ProgramCourse
            'id',               // Foreign key on Course
            'program_course_id', // Local key on OfferedCourse
            'course_id'         // Local key on ProgramCourse
        );
    }

    public function timings(): HasMany
    {
        return $this->hasMany(CourseTiming::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->BelongsTo(Instructor::class);
    }

    public function semester(): BelongsTo
    {
        return $this->BelongsTo(Semester::class);
    }
    public function studentEnrollmentDetails()
    {
        return $this->hasMany(StudentEnrollmentDetail::class, 'offered_course_id');
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

    public static function courseCount($program_id = 0)
    {
        $semester = Semester::getCurrentSemester();
        if($program_id == 0) {
            return self::where(['semester_id' => $semester->id])->count();
        }
        return self::where(['program_id' => $program_id,
            'semester_id' => $semester->id])->count();
    }

    public function getStudentCountAttribute()
    {
        return StudentEnrollment::where('semester_id', $this->semester_id)
            ->where('status', '!=', 'Draft')->whereHas('enrollmentDetails', function ($query) {
                $query->where('offered_course_id', $this->id)->where('status', '!=', 'Dropped');
            })->count();
    }
}
