<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProjectsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalProjects = Project::count();
        $activeProjects = Project::whereHas('tasks', function ($query) {
            $query->whereIn('status', ['to_do', 'in_progress', 'qa']);
        })->count();
        $completedProjects = Project::whereDoesntHave('tasks', function ($query) {
            $query->whereIn('status', ['to_do', 'in_progress', 'qa']);
        })->whereHas('tasks', function ($query) {
            $query->where('status', 'done');
        })->count();
        $totalUsers = User::count();
        $totalTasks = Task::count();

        return [
            Stat::make('Total Users', $totalUsers)
                ->description('Registered users')
                ->descriptionIcon('heroicon-o-users')
                ->color('info')
                ->chart([10, 15, 18, 20, $totalUsers])
                ->icon('heroicon-o-users'),

            Stat::make('Total Projects', $totalProjects)
                ->description('All projects')
                ->descriptionIcon('heroicon-o-briefcase')
                ->color('primary')
                ->chart([7, 12, 10, 15, $totalProjects])
                ->icon('heroicon-o-briefcase'),

            Stat::make('Active Projects', $activeProjects)
                ->description('In progress')
                ->descriptionIcon('heroicon-o-arrow-path')
                ->color('warning')
                ->chart([5, 8, 9, 12, $activeProjects])
                ->icon('heroicon-o-arrow-path'),

            Stat::make('Completed Projects', $completedProjects)
                ->description('Finished')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart([2, 4, 5, 6, $completedProjects])
                ->icon('heroicon-o-check-circle'),

            Stat::make('Total Tasks', $totalTasks)
                ->description('Across all projects')
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('gray')
                ->chart([5, 10, 15, 20, $totalTasks])
                ->icon('heroicon-o-clipboard-document-list'),
        ];
    }
}
