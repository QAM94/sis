<?php

namespace App\Filament\Resources\ProgramResource\RelationManagers;

use App\Models\Domain;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'courses';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('crn')
            ->columns([
                Tables\Columns\TextColumn::make('pivot.domain_id')
                    ->label('Domain')
                    ->formatStateUsing(fn($state) => Domain::find($state)?->title ?? 'N/A'),
                Tables\Columns\TextColumn::make('crn')->label('CRN'),
                Tables\Columns\TextColumn::make('title')->label('Course Name'),
                Tables\Columns\TextColumn::make('pivot.semester')
                    ->label('Semester'),
                Tables\Columns\TextColumn::make('pivot.units')
                    ->label('Units'),
                Tables\Columns\TextColumn::make('pivot.hours')
                    ->label('Hours'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->recordSelect(
                        fn(Forms\Components\Select $select) => $select->placeholder('Select Course(s)'),
                    )
                    ->recordSelectSearchColumns(['title', 'crn'])
                    ->multiple()
                    ->form(fn(Tables\Actions\AttachAction $action): array => [
                        Forms\Components\Select::make('domain_id')  // Changed 'domain' to 'domain_id'
                        ->placeholder('Select Domain')
                            ->options(Domain::all()->pluck('title', 'id'))
                            ->required()
                            ->label(''),

                        $action->getRecordSelect(),

                        Forms\Components\TextInput::make('semester')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(8),

                        Forms\Components\TextInput::make('units')
                            ->required()
                            ->numeric()
                            ->step(0.5)
                            ->minValue(0)
                            ->maxValue(20),

                        Forms\Components\TextInput::make('hours')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(500)

                    ])
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
