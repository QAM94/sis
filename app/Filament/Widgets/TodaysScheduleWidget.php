<?php

namespace App\Filament\Widgets;

use App\Models\CourseTiming;
use App\Models\Semester;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TodaysScheduleWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 2;
    public function table(Table $table): Table
    {
        $semester = Semester::getCurrentSemester();
        return $table
            ->query(
                CourseTiming::query()->whereHas('offeredCourse', function ($query) use ($semester) {
                    $query->where('semester_id', $semester->id);
                })->with(['offeredCourse.course', 'offeredCourse.instructor'])
                    ->where('day', date('l'))->orderBy('start_time', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('offeredCourse.course.title')->label('Course'),
                Tables\Columns\TextColumn::make('offeredCourse.course.crn')->label('Code'),
                Tables\Columns\TextColumn::make('room_no')->label('Room No'),
                Tables\Columns\TextColumn::make('start_time')->label('Start Time')->time(),
                Tables\Columns\TextColumn::make('end_time')->label('End Time')->time(),
                Tables\Columns\TextColumn::make('offeredCourse.instructor.full_name')->label('Instructor'),
                Tables\Columns\TextColumn::make('offeredCourse.studentCount')->label('Students'),
            ]);
    }
}
