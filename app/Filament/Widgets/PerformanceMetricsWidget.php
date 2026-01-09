<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PerformanceMetricsWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 1;

    protected function getStats(): array
    {
        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'done')->count();
        $inProgressTasks = Task::whereIn('status', ['in_progress', 'qa'])->count();

        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        $productivityScore = $totalTasks > 0 ? round((($completedTasks + $inProgressTasks) / $totalTasks) * 100) : 0;

        return [
            Stat::make('Productivity Score', $productivityScore.'%')
                ->description('Overall productivity indicator')
                ->descriptionIcon('heroicon-o-bolt')
                ->color('primary')
                ->chart([60, 62, 61, 63, $productivityScore])
                ->icon('heroicon-o-bolt'),

            Stat::make('Completion Rate', $completionRate.'%')
                ->description('Tasks completed successfully')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart([15, 18, 20, 19, $completionRate])
                ->icon('heroicon-o-check-circle'),
        ];
    }
}

