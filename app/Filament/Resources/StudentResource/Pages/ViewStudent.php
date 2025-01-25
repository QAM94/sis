<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\StudentEnrollment;
use Filament\Infolists\Components\Section;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Components\Tabs;


class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    public function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        // Fetch programs registered by the student
        $enrollments = StudentEnrollment::with(['studentProgram', 'semester'])
            ->where('student_id', $this->record->id)
            ->get();
        return $infolist
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Student Info')
                            ->icon('heroicon-o-user-circle')
                            ->schema([
                                Infolists\Components\TextEntry::make('reg_no')->label('Registration No.'),
                                Infolists\Components\TextEntry::make('first_name')->label('First Name'),
                                Infolists\Components\TextEntry::make('last_name')->label('Last Name'),
                                Infolists\Components\TextEntry::make('gender')->label('Gender'),
                                Infolists\Components\TextEntry::make('contact')->label('Contact Number'),
                                Infolists\Components\TextEntry::make('email')->label('Email Address'),
                                Infolists\Components\TextEntry::make('address')->label('Address')->columnSpan(2),
                                Infolists\Components\TextEntry::make('postcode')->label('Postcode'),
                                Infolists\Components\TextEntry::make('nationality')->label('Nationality'),
                                Infolists\Components\TextEntry::make('sin')->label('SIN'),
                                Infolists\Components\TextEntry::make('status')->label('Status'),
                                Infolists\Components\TextEntry::make('created_at')->label('Joined On')->date(),
                                Infolists\Components\TextEntry::make('updated_at')->label('Last Updated')->date(),
                            ])->columns(3),
                        Tabs\Tab::make('Enrollments')
                            ->icon('heroicon-o-inbox-stack')
                            ->schema([
                                Section::make('Registration Details')
                                    ->schema(
                                        $enrollments->map(function ($enrollment) {
                                            return Infolists\Components\Grid::make(6)
                                                ->schema([
                                                    Infolists\Components\TextEntry::make('program')
                                                        ->label('Program')
                                                        ->default($enrollment->program->title ?? 'N/A'),
                                                    Infolists\Components\TextEntry::make('program_status')
                                                        ->label('Status')
                                                        ->default($enrollment->studentProgram->status ?? 'N/A'),
                                                    Infolists\Components\TextEntry::make('semester')
                                                        ->label('Semester')
                                                        ->default($enrollment->semester ?
                                                            $enrollment->semester->type . ' ' . $enrollment->semester->year : 'N/A'),
                                                    Infolists\Components\TextEntry::make('enrollment_status')
                                                        ->label('Enrollment')
                                                        ->default($enrollment->status ?? 'N/A'),
                                                    Infolists\Components\TextEntry::make('semester_start')
                                                        ->label('Start Date')
                                                        ->default($enrollment->semester ?
                                                            date('jS M y', strtotime($enrollment->semester->start_date)) : 'N/A'),
                                                    Infolists\Components\TextEntry::make('semester_end')
                                                        ->label('End Date')
                                                        ->default($enrollment->semester ?
                                                            date('jS M y', strtotime($enrollment->semester->end_date)) : 'N/A'),
                                                ]);
                                        })->toArray()
                                    ),
                            ]),
                    ]),


            ])
            ->columns(1);
    }
}
