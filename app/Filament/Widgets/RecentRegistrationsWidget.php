<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\StudentProgram;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentRegistrationsWidget extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                StudentProgram::query()->with('student', 'program')
                    ->orderBy('enrolled_on', 'desc')->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('program.title')
                    ->label('Program'),
                Tables\Columns\TextColumn::make('student.reg_no')
                    ->label('Reg No.'),
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Student'),
                Tables\Columns\TextColumn::make('student.contact')
                    ->label('Contact'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status'),
            ]);
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}
