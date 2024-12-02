<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfferedCourseResource\Pages;
use App\Filament\Resources\OfferedCourseResource\RelationManagers;
use App\Filament\Traits\HasResourcePermissions;
use App\Models\OfferedCourse;
use App\Models\ProgramCourse;
use App\Models\Semester;
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
                    ->default(fn() => request()->get('program_id')) // Dynamically set default from URL
                    ->required(),
                Forms\Components\Select::make('semester_id')
                    ->options(function () {
                        return Semester::where('start_date', '>', date('Y-m-d'))
                            ->get()
                            ->mapWithKeys(function ($semester) {
                                return [$semester->id => "{$semester->type} {$semester->year}"];
                            });
                    })
                    ->default(function () {
                        $firstSemester = Semester::where('start_date', '>', date('Y-m-d'))
                            ->orderBy('start_date', 'asc') // Ensure the first option matches the dropdown
                            ->first();
                        return $firstSemester?->id; // Automatically set the ID of the first option
                    })
                    ->required()
                    ->label('Semester'),
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
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('semester')
                    ->label('Semester')
                    ->formatStateUsing(function ($record) {
                        return "{$record->semester->type} {$record->semester->year}";
                    }),
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
