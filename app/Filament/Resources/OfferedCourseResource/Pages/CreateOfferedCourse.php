<?php

namespace App\Filament\Resources\OfferedCourseResource\Pages;

use App\Filament\Resources\OfferedCourseResource;
use App\Models\OfferedCourse;
use App\Models\Semester;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateOfferedCourse extends CreateRecord
{
    protected static string $resource = OfferedCourseResource::class;
    public function getTitle(): string
    {
        $semesterId = request()->get('semester_id');
        $semester = Semester::find($semesterId);
        if(!empty($semester)) {
            return "Offered Courses for: $semester->type $semester->year";
        }
        return "Offered Courses";
    }

    /*protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Check if a record with the same combination exists
        $exists = OfferedCourse::where([
            'semester_id' => $data['semester_id'],
            'instructor_id' => $data['instructor_id'],
            'program_course_id' => $data['program_course_id']
        ])->exists();

        if ($exists) {
            Notification::make()
                ->warning()
                ->title('A record with the same combination already exists.')
                ->persistent()
                ->send();
            $this->halt();
        }

        return $data;
    }*/
}
