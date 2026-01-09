<x-filament-panels::page>
    <style>
        .fi-widgets {
            gap: 1rem !important;
        }
        .fi-stats-overview-widget {
            gap: 0.75rem !important;
        }
        .fi-widget {
            margin-bottom: 0 !important;
        }
        .fi-widget-card {
            margin-bottom: 0 !important;
        }
        .left-chart-widget {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .left-chart-widget .fi-widget-card {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .left-chart-widget .fi-widget-chart {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 0;
        }
        .left-chart-widget canvas {
            max-height: 100% !important;
            width: auto !important;
        }
        .right-column {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            height: 100%;
        }
        .right-chart-widget {
            flex-shrink: 0;
        }
        .right-chart-widget .fi-widget-card {
            min-height: 240px !important;
            display: flex;
            flex-direction: column;
        }
        .right-chart-widget .fi-widget-chart {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 0;
        }
        .right-chart-widget canvas {
            max-height: 200px !important;
            height: 200px !important;
        }
        .performance-metrics-widget {
            flex-shrink: 0;
        }
        .performance-metrics-widget .fi-stats-overview-widget {
            gap: 0.75rem !important;
        }
        .performance-metrics-widget .fi-stat {
            padding: 0.875rem !important;
        }
        .performance-metrics-widget .fi-stat-value {
            font-size: 1.75rem !important;
            font-weight: 700 !important;
        }
        .performance-metrics-widget .fi-stat-description {
            font-size: 0.75rem !important;
            margin-top: 0.25rem !important;
        }
        .performance-metrics-widget .fi-stat-icon {
            width: 2.25rem !important;
            height: 2.25rem !important;
        }
        .performance-metrics-widget .fi-stat-icon svg {
            width: 1.375rem !important;
            height: 1.375rem !important;
        }
    </style>

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 md:p-6">
        <div class="group relative overflow-hidden rounded-2xl border-0 p-4 shadow-lg md:p-5 bg-gradient-to-r from-primary-500 via-primary-600 to-primary-700 dark:from-primary-700 dark:via-primary-800 dark:to-primary-900">
            <div class="absolute inset-0 bg-gradient-to-br from-primary/20 via-primary-600/20 to-primary-700/20 opacity-40"></div>
            
            <div class="relative z-10 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div class="flex flex-col gap-1">
                    <h1 class="text-2xl font-bold tracking-tight text-white drop-shadow-md md:text-3xl">
                        Welcome, Admin!
                    </h1>
                    <p class="text-sm text-white/90">
                        Here's your project management overview
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-2 rounded-lg border-0 bg-white/20 backdrop-blur-md px-3 py-1.5 text-sm font-medium text-white shadow-md dark:bg-white/10">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ now()->format('F j, Y') }}
                    </span>
                </div>
            </div>
        </div>

        <x-filament-panels::widgets
            :widgets="$this->getHeaderWidgets()"
        />

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:items-stretch">
            <div class="left-chart-widget">
                <x-filament-panels::widgets
                    :widgets="[$this->getWidgets()[0]]"
                    :columns="['default' => 1, 'md' => 1]"
                />
            </div>
            <div class="right-column">
                <div class="right-chart-widget">
                    <x-filament-panels::widgets
                        :widgets="[$this->getWidgets()[1]]"
                        :columns="['default' => 1, 'md' => 1]"
                    />
                </div>
                <div class="performance-metrics-widget">
                    <x-filament-panels::widgets
                        :widgets="[$this->getWidgets()[2]]"
                        :columns="['default' => 1, 'md' => 1]"
                    />
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
