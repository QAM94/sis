<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentEnrollmentDetail extends Model
{
    public $timestamps = false;

    protected $fillable = ['student_enrollment_id', 'offered_course_id', 'enrolled_at', 'dropped_at',
        'status'];
}
