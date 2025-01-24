<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseTimingResource\Pages;
use App\Filament\Traits\HasResourcePermissions;
use App\Imports\ClassGradesImport;
use App\Models\CourseTiming;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;

class CourseTimingResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $model = CourseTiming::class;
    protected static ?string $module = 'course_timing';
    protected static ?string $navigationLabel = 'Class Schedule';
    protected static ?string $navigationGroup = 'Semester Management';

    protected static bool $shouldRegisterNavigation = true;


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('offered_course_id')
                    ->relationship('offeredCourse', 'programCourse.course.title')
                    ->required(),
                Forms\Components\TextInput::make('room_no'),
                Forms\Components\TextInput::make('day')->required(),
                Forms\Components\TimePicker::make('start_time')->required(),
                Forms\Components\TimePicker::make('end_time')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('offeredCourse.course.title')->label('Course'),
                Tables\Columns\TextColumn::make('offeredCourse.course.crn')->label('Code'),
                Tables\Columns\TextColumn::make('room_no')->label('Room No'),
                Tables\Columns\TextColumn::make('day')->label('Day'),
                Tables\Columns\TextColumn::make('start_time')->label('Start Time')->time(),
                Tables\Columns\TextColumn::make('end_time')->label('End Time')->time(),
                Tables\Columns\TextColumn::make('offeredCourse.instructor.full_name')->label('Instructor'),
                Tables\Columns\TextColumn::make('offeredCourse.studentCount')->label('Students'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Download Attendance Sheet
                Tables\Actions\Action::make('attendance_sheet')
                    ->label('Attendance Sheet')
                    ->icon('heroicon-o-document-text')
                    ->url(function ($record) {
                        return route('attendance_sheet.download', ['id' => $record->id]);
                    })
                    ->openUrlInNewTab(),

                // Download Grade Sheet
                Tables\Actions\Action::make('grade_sheet')
                    ->label('Grade Sheet')
                    ->icon('heroicon-o-document-text')
                    ->url(function ($record) {
                        return route('grade_sheet.download', ['id' => $record->id]);
                    })
                    ->openUrlInNewTab(),

                // Upload Grade Sheet
                Tables\Actions\Action::make('upload_grade_sheet')
                    ->label('Upload Grade Sheet')
                    ->icon('heroicon-o-document-text')
                    ->form([
                        Forms\Components\FileUpload::make('grade_sheet')
                            ->label('Upload Grade Sheet')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                            ->required()->directory('grade_sheets'),
                    ])
                    ->action(function (array $data, $record) {
                        // Determine if the file is stored in the public directory
                        $filePath = public_path("storage/{$data['grade_sheet']}"); // For public storage

                        // Ensure the file exists before processing
                        if (!file_exists($filePath)) {
                            Notification::make()
                                ->title('File Not Found')
                                ->danger()
                                ->send();
                        }
                        try {
                            // Call your existing import logic
                            Excel::import(new ClassGradesImport($record->id), $filePath);
                            return Notification::make()
                                ->title('Grade Sheet Uploaded')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            return redirect()->back()->with('error', 'Failed to import grades: ' . $e->getMessage());
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
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
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('offeredCourse.studentEnrollments', function ($query) {
                $query->where('student_enrollment_details.status', '!=', 'Dropped');
            })
            ->orderBy('created_at', 'DESC');
    }

}
