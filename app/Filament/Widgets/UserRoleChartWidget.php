<?php

namespace App\Filament\Widgets;

use App\Enums\UserExperience;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class UserRoleChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Team Distribution';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $seniorCount = User::where('experience', UserExperience::SENIOR->value)->count();
        $middleCount = User::where('experience', UserExperience::MIDDLE->value)->count();
        $juniorCount = User::where('experience', UserExperience::JUNIOR->value)->count();

        return [
            'datasets' => [
                [
                    'label' => 'Users by Experience',
                    'data' => [$seniorCount, $middleCount, $juniorCount],
                    'backgroundColor' => [
                        'rgba(43, 108, 176, 0.8)',
                        'rgba(76, 81, 191, 0.8)',
                        'rgba(128, 90, 213, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(43, 108, 176)',
                        'rgb(76, 81, 191)',
                        'rgb(128, 90, 213)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Senior', 'Middle', 'Junior'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
            'indexAxis' => 'y',
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'font' => [
                            'size' => 11,
                        ],
                    ],
                ],
                'y' => [
                    'ticks' => [
                        'font' => [
                            'size' => 11,
                        ],
                    ],
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
