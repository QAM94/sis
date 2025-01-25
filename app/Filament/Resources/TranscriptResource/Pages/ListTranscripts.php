<?php

namespace App\Filament\Resources\TranscriptResource\Pages;

use App\Filament\Resources\TranscriptResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTranscripts extends ListRecords
{
    protected static string $resource = TranscriptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function getTabs(): array
    {
        return [
            'Completed' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'Completed')),
            'Scheduled' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'Scheduled')),
        ];
    }

}
