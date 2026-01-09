<?php

namespace App\Filament\Resources;

use App\Enums\UserExperience;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers\TasksRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\UsersRelationManager;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->prefixIcon('heroicon-o-briefcase')
                    ->prefixIconColor('primary'),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('requirements')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('estimated_time_of_completion')
                    ->required()
                    ->numeric()
                    ->prefixIcon('heroicon-o-clock')
                    ->prefixIconColor('success'),
                Forms\Components\DateTimePicker::make('deadline')
                    ->seconds(false)
                    ->required()
                    ->prefixIcon('heroicon-o-calendar-days')
                    ->prefixIconColor('danger'),
                Forms\Components\Select::make('user_id')
                    ->relationship(
                        name: 'teamLead',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->where('experience', UserExperience::SENIOR->value)
                    )
                    ->searchable()
                    ->preload()
                    ->prefixIcon('heroicon-o-user-circle')
                    ->prefixIconColor('info')
                    ->helperText('Only senior users can be assigned as team leads'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->size('lg')
                    ->icon('heroicon-o-briefcase')
                    ->iconColor('primary')
                    ->description(fn (Project $record): string => \Str::limit($record->description, 50))
                    ->wrap(),
                Tables\Columns\ImageColumn::make('teamLead.avatar')
                    ->label('Team Lead')
                    ->circular()
                    ->defaultImageUrl(function (Project $record): string {
                        return 'https://ui-avatars.com/api/?name='.urlencode($record->teamLead?->name ?? 'Unknown').'&background=3b82f6&color=fff';
                    })
                    ->size(40),
                Tables\Columns\TextColumn::make('teamLead.name')
                    ->label('')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->color('gray'),
                Tables\Columns\TextColumn::make('tasks_count')
                    ->label('Tasks')
                    ->counts('tasks')
                    ->badge()
                    ->color(fn (Project $record): string => match (true) {
                        $record->tasks()->count() > 10 => 'success',
                        $record->tasks()->count() > 5 => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (Project $record, $state): string => $state.' tasks'),
                Tables\Columns\TextColumn::make('progress')
                    ->label('Progress')
                    ->state(function (Project $record): string {
                        $totalTasks = $record->tasks()->count();
                        if ($totalTasks === 0) {
                            return '0%';
                        }
                        $completedTasks = $record->tasks()->where('status', 'done')->count();
                        $progress = round(($completedTasks / $totalTasks) * 100);

                        return $progress.'%';
                    })
                    ->badge()
                    ->color(function (Project $record): string {
                        $totalTasks = $record->tasks()->count();
                        if ($totalTasks === 0) {
                            return 'gray';
                        }
                        $completedTasks = $record->tasks()->where('status', 'done')->count();
                        $progress = round(($completedTasks / $totalTasks) * 100);

                        return match (true) {
                            $progress === 100 => 'success',
                            $progress >= 50 => 'warning',
                            default => 'danger',
                        };
                    })
                    ->icon(function (Project $record): string {
                        $totalTasks = $record->tasks()->count();
                        if ($totalTasks === 0) {
                            return 'heroicon-o-minus-circle';
                        }
                        $completedTasks = $record->tasks()->where('status', 'done')->count();
                        $progress = round(($completedTasks / $totalTasks) * 100);

                        return match (true) {
                            $progress === 100 => 'heroicon-o-check-circle',
                            $progress >= 50 => 'heroicon-o-arrow-path',
                            default => 'heroicon-o-clock',
                        };
                    }),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Team Size')
                    ->counts('users')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-users'),
                Tables\Columns\TextColumn::make('deadline')
                    ->label('Deadline')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar-days')
                    ->iconColor(function (Project $record): string {
                        return $record->deadline && $record->deadline->isPast() ? 'danger' : 'gray';
                    })
                    ->badge(function (Project $record): bool {
                        return $record->deadline && $record->deadline->isPast();
                    })
                    ->color(function (Project $record): string {
                        return $record->deadline && $record->deadline->isPast() ? 'danger' : 'gray';
                    }),
                Tables\Columns\TextColumn::make('estimated_time_of_completion')
                    ->label('Est. Time')
                    ->numeric()
                    ->suffix(' hrs')
                    ->sortable()
                    ->icon('heroicon-o-clock')
                    ->iconColor('success')
                    ->color('gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('gray'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue Projects')
                    ->query(fn ($query) => $query->where('deadline', '<', now())),
                Tables\Filters\Filter::make('active')
                    ->label('Active Projects')
                    ->query(fn ($query) => $query->whereHas('tasks', function ($q) {
                        $q->whereIn('status', ['to_do', 'in_progress', 'qa']);
                    })),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->color('primary'),
                Tables\Actions\EditAction::make()
                    ->color('warning'),
                Tables\Actions\DeleteAction::make()
                    ->color('danger'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->poll('30s');
    }

    public static function getRelations(): array
    {
        return [
            TasksRelationManager::class,
            UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
