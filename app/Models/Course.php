<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Course extends Model
{
    use SoftDeletes;
    protected $fillable = ['crn', 'title', 'description'];
    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(Instructor::class, 'instructor_courses');
    }
    public function programs(): HasManyThrough
    {
        return $this->HasManyThrough(Program::class, ProgramCourse::class,
            'course_id', 'program_id');
    }
    public function coursesList()
    {
        return $this->select(DB::raw('CONCAT(crn, "-", title) as course_name'))->get();
    }
}
