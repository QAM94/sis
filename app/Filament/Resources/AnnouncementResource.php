<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Filament\Resources\AnnouncementResource\RelationManagers;
use App\Models\Announcement;
use App\Models\Course;
use App\Models\CourseTiming;
use App\Models\OfferedCourse;
use App\Models\Program;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;
    protected static ?string $module = 'announcement';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('target')
                    ->options([
                        'all' => 'All Students',
                        'program' => 'Specific Program',
                        'course' => 'Specific Course',
                    ])
                    ->label('Target Audience')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('target_id', null)),

                Forms\Components\Select::make('target_id')
                    ->label('Target')
                    ->options(function ($get) {
                        if ($get('target') === 'program') {
                            return Program::pluck('title', 'id');
                        } elseif ($get('target') === 'course') {
                            return CourseTiming::current();
                        }
                        return [];
                    })
                    ->searchable()
                    ->placeholder('Select a program or course')
                    ->hidden(fn ($get) => $get('target') === 'all'),

                Forms\Components\TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('message')
                    ->label('Message')
                    ->required(),

                Forms\Components\DatePicker::make('start_date')
                    ->label('Start Date')
                    ->nullable(),

                Forms\Components\DatePicker::make('end_date')
                    ->label('End Date')
                    ->nullable(),

                Forms\Components\Select::make('type')
                    ->label('Type')
                    ->options([
                        'info' => 'Info',
                        'warning' => 'Warning',
                        'error' => 'Error',
                        'success' => 'Success',
                    ])
                    ->default('info')
                    ->required(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->limit(20)
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'primary' => 'info',
                        'warning' => 'warning',
                        'danger' => 'error',
                        'success' => 'success',
                    ]),

                Tables\Columns\TextColumn::make('target')
                    ->label('Target Audience')
                    ->formatStateUsing(function ($record) {
                        return $record->target === 'all'
                            ? 'All Students'
                            : ($record->target === 'program'
                                ? Program::find($record->target_id)?->title
                                : CourseTiming::getTitle($record->target_id));
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('is_active')
                    ->label('Active')
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label('Active Announcements')
                    ->query(fn ($query) => $query->where('is_active', true)),

                Tables\Filters\Filter::make('expired')
                    ->label('Expired Announcements')
                    ->query(fn ($query) => $query->where('end_date', '<', now())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('start_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'view' => Pages\ViewAnnouncement::route('/{record}'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
