<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseTiming extends Model
{
    protected $fillable = ['offered_course_id', 'room_no', 'day', 'start_time', 'end_time'];

    public function offeredCourse(): BelongsTo
    {
        return $this->belongsTo(OfferedCourse::class);
    }
}
