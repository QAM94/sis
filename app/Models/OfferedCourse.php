<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfferedCourse extends Model
{
    protected $fillable = ['program_id', 'program_course_id', 'instructor_id', 'type', 'year'];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function timings(): HasMany
    {
        return $this->HasMany(CourseTiming::class);
    }
}
