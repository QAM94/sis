<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseTimingResource\Pages;
use App\Filament\Traits\HasResourcePermissions;
use App\Models\CourseTiming;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CourseTimingResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $model = CourseTiming::class;
    protected static ?string $module = 'course_timing';
    protected static bool $shouldRegisterNavigation = false;


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('offered_course_id')
                    ->relationship('offeredCourse', 'programCourse.course.title')
                    ->required(),
                Forms\Components\TextInput::make('day')->required(),
                Forms\Components\TimePicker::make('start_time')->required(),
                Forms\Components\TimePicker::make('end_time')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('offeredCourse.programCourse.course.title')->label('Course'),
                Tables\Columns\TextColumn::make('day')->label('Day'),
                Tables\Columns\TextColumn::make('start_time')->label('Start Time')->time(),
                Tables\Columns\TextColumn::make('end_time')->label('End Time')->time(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListCourseTimings::route('/'),
            'create' => Pages\CreateCourseTiming::route('/create'),
            'edit' => Pages\EditCourseTiming::route('/{record}/edit'),
        ];
    }
}
