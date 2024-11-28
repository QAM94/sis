<?php

namespace App\Filament\Resources\OfferedCourseResource\Pages;

use App\Filament\Resources\OfferedCourseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOfferedCourses extends ListRecords
{
    protected static string $resource = OfferedCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
