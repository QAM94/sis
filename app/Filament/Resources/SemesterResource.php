<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SemesterResource\Pages;
use App\Filament\Traits\HasResourcePermissions;
use App\Models\Semester;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SemesterResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $model = Semester::class;
    protected static ?string $module = 'semester';
    protected static ?string $navigationGroup = 'Semester Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->options(['Spring' => 'Spring', 'Summer' => 'Summer', 'Fall' => 'Fall'])
                    ->required(),
                Forms\Components\Select::make('year')
                    ->options([
                        date('Y') => date('Y'), // Current year
                        date('Y', strtotime('+1 year')) => date('Y', strtotime('+1 year')) // Next year
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('start_date')
                    ->required()
                    ->label('Start Date'),
                Forms\Components\DatePicker::make('end_date')
                    ->required()
                    ->label('End Date'),
                Forms\Components\DatePicker::make('reg_begin_at')
                    ->required()
                    ->label('Registration Start'),
                Forms\Components\DatePicker::make('reg_lock_at')
                    ->required()
                    ->label('Registration End'),
                Forms\Components\TextInput::make('min_courses')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(6)
                    ->required()
                    ->label('Minimum Courses Required'),
                Forms\Components\TextInput::make('max_courses')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(99)
                    ->required()
                    ->label('Maximum Courses Allowed')
                    ->helperText('Enter 99 if unlimited'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->sortable()
                    ->searchable()
                    ->label('Semester Type'),
                Tables\Columns\TextColumn::make('year')
                    ->sortable()
                    ->searchable()
                    ->label('Year'),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->date(),
                Tables\Columns\TextColumn::make('reg_begin_at')
                    ->label('Registration Start')
                    ->date(),
                Tables\Columns\TextColumn::make('reg_lock_at')
                    ->label('Registration End')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Custom Button Action
                Tables\Actions\Action::make('offerCourses')
                    ->label('Offer Courses')
                    ->icon('heroicon-o-rectangle-stack')
                    ->url(fn($record) => route('filament.admin.resources.offered-courses.index', ['semester_id' => $record->id]))
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSemesters::route('/'),
            'create' => Pages\CreateSemester::route('/create'),
            'edit' => Pages\EditSemester::route('/{record}/edit'),
        ];
    }
}
