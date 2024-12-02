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

    protected $student, $currentSemester, $enrolledCourseCount;

    public function __construct()
    {
        $this->student = Auth::user()->student ?? null;
        $this->currentSemester = Semester::whereDate('reg_begin_at', '<=', now())
            ->where('reg_lock_at', '>=', now())
            ->first();

        if ($this->student && $this->currentSemester) {
            $this->enrolledCourseCount = StudentEnrollmentDetail::whereHas('studentEnrollment', function ($query) {
                $query->where(['student_id' => $this->student->id, 'semester_id' => $this->currentSemester->id]);
            })->where('status', 'Enrolled')->count();
        } else {
            $this->enrolledCourseCount = 0; // Default to zero if conditions aren't met
        }
    }

    public function getTitle(): string
    {
        if ($this->currentSemester) {
            return "Course Registration: {$this->currentSemester->type} {$this->currentSemester->year}";
        }

        return "Course Registration";
    }

    protected function getTableQuery(): ?Builder
    {
        if (!$this->student || !$this->currentSemester) {
            return OfferedCourse::query()->limit(0);
        }
        return OfferedCourse::query()
            ->with(['program', 'programCourse.course', 'instructor', 'timings'])
            ->where('program_id', $this->student->program_id)
            ->where('semester_id', $this->currentSemester->id)
            ->whereDoesntHave('studentEnrollments', function ($query) {
                $query->where('student_id', $this->student->id)
                    ->whereHas('enrollmentDetails', function ($subQuery) {
                        $subQuery->where('status', 'Completed')
                            ->whereColumn('program_course_id', 'offered_courses.program_course_id');
                    });
            });
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
        if (!$this->currentSemester) {
            Notification::make()
                ->title('Registration is closed.')
                ->danger()
                ->send();
            return [];
        }
        return [
            Tables\Actions\Action::make('add')
                ->label('Add')
                ->icon('heroicon-o-plus-circle')
                ->action(function ($record) {
                    $this->enrollInCourse($record->id);
                    $this->redirect(static::class);
                })
                ->visible(function ($record) {
                    return (!$this->checkifCourseExists($record->id) &&
                        $this->enrolledCourseCount < ($this->currentSemester->max_courses ?? 6));
                }),

            Tables\Actions\Action::make('drop')
                ->label('Drop')
                ->icon('heroicon-o-x-circle')
                ->action(function ($record) {
                    $this->dropCourse($record->id);
                    $this->redirect(static::class);
                })
                ->visible(function ($record) {
                    return $this->checkifCourseExists($record->id);
                })
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

    public function enrollInCourse($offeredCourseId)
    {
        $offeredCourse = OfferedCourse::find($offeredCourseId);
        $isRegistered = StudentEnrollmentDetail::whereHas('studentEnrollment', function ($query) {
            $query->where('student_id', $this->student->id);
        })->whereIn('status', ['Enrolled', 'Completed'])
            ->whereHas('offeredCourse', function ($query) use ($offeredCourse) {
                $query->where('program_course_id', $offeredCourse->program_course_id);
            })->exists();

        if(!$isRegistered) {
            // Create or find the enrollment record
            $enrollment = StudentEnrollment::firstOrCreate([
                'student_id' => $this->student->id,
                'semester_id' => $this->currentSemester->id,
            ]);

            // Add course to enrollment details
            StudentEnrollmentDetail::updateOrCreate([
                'student_enrollment_id' => $enrollment->id,
                'offered_course_id' => $offeredCourseId,
            ], [
                'dropped_at' => null,
                'status' => 'Enrolled',
            ]);

            $enrollment->course_count = $this->enrolledCourseCount + 1;
            $enrollment->save();

            Notification::make()
                ->title('Course added successfully!')
                ->success()
                ->send();
        }
        else {
            Notification::make()
                ->title('Course already registered!')
                ->danger()
                ->send();
        }

    }

    public function dropCourse($offeredCourseId)
    {
        $enrollmentDetail = StudentEnrollmentDetail::where('offered_course_id', $offeredCourseId)
            ->first();
        if ($enrollmentDetail) {
            $enrollmentDetail->update([
                'status' => 'Dropped',
                'dropped_at' => now(),
            ]);
            $enrollment = StudentEnrollment::find($enrollmentDetail->student_enrollment_id);
            $enrollment->course_count = $this->enrolledCourseCount - 1;
            $enrollment->save();
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
    }

    public function checkifCourseExists($offeredCourseId)
    {
        return StudentEnrollmentDetail::whereHas('studentEnrollment', function ($query) {
            $query->where(['student_id' => $this->student->id, 'semester_id' => $this->currentSemester->id]);
        })->where(['offered_course_id' => $offeredCourseId, 'status' => 'Enrolled'])->exists();
    }


}
