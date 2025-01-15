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

    public static function current()
    {
        $semester = Semester::getCurrentSemester();
        $classes = self::whereHas('offeredCourse', function ($query) use ($semester) {
            $query->where('semester_id', $semester->id);
        })->with('offeredCourse.course')->get();
        return $classes->mapWithKeys(function ($class) {
            $day = substr($class->day, 0, 3);
            $time = date('H:iA', strtotime($class->start_time));
            $courseTitle = $class->offeredCourse->course->title ?? 'N/A';
            return [$class->id => "{$day} {$time} - {$courseTitle}"];
        })->toArray();
    }

    public static function getTitle($id, $type='crn')
    {
        $class = self::where('id', $id)->with('offeredCourse.course')->first();
        $day = substr($class->day, 0, 3);
        $time = date('H:iA', strtotime($class->start_time));
        $course = $class->offeredCourse->course;
        $title = $type == 'crn' ? $course->crn : $course->crn.' ('.$course->title.')';
        return $title.' - '.$day.' '.$time;
    }
}
