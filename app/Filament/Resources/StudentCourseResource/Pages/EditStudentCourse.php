<?php

namespace App\Filament\Resources\StudentCourseResource\Pages;

use App\Filament\Resources\StudentCourseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentCourse extends EditRecord
{
    protected static string $resource = StudentCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
