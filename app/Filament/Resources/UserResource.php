<?php

namespace App\Filament\Resources;

use App\Enums\UserExperience;
use App\Enums\UserRole;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\ProjectsRelationManager;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->prefixIcon('heroicon-o-user')
                    ->prefixIconColor('primary'),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->prefixIcon('heroicon-o-envelope')
                    ->prefixIconColor('info'),
                Forms\Components\TextInput::make('password')
                    ->hiddenOn('edit')
                    ->password()
                    ->required()
                    ->prefixIcon('heroicon-o-lock-closed')
                    ->prefixIconColor('warning'),
                Forms\Components\Select::make('experience')
                    ->options(UserExperience::class)
                    ->required()
                    ->prefixIcon('heroicon-o-academic-cap')
                    ->prefixIconColor('success'),
                Forms\Components\Select::make('role')
                    ->options(UserRole::class)
                    ->required()
                    ->prefixIcon('heroicon-o-shield-check')
                    ->prefixIconColor('danger'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->icon('heroicon-o-user')
                    ->iconColor('primary'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->iconColor('info'),
                Tables\Columns\TextColumn::make('experience')
                    ->badge()
                    ->searchable()
                    ->icon(fn ($record) => $record->experience?->getIcon())
                    ->color(fn ($record) => $record->experience?->getColor()),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->searchable()
                    ->icon(fn ($record) => $record->role?->getIcon())
                    ->color(fn ($record) => $record->role?->getColor()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-calendar')
                    ->iconColor('gray'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-clock')
                    ->iconColor('gray'),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
