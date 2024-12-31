<?php

namespace App\Filament\Resources\StudentProgramResource\Pages;

use App\Filament\Resources\StudentProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentProgram extends EditRecord
{
    protected static string $resource = StudentProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
