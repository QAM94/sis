<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers\ProgramsRelationManager;
use App\Filament\Traits\HasResourcePermissions;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StudentResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $model = Student::class;
    protected static ?string $module = 'student';
    protected static ?string $navigationGroup = 'User Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('reg_no')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('contact')
                    ->tel()
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('postcode')
                    ->required()
                    ->maxLength(15),
                Forms\Components\TextInput::make('nationality')
                    ->required()
                    ->maxLength(15),
                Forms\Components\TextInput::make('sin')
                    ->required()
                    ->maxLength(15),
                Forms\Components\DatePicker::make('dob')
                    ->required()->label('Date of Birth'),
                Forms\Components\Select::make('gender')
                    ->options(['Male' => 'Male',
                        'Female' => 'Female'])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options(['Enrolled' => 'Enrolled',
                        'Completed' => 'Completed',
                        'Suspended' => 'Suspended',
                        'Withdrawn' => 'Withdrawn'])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reg_no')
                    ->label('Reg ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email Address'),
                Tables\Columns\TextColumn::make('gender')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('contact')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('nationality')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('sin')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('dob')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('Status')
                    ->multiple()
                    ->options([
                        'Enrolled' => 'Enrolled',
                        'Completed' => 'Completed',
                        'Suspended' => 'Suspended',
                        'Withdrawn' => 'Withdrawn'
                    ])
                    ->attribute('status'),
                Tables\Filters\SelectFilter::make('Gender')
                    ->options([
                        'Male' => 'Male',
                        'Female' => 'Female'
                    ])
                    ->attribute('gender')
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ProgramsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'view' => Pages\ViewStudent::route('/{record}'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
