<?php

namespace App\Filament\Resources\OfferedCourseResource\Pages;

use App\Filament\Resources\OfferedCourseResource;
use App\Models\OfferedCourse;
use App\Models\FeeVoucher;
use App\Models\Program;
use App\Models\ProgramFee;
use App\Models\Semester;
use App\Models\StudentEnrollment;
use App\Models\StudentEnrollmentDetail;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class OfferedCourses extends ListRecords
{
    use InteractsWithTable;

    protected static string $resource = OfferedCourseResource::class;

    protected static string $view = 'filament.resources.offered-course-resource.pages.offered-courses';

    // Custom Title and Navigation Label
    protected static ?string $title = 'Course Registration';
    protected static ?string $navigationLabel = 'Course Registration';

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public $student, $currentSemester, $program_id, $whereArr, $enrolledCourseCount;

    public function __construct()
    {
        $this->program_id = (int)request()->program_id;
        $this->student = Auth::user()->student ?? null;
        $this->currentSemester = Semester::getCurrentSemester();

        if ($this->student && $this->currentSemester&& $this->program_id) {
            $this->whereArr = ['student_id' => $this->student->id,
                'program_id' => $this->program_id,
                'semester_id' => $this->currentSemester->id];
            $this->enrolledCourseCount = StudentEnrollmentDetail::whereHas('studentEnrollment',
                function ($query) {
                    $query->where($this->whereArr);
                })->where('status', 'Enrolled')->count();
        } else {
            $this->whereArr = [];
            $this->enrolledCourseCount = 0; // Default to zero if conditions aren't met
        }
    }

    public function getTitle(): string
    {
        $title ="Course Registration";
        if ($this->program_id) {
            $program = Program::find($this->program_id);
            $title .= ': '.$program->title;
        }
        if ($this->currentSemester) {
            $title .= ' - '.$this->currentSemester->type.' '.$this->currentSemester->year;
        }

        return $title;
    }

    protected function getTableQuery(): ?Builder
    {
        if (!$this->student || !$this->currentSemester|| !$this->program_id) {
            return OfferedCourse::query()->limit(0);
        }
        return OfferedCourse::query()
            ->with(['program', 'programCourse.course', 'instructor', 'timings'])
            ->where('program_id', $this->program_id)
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
                    $this->redirect(route('filament.admin.resources.offered-courses.register',
                        ['program_id' => $record->program_id]));
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
                    $this->redirect(route('filament.admin.resources.offered-courses.register',
                        ['program_id' => $record->program_id]));
                })
                ->visible(function ($record) {
                    return $this->checkifCourseExists($record->id);
                })
        ];
    }


    protected function getTableHeaderActions(): array
    {
        $voucherExists = FeeVoucher::whereHas('studentEnrollment',
            function ($query) {
                $query->where($this->whereArr);
            })->exists();

        return [
            // Generate Voucher Button
            Tables\Actions\Action::make('lock_registration')
                ->label('Lock Registration')
                ->icon('heroicon-o-document-text')
                ->action(function () {
                    $this->lockRegistration();
                    $this->redirect(route('filament.admin.resources.offered-courses.register',
                        ['program_id' => $this->program_id]));
                })
                ->visible(function () use ($voucherExists) {
                    $selectedCoursesCount = StudentEnrollmentDetail::whereHas('studentEnrollment',
                        function ($query) {
                            $query->where($this->whereArr);
                        })->where('status', 'Enrolled')->count();

                    // Show Generate button if minimum courses are met and voucher doesn't exist
                    return !$voucherExists && $selectedCoursesCount >= $this->currentSemester->min_courses;
                }),

            // Download Voucher Button
            Tables\Actions\Action::make('download_voucher')
                ->label('Download Fee Voucher')
                ->icon('heroicon-o-document-text')
                ->url(function () {
                    $voucher = FeeVoucher::whereHas('studentEnrollment',
                        function ($query) {
                            $query->where(['student_id' => $this->student->id,
                                'program_id' => $this->program_id]);
                        })->where('status', 'Pending')->first();

                    if ($voucher) {
                        return route('voucher.download', ['voucher' => $voucher->id]); // Route to handle download
                    }

                    return '#';
                })
                ->visible(function () use ($voucherExists) {
                    // Show Download button only if voucher exists
                    return $voucherExists;
                }),
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
            $query->where(['student_id' => $this->student->id, 'program_id' => $this->program_id]);
        })->whereIn('status', ['Enrolled', 'Completed'])
            ->whereHas('offeredCourse', function ($query) use ($offeredCourse) {
                $query->where('program_course_id', $offeredCourse->program_course_id);
            })->exists();

        if (!$isRegistered) {
            // Create or find the enrollment record
            $enrollment = StudentEnrollment::firstOrCreate($this->whereArr);

            // Add course to enrollment details
            StudentEnrollmentDetail::updateOrCreate([
                'student_enrollment_id' => $enrollment->id,
                'offered_course_id' => $offeredCourseId,
            ], [
                'enrolled_at' => date('Y-m-d'),
                'dropped_at' => null,
                'status' => 'Enrolled',
            ]);

            $enrollment->course_count = $this->enrolledCourseCount + 1;
            $enrollment->save();

            Notification::make()
                ->title('Course added successfully!')
                ->success()
                ->send();
        } else {
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
            $query->where($this->whereArr);
        })->where(['offered_course_id' => $offeredCourseId, 'status' => 'Enrolled'])->exists();
    }

    public function lockRegistration()
    {
        $enrollment = StudentEnrollment::where($this->whereArr)->where('status', 'Draft')->first();
        if (empty($enrollment)) {
            Notification::make()
                ->title('Enrollment Not Found')
                ->danger()
                ->send();
            return;
        }
        $enrollment->status = 'Locked';
        $enrollment->save();

        $enrolledCourses = StudentEnrollmentDetail::whereHas('studentEnrollment', function ($query) {
            $query->where($this->whereArr);
        })->where('status', 'Enrolled')->with('offeredCourse.programCourse')->get();

        // Your logic to calculate fees and generate voucher
        $semesterFee = ProgramFee::getSemesterFee($this->student->id, $this->program_id, $enrolledCourses);

        $voucher_number = $this->student->reg_no.'_'.str_pad($enrollment->id, 3, '0', STR_PAD_LEFT);
        // Save the payment voucher
        FeeVoucher::create([
            'student_enrollment_id' => $enrollment->id,
            'voucher_number' => $voucher_number,
            'total_amount' => $semesterFee['total'],
            'fee_breakdown' => json_encode($semesterFee['breakdown']),
            'status' => 'Pending',
        ]);

        // Notify the user
        Notification::make()
            ->title('Registration Locked and Payment Voucher Generated Successfully!')
            ->success()
            ->send();
    }

}
