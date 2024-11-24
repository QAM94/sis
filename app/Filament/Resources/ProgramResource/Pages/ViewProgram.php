<?php

namespace App\Filament\Resources\ProgramResource\Pages;

use App\Filament\Resources\ProgramResource;
use Filament\Forms\Components\Tabs;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables;
use Filament\Tables\Table;

class ViewProgram extends ViewRecord
{
    protected static string $resource = ProgramResource::class;

    protected function getFormSchema(): array
    {
        return [
            Tabs::make('Dynamic Tabs')
                ->tabs(function () {
                    $tabs = [];
                    $semesters = [1,2,3,4,5,6,7,8]; // Fetch domains dynamically

                    foreach ($semesters as $semester) {
                        $tabs[] = Tab::make('Semester ' . $semester)
                            ->schema([
                                Table::make('courses')
                                    ->columns([
                                        Tables\Columns\TextColumn::make('course_name')->label('Course Name'),
                                        Tables\Columns\TextColumn::make('credits')->label('Credits'),
                                        Tables\Columns\TextColumn::make('extra_credits')->label('Extra Credits'),
                                    ])
                                    ->rows(function () use ($semester) {
                                        return $semester; // Assuming a relationship exists
                                    }),
                            ]);
                    }

                    return $tabs;
                }),
        ];
    }

}
