<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProgramCourse extends Model
{
    public $timestamps = false;

    protected $fillable = ['program_id', 'domain_id', 'course_id', 'semester', 'hours', 'units'];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_courses')
            ->withPivot('semester', 'enrolled_at', 'dropped_at', 'status');
    }
}
