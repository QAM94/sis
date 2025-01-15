<?php

namespace App\Filament\Widgets;

use App\Models\Instructor;
use App\Models\OfferedCourse;
use App\Models\Program;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Students', Student::where('status', 'Active')->count())
                ->description('Enrolled in the system')
                ->color('success'),

            Stat::make('Total Instructors', Instructor::count())
                ->description('Past and Current Instructors')
                ->color('success'),

            Stat::make('Total Programs', Program::count())
                ->description('Active Programs')
                ->color('primary'),

            Stat::make('Offered Courses', OfferedCourse::courseCount())
                ->description('Currently Offered across all programs')
                ->color('warning'),
        ];
    }
}
