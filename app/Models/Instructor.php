<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instructor extends Model
{
    use SoftDeletes;
    protected $fillable = ['full_name', 'gender', 'contact', 'address', 'nationality', 'sin',
        'dob', 'joining_date', 'status'];

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'instructor_courses');
    }

}
