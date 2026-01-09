<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;

class TasksPerProjectChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Tasks Per Project';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $projects = Project::withCount('tasks')
            ->orderBy('tasks_count', 'desc')
            ->take(10)
            ->get();

        $labels = $projects->map(fn ($project) => strlen($project->title) > 20
            ? substr($project->title, 0, 20).'...'
            : $project->title)->toArray();

        $data = $projects->pluck('tasks_count')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Number of Tasks',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(43, 108, 176, 0.8)',
                        'rgba(76, 81, 191, 0.8)',
                        'rgba(128, 90, 213, 0.8)',
                        'rgba(43, 108, 176, 0.8)',
                        'rgba(76, 81, 191, 0.8)',
                        'rgba(128, 90, 213, 0.8)',
                        'rgba(43, 108, 176, 0.8)',
                        'rgba(76, 81, 191, 0.8)',
                        'rgba(128, 90, 213, 0.8)',
                        'rgba(148, 163, 184, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(43, 108, 176)',
                        'rgb(76, 81, 191)',
                        'rgb(128, 90, 213)',
                        'rgb(43, 108, 176)',
                        'rgb(76, 81, 191)',
                        'rgb(128, 90, 213)',
                        'rgb(43, 108, 176)',
                        'rgb(76, 81, 191)',
                        'rgb(128, 90, 213)',
                        'rgb(148, 163, 184)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
