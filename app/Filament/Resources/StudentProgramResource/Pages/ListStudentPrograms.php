<?php

namespace App\Filament\Resources\StudentProgramResource\Pages;

use App\Filament\Resources\StudentProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentPrograms extends ListRecords
{
    protected static string $resource = StudentProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
