<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use SoftDeletes;
    protected $fillable = ['type', 'title', 'description'];

    public function student() : HasMany {
        return $this->hasMany(Student::class);
    }

    public function courses(): HasManyThrough
    {
        return $this->HasManyThrough(Course::class, ProgramCourse::class,
            'program_id', 'course_id');
    }
}
