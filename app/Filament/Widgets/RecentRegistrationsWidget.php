<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentRegistrationsWidget extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Student::query()->orderBy('created_at', 'desc')->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('reg_no')
                    ->label('Reg No.'),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Student'),
                Tables\Columns\TextColumn::make('contact')
                    ->label('Contact'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status'),
            ]);
    }
}
