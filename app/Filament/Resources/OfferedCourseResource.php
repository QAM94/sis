<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfferedCourseResource\Pages;
use App\Filament\Traits\HasResourcePermissions;
use App\Models\OfferedCourse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OfferedCourseResource extends Resource
{
    use HasResourcePermissions;
    protected static ?string $model = OfferedCourse::class;
    protected static ?string $module = 'offered_course';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('program_id')
                    ->relationship('program', 'title')
                    ->required(),
                Forms\Components\Select::make('program_course_id')
                    ->relationship('programCourse', 'course.title')
                    ->required(),
                Forms\Components\Select::make('instructor_id')
                    ->relationship('instructor', 'name')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options(['Spring' => 'Spring', 'Summer' => 'Summer', 'Fall' => 'Fall'])
                    ->required(),
                Forms\Components\DatePicker::make('year')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('program.title')->label('Program'),
                Tables\Columns\TextColumn::make('programCourse.course.title')->label('Course'),
                Tables\Columns\TextColumn::make('instructor.name')->label('Instructor'),
                Tables\Columns\TextColumn::make('type')->label('Semester'),
                Tables\Columns\TextColumn::make('year')->label('Year'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')->label('Semester Type'),
                Tables\Filters\SelectFilter::make('year')->label('Year'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOfferedCourses::route('/'),
            'create' => Pages\CreateOfferedCourse::route('/create'),
            'edit' => Pages\EditOfferedCourse::route('/{record}/edit'),
        ];
    }
}
