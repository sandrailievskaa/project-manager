<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        $user = auth()->user();

        // Get tasks per project (only assigned projects)
        $projectsWithTaskCounts = $user->projects()
            ->withCount(['tasks' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->get()
            ->map(function ($project) {
                return [
                    'title' => $project->title,
                    'task_count' => $project->tasks_count,
                ];
            })
            ->filter(fn ($project) => $project['task_count'] > 0)
            ->sortByDesc('task_count')
            ->take(10)
            ->values();

        // Get recent activity (last 10 task updates)
        $recentTasks = $user->tasks()
            ->with(['project:id,title'])
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'status' => $task->status->value,
                    'project_title' => $task->project->title ?? 'Unknown Project',
                    'updated_at' => $task->updated_at->diffForHumans(),
                ];
            });

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_projects' => $user->projects()->count(),
                'team_lead_projects' => $user->teamLeadProjects()->count(),
                'total_tasks' => $user->tasks()->count(),
                'tasks_by_status' => [
                    'to_do' => $user->tasks()->where('status', 'to_do')->count(),
                    'in_progress' => $user->tasks()->where('status', 'in_progress')->count(),
                    'qa' => $user->tasks()->where('status', 'qa')->count(),
                    'done' => $user->tasks()->where('status', 'done')->count(),
                ],
                'tasks_per_project' => $projectsWithTaskCounts,
                'recent_activity' => $recentTasks,
            ],
        ]);
    })->name('dashboard');

    Route::resource('projects', App\Http\Controllers\ProjectController::class)->only(['show']);
    Route::post('projects/{project}/users', [App\Http\Controllers\ProjectController::class, 'attachUser'])->name('projects.users.attach');
    Route::delete('projects/{project}/users/{user}', [App\Http\Controllers\ProjectController::class, 'detachUser'])
        ->name('projects.users.detach');
    Route::resource('tasks', App\Http\Controllers\TaskController::class)->only(['store', 'update', 'destroy']);
    Route::resource('comments', App\Http\Controllers\CommentController::class)->only(['store', 'update', 'destroy']);
});

require __DIR__.'/settings.php';
