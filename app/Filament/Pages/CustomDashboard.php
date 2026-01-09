<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\PerformanceMetricsWidget;
use App\Filament\Widgets\ProjectsChartWidget;
use App\Filament\Widgets\ProjectsOverviewWidget;
use App\Filament\Widgets\UserRoleChartWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class CustomDashboard extends BaseDashboard
{
    protected static string $routePath = '/';

    protected static ?string $navigationLabel = 'Dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            ProjectsOverviewWidget::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            ProjectsChartWidget::class,
            UserRoleChartWidget::class,
            PerformanceMetricsWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'md' => 2,
            'lg' => 2,
            'xl' => 2,
            '2xl' => 2,
        ];
    }
}
