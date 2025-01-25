<?php

namespace App\Filament\Resources\TranscriptResource\Pages;

use App\Filament\Resources\TranscriptResource;
use Filament\Resources\Pages\ViewRecord;

class ViewTranscript extends ViewRecord
{
    protected static string $resource = TranscriptResource::class;
    protected static string $view = 'filament.resources.offered-course-resource.pages.view-transcript';
    protected static ?string $title = 'View Transcript';

    protected function getViewData(): array
    {
        $record = $this->getRecord();
        return [
            'course' => $record, // Pass the course details
            'students' => $record->studentEnrollmentDetails, // Pass the enrolled students
        ];
    }
}

