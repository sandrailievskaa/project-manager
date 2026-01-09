<?php

namespace App\Filament\Resources;

use App\Enums\TaskStatus;
use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers\CommentsRelationManager;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->prefixIcon('heroicon-o-document-text')
                    ->prefixIconColor('primary'),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('project_id')
                    ->relationship('project', 'title')
                    ->required()
                    ->prefixIcon('heroicon-o-briefcase')
                    ->prefixIconColor('success'),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->prefixIcon('heroicon-o-user')
                    ->prefixIconColor('warning'),
                Forms\Components\Select::make('status')
                    ->options(TaskStatus::class)
                    ->required()
                    ->prefixIcon('heroicon-o-flag')
                    ->prefixIconColor('danger'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->icon('heroicon-o-document-text')
                    ->iconColor('primary'),
                Tables\Columns\TextColumn::make('project.title')
                    ->numeric()
                    ->sortable()
                    ->icon('heroicon-o-briefcase')
                    ->iconColor('success'),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->iconColor('warning'),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->icon(fn ($record) => $record->status?->getIcon())
                    ->color(fn ($record) => $record->status?->getColor()),
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
            CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'view' => Pages\ViewTask::route('/{record}'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
