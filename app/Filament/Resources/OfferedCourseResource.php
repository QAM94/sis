<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfferedCourseResource\Pages;
use App\Filament\Resources\OfferedCourseResource\RelationManagers;
use App\Filament\Traits\HasResourcePermissions;
use App\Models\OfferedCourse;
use App\Models\ProgramCourse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OfferedCourseResource extends Resource
{
    use HasResourcePermissions;
    protected static ?string $model = OfferedCourse::class;
    protected static ?string $module = 'offered_course';
    protected static bool $shouldRegisterNavigation = false;



    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('program_id')
                    ->default(fn () => request()->get('program_id')) // Dynamically set default from URL
                    ->required(),
                Forms\Components\Select::make('instructor_id')
                    ->relationship('instructor', 'full_name')
                    ->reactive()
                    ->required(),
                Forms\Components\Select::make('program_course_id')
                    ->label('Courses')
                    ->options(function (callable $get) {
                        $programId = $get('program_id'); // Get program_id from the field
                        $instructorId = $get('instructor_id'); // Get instructor_id from the field
                        if ($programId && $instructorId) {
                            return ProgramCourse::where('program_id', $programId)
                                ->whereHas('course.instructors', function ($query) use ($instructorId) {
                                    $query->where('instructors.id', $instructorId);
                                })
                                ->get()
                                ->mapWithKeys(function ($programCourse) {
                                    return [$programCourse->id => $programCourse->course->title]; // Access course title via relationship
                                });
                        }
                        return [];
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options(['Spring' => 'Spring', 'Summer' => 'Summer', 'Fall' => 'Fall'])
                    ->required(),
                Forms\Components\Select::make('year')
                    ->options([
                        date('Y') => date('Y'), // Current year
                        date('Y', strtotime('+1 year')) => date('Y', strtotime('+1 year')) // Next year
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('program.title')->label('Program'),
                Tables\Columns\TextColumn::make('programCourse.course.title')->label('Course'),
                Tables\Columns\TextColumn::make('instructor.full_name')->label('Instructor'),
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
            RelationManagers\TimingsRelationManager::class
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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->when(request()->get('program_id'), function ($query, $programId) {
                $query->where('program_id', $programId);
            });
    }
}
