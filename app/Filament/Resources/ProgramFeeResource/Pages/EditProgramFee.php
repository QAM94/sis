<?php

namespace App\Filament\Resources\ProgramFeeResource\Pages;

use App\Filament\Resources\ProgramFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProgramFee extends EditRecord
{
    protected static string $resource = ProgramFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
