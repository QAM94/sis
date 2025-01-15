<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = ['crn', 'title', 'description'];
    protected $appends = ['coded_title'];

    public function getCodedTitleAttribute()
    {
        return $this->crn . ' - ' . $this->title;
    }

    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(Instructor::class, 'instructor_courses');
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'program_courses')
            ->withPivot('domain_id', 'semester', 'hours', 'units')
            ->withTimestamps();
    }

    public function programCourses()
    {
        return $this->hasMany(ProgramCourse::class);
    }

    public function coursesList()
    {
        return $this->select(DB::raw('CONCAT(crn, "-", title) as course_name'))->get();
    }

}
