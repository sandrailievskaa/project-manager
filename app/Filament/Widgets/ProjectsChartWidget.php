<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;

class ProjectsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Project Status';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
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

        return [
            'datasets' => [
                [
                    'label' => 'Projects',
                    'data' => [
                        $activeProjects,
                        $completedProjects,
                        max(0, $totalProjects - $activeProjects - $completedProjects),
                    ],
                    'backgroundColor' => [
                        'rgba(43, 108, 176, 0.8)',
                        'rgba(128, 90, 213, 0.8)',
                        'rgba(148, 163, 184, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(43, 108, 176)',
                        'rgb(128, 90, 213)',
                        'rgb(148, 163, 184)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Active Projects', 'Completed Projects', 'Other Projects'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 12,
                        'font' => [
                            'size' => 12,
                        ],
                    ],
                ],
            ],
        ];
    }
}
