<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->icon('heroicon-o-briefcase')
                    ->iconColor('primary'),
                Tables\Columns\TextColumn::make('deadline')
                    ->dateTime()
                    ->sortable()
                    ->icon('heroicon-o-calendar-days')
                    ->iconColor('danger'),
                Tables\Columns\TextColumn::make('teamLead.name')
                    ->icon('heroicon-o-user-circle')
                    ->iconColor('info'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
