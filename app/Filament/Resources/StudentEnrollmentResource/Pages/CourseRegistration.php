<?php

namespace App\Filament\Resources\StudentEnrollmentResource\Pages;

use App\Filament\Resources\StudentEnrollmentResource;
use App\Models\OfferedCourse;
use App\Models\Semester;
use App\Models\StudentEnrollment;
use App\Models\StudentEnrollmentDetail;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;

class CourseRegistration extends Page
{
    protected static string $resource = StudentEnrollmentResource::class;

    protected static string $view = 'filament.resources.student-enrollment-resource.pages.course-registration';

    public function getSemester()
    {
        return Semester::where('reg_begin_at', '<=', now())
            ->where('reg_lock_at', '>=', now())
            ->first();
    }

    public function getOfferedCourses()
    {
        $student = Auth::user()->student; // Assuming `Student` is linked to `User`

        $currentSemester = $this->getSemester();

        if (!$currentSemester) {
            return [];
        }

        return OfferedCourse::where('program_id', $student->program_id)
            ->where('type', $currentSemester->type)
            ->where('year', $currentSemester->year)
            ->with(['programCourse.course', 'programCourse.domain'])
            ->get();
    }

    public function enrollInCourse($offeredCourseId)
    {
        $student = Auth::user()->student;
        $currentSemester = $this->getSemester();

        if (!$currentSemester) {
            return redirect()->back()->with('error', 'Registration is currently closed.');
        }

        $enrollment = StudentEnrollment::firstOrCreate([
            'student_id' => $student->id,
            'semester_id' => $currentSemester->id,
        ]);

        StudentEnrollmentDetail::create([
            'student_enrollment_id' => $enrollment->id,
            'offered_course_id' => $offeredCourseId,
            'status' => 'Enrolled',
        ]);

        return redirect()->back()->with('success', 'Course added successfully!');
    }

    public function dropCourse($offeredCourseId)
    {
        $student = Auth::user()->student;
        $currentSemester = $this->getSemester();

        if (!$currentSemester) {
            return redirect()->back()->with('error', 'Cannot drop courses outside the registration window.');
        }

        $enrollment = StudentEnrollment::where('student_id', $student->id)
            ->where('semester_id', $currentSemester->id)
            ->first();

        if (!$enrollment) {
            return redirect()->back()->with('error', 'No enrollment record found.');
        }

        $enrollmentDetail = StudentEnrollmentDetail::where('student_enrollment_id', $enrollment->id)
            ->where('offered_course_id', $offeredCourseId)
            ->first();

        if ($enrollmentDetail) {
            $enrollmentDetail->update([
                'status' => 'Dropped',
                'dropped_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Course dropped successfully!');
        }

        return redirect()->back()->with('error', 'Course not found in your enrollment.');
    }
}
