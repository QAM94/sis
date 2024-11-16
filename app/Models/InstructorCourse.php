<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class InstructorCourse extends Model
{
    public $timestamps = false;

    public function instructor(): BelongsToMany
    {
        return $this->belongsToMany(Instructor::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class);
    }
}
