<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'reg_no', 'first_name', 'last_name', 'contact', 'email',
        'address', 'postcode', 'nationality', 'sin', 'dob', 'gender'];

    protected $appends = ['full_name'];

    public function getFullNameAttribute() {
        return $this->first_name.' '.$this->last_name;
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'student_programs');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function programCourses(): BelongsToMany
    {
        return $this->belongsToMany(ProgramCourse::class, 'student_courses')
            ->withPivot('semester', 'enrolled_at', 'dropped_at', 'status');
    }
}
