<?php

namespace App\Filament\Resources\OfferedCourseResource\Pages;

use App\Filament\Resources\OfferedCourseResource;
use App\Models\OfferedCourse;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateOfferedCourse extends CreateRecord
{
    protected static string $resource = OfferedCourseResource::class;
    public function getTitle(): string
    {
        $programId = request()->get('program_id');
        $programTitle = \App\Models\Program::find($programId)?->title ?? 'All Programs';

        return "Offer Course for: {$programTitle}";
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Check if a record with the same combination exists
        $exists = \App\Models\OfferedCourse::where([
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
    }
}
