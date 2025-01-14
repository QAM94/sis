<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use SoftDeletes;

    protected $fillable = ['type', 'title', 'description'];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_programs');
    }

    public function programCourses(): HasMany
    {
        return $this->hasMany(ProgramCourse::class);
    }

    public function programFee(): HasOne
    {
        return $this->hasOne(ProgramFee::class);
    }

    public function studentPrograms(): HasMany
    {
        return $this->hasMany(StudentProgram::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'program_courses')
            ->withPivot('domain_id', 'semester', 'hours', 'units')
            ->withTimestamps();
    }
}
