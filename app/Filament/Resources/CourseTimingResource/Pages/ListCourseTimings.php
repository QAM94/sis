<?php

namespace App\Filament\Resources\CourseTimingResource\Pages;

use App\Filament\Resources\CourseTimingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCourseTimings extends ListRecords
{
    protected static string $resource = CourseTimingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
