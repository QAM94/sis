<?php

namespace App\Filament\Resources\OfferedCourseResource\Pages;

use App\Filament\Resources\OfferedCourseResource;
use App\Models\OfferedCourse;
use App\Models\Semester;
use App\Models\StudentEnrollment;
use App\Models\StudentEnrollmentDetail;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Concerns\InteractsWithTable;

class OfferedCourses extends ListRecords
{
    use InteractsWithTable;
    protected static string $resource = OfferedCourseResource::class;

    protected static string $view = 'filament.resources.offered-course-resource.pages.offered-courses';

    // Custom Title and Navigation Label
    protected static ?string $title = 'Course Registration';
    protected static ?string $navigationLabel = 'Course Registration';

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected function getTableQuery(): ?Builder
    {
        if(!empty(Auth::user()->student)) {
            return OfferedCourse::query()->with(['program', 'programCourse.course', 'instructor', 'timings'])
                ->where('program_id', Auth::user()->student->program_id);
        }
        return null;
    }

    protected function getTableColumns(): array
    {
        return [
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
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            //
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('add')
                ->label('Add')
                ->icon('heroicon-o-plus-circle') // Icon for the action
                ->action(function ($record) {
                    // Custom logic to add the course for the student
                    $student = auth()->user()->student; // Assuming `student` relationship exists on the user
                    $currentSemester = Semester::where('reg_begin_at', '<=', now())
                        ->where('reg_lock_at', '>=', now())
                        ->first();

                    if (!$currentSemester) {
                        Notification::make()
                            ->title('Registration is closed.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Create or find the enrollment record
                    $enrollment = StudentEnrollment::firstOrCreate([
                        'student_id' => $student->id,
                        'semester_id' => $currentSemester->id,
                    ]);

                    // Add course to enrollment details
                    StudentEnrollmentDetail::firstOrCreate([
                        'student_enrollment_id' => $enrollment->id,
                        'offered_course_id' => $record->id,
                    ]);

                    Notification::make()
                        ->title('Course added successfully!')
                        ->success()
                        ->send();
                }),

            Tables\Actions\Action::make('drop')
                ->label('Drop')
                ->icon('heroicon-o-x-circle') // Icon for the action
                ->action(function ($record) {
                    // Custom logic to drop the course for the student
                    $student = auth()->user()->student; // Assuming `student` relationship exists on the user
                    $currentSemester = Semester::where('reg_begin_at', '<=', now())
                        ->where('reg_lock_at', '>=', now())
                        ->first();

                    if (!$currentSemester) {
                        Notification::make()
                            ->title('Cannot drop courses outside the registration window.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Find the enrollment and details
                    $enrollment = StudentEnrollment::where('student_id', $student->id)
                        ->where('semester_id', $currentSemester->id)
                        ->first();

                    if (!$enrollment) {
                        Notification::make()
                            ->title('No enrollment record found.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $enrollmentDetail = StudentEnrollmentDetail::where('student_enrollment_id', $enrollment->id)
                        ->where('offered_course_id', $record->id)
                        ->first();

                    if ($enrollmentDetail) {
                        $enrollmentDetail->update([
                            'status' => 'Dropped',
                            'dropped_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Course dropped successfully!')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Course not found in your enrollment.')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }


    protected function getTableHeaderActions(): array
    {
        return [

        ];
    }

    protected function getTableBulkActions(): array
    {
        return [

        ];
    }



}
