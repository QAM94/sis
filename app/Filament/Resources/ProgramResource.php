<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramResource\Pages;
use App\Filament\Resources\ProgramResource\RelationManagers;
use App\Filament\Traits\HasResourcePermissions;
use App\Models\Program;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProgramResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $model = Program::class;
    protected static ?string $module = 'program';
    protected static ?string $navigationGroup = 'Curriculum Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->options(['Diploma' => 'Diploma',
                        'Under-Graduate' => 'Under-Graduate',
                        'Graduate' => 'Graduate',
                        'Postgraduate' => 'Postgraduate'])
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('Type')
                    ->options(['Diploma' => 'Diploma',
                        'Under-Graduate' => 'Under-Graduate',
                        'Graduate' => 'Graduate',
                        'Postgraduate' => 'Postgraduate'
                    ])
                    ->attribute('type'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CoursesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrograms::route('/'),
            'create' => Pages\CreateProgram::route('/create'),
            'view' => Pages\ViewProgram::route('/{record}'),
            'edit' => Pages\EditProgram::route('/{record}/edit'),
            'offerCourses' => \App\Filament\Resources\OfferedCourseResource\Pages\CreateOfferedCourse::route('/create/{record}')
        ];
    }
}
