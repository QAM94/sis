<?php

namespace App\Filament\Widgets;

use App\Models\FeeVoucher;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentPaymentsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 2;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                FeeVoucher::query()->with(['studentEnrollment.student', 'studentEnrollment.program'])
                    ->orderBy('payment_date', 'desc')->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('studentEnrollment.student.full_name')
                    ->label('Student'),
                Tables\Columns\TextColumn::make('studentEnrollment.program.title')
                    ->label('Program'),
                Tables\Columns\TextColumn::make('voucher_number')->label('Voucher No.'),
                Tables\Columns\TextColumn::make('total_amount')->label('Total Amount'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'primary' => 'Pending',
                        'success' => 'Confirmed',
                        'danger' => 'Rejected',
                    ]),
                Tables\Columns\TextColumn::make('payment_date')->label('Payment Date')->date()
            ]);
    }
}
