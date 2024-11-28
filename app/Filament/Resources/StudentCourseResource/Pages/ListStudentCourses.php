<?php

namespace App\Filament\Resources\StudentCourseResource\Pages;

use App\Filament\Resources\StudentCourseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentCourses extends ListRecords
{
    protected static string $resource = StudentCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
