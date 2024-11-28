<?php

namespace App\Filament\Resources\CourseTimingResource\Pages;

use App\Filament\Resources\CourseTimingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCourseTiming extends EditRecord
{
    protected static string $resource = CourseTimingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
