<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentEnrollment extends Model
{
    protected $fillable = ['student_id', 'semester_id', 'status', 'course_count'];

    public function enrollmentDetails(): HasMany
    {
        return $this->hasMany(StudentEnrollmentDetail::class, 'student_enrollment_id');
    }
}
