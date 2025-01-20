<?php

namespace App\Filament\Widgets;

use App\Models\Announcement;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentAnnouncementsWidget extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Announcement::query()->orderBy('created_at', 'desc')->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->limit(30)
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'primary' => 'info',
                        'warning' => 'warning',
                        'danger' => 'error',
                        'success' => 'success',
                    ]),
                Tables\Columns\TextColumn::make('is_active')
                    ->label('Active')
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
            ]);
    }
}
