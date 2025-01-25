<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\StudentEnrollmentDetail;

class ClassGradesImport implements ToCollection
{
    private $offered_course_id;

    public function __construct($offered_course_id)
    {
        $this->offered_course_id = $offered_course_id;
    }

    public function collection(Collection $collection)
    {
        // Start reading data from row 9 (assuming headers are above row 9)
        $startRow = 9;

        foreach ($collection as $index => $row) {
            if ($index < $startRow || empty($row[0])) {
                continue; // Skip rows before the start or empty rows
            }

            $studentRegNo = $row[0] ?? null; // Registration number
            $score = $row[3] ?? null;       // Score
            $grade = $row[4] ?? null;       // Grade
            $comments = $row[5] ?? null;    // Comments

            if ($studentRegNo) {
                // Match enrollment by student registration number and course ID
                $enrollment = StudentEnrollmentDetail::whereHas('student', function ($query) use ($studentRegNo) {
                    $query->where('reg_no', $studentRegNo);
                })->where('offered_course_id', $this->offered_course_id)->first();

                if ($enrollment) {
                    $enrollment->update([
                        'score' => $score,
                        'grade' => $grade,
                        'comments' => $comments,
                        'completed_at' => date('Y-m-d'),
                        'status' => 'Completed',
                    ]);
                } else {
                    Log::warning("Enrollment not found for student registration number: {$studentRegNo}");
                }
            }
        }
    }
}
