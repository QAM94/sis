<?php

namespace App\Imports;

use App\Models\CourseTiming;
use App\Models\StudentEnrollmentDetail;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClassGradesImport implements ToCollection, WithHeadingRow
{
    protected $schedule_id;

    // Constructor to accept schedule_id
    public function __construct($schedule_id)
    {
        $this->schedule_id = $schedule_id;
    }

    public function collection(Collection $collection)
    {
        $start = 9;
        $transcripts = [];
        while ($collection[$start][0] != NULL) {
            array_push($transcripts, [
                    'studentId' => $collection[$start][0],
                    'score' => $collection[$start][3],
                    'grade' => $collection[$start][4],
                    'comments' => $collection[$start][5]
                ]
            );
            $start++;
        }
        $course_id = CourseTiming::find($this->schedule_id)->offered_course_id;

        foreach ($transcripts as $transcript) {
            $enrollment = StudentEnrollmentDetail::whereHas('student', function ($query) use ($transcript) {
                $query->where('reg_no', $transcript['studentId']);
            })->where('offered_course_id', $course_id)->first();

            $enrollment->score = $transcript['score'];
            $enrollment->grade = $transcript['grade'];
            $enrollment->comments = $transcript['comments'];
            $enrollment->completed_at = date('Y-m-d');
            $enrollment->status = 'Completed';
            $enrollment->save();
        }
    }
}

