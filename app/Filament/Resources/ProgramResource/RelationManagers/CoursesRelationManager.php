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
                Tables\Columns\TextColumn::make('crn')->label('CRN'),
                Tables\Columns\TextColumn::make('pivot.domain_id')
                    ->label('Domain')
                    ->formatStateUsing(fn ($state) => Domain::find($state)?->title ?? 'N/A'),
                Tables\Columns\TextColumn::make('pivot.credits')
                    ->label('Credits'),
                Tables\Columns\TextColumn::make('pivot.credits_extra')
                    ->label('Extra Credits'),
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

                        Forms\Components\TextInput::make('credits')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5),

                        Forms\Components\TextInput::make('credits_extra')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5)
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
