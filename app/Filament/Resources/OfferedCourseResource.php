<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfferedCourseResource\Pages;
use App\Filament\Resources\OfferedCourseResource\RelationManagers;
use App\Filament\Traits\HasResourcePermissions;
use App\Models\Instructor;
use App\Models\OfferedCourse;
use App\Models\Program;
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
                Forms\Components\Hidden::make('semester_id')
                    ->default(fn() => request()->get('semester_id')) // Dynamically set default from URL
                    ->required(),
                Forms\Components\Select::make('program_id')
                    ->options(function () {
                        return Program::all()
                            ->mapWithKeys(function ($program) {
                                return [$program->id => $program->title];
                            });
                    })
                    ->reactive()
                    ->required()
                    ->searchable()
                    ->label('Program'),

                Forms\Components\Select::make('program_course_id')
                    ->label('Courses')
                    ->options(function (callable $get) {
                        $programId = $get('program_id'); // Get selected program
                        if ($programId) {
                            return ProgramCourse::where('program_id', $programId)
                                ->get()
                                ->mapWithKeys(function ($programCourse) {
                                    return [$programCourse->id => $programCourse->course->title]; // Map courses
                                });
                        }
                        return [];
                    })
                    ->reactive()
                    ->searchable()
                    ->preload()
                    ->required(),
                // Instructors Selector (Filtered by Course)
                Forms\Components\Select::make('instructor_id')
                    ->label('Instructors')
                    ->options(function (callable $get) {
                        $programCourseId = $get('program_course_id'); // Get selected program_course_id
                        if ($programCourseId) {
                            $programCourse = ProgramCourse::find($programCourseId);
                            return Instructor::whereHas('courses', function ($query) use ($programCourse) {
                                $query->where('courses.id', $programCourse->course_id); // Match the course_id with program_course_id
                            })->get()
                                ->mapWithKeys(function ($instructor) {
                                    return [$instructor->id => $instructor->full_name]; // Map instructors
                                });
                        }
                        return [];
                    })
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('programCourse.program.title')->label('Program'),
                Tables\Columns\TextColumn::make('programCourse.course.title')->label('Course'),
                Tables\Columns\TextColumn::make('instructor.full_name')->label('Instructor'),
                Tables\Columns\TextColumn::make('timings')
                    ->label('Timings')
                    ->formatStateUsing(function ($record) {
                        return $record->timings->map(function ($timing) {
                            return "<li>{$timing->day}: {$timing->start_time} - {$timing->end_time}</li>";
                        })->join('');
                    })
                    ->html() // Enable HTML rendering for this column
                    ->wrap() // Optional: Wrap the content for better display in the table
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('Program')
                    ->options(Program::all()->pluck('title', 'id')->toArray())
                    ->searchable()
                    ->placeholder('All Programs')
                    ->attribute('program_id')
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
            'register' => Pages\OfferedCourses::route('/register'),
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
