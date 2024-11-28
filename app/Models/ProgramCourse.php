<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProgramCourse extends Model
{
    public $timestamps = false;

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_courses')
            ->withPivot('semester', 'enrolled_at', 'dropped_at', 'status');
    }
}
