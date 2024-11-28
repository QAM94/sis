<?php

namespace App\Filament\Resources\OfferedCourseResource\Pages;

use App\Filament\Resources\OfferedCourseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOfferedCourse extends EditRecord
{
    protected static string $resource = OfferedCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
