<?php

namespace App\Observers;

use App\Models\Task;

class TaskObserver
{
    public function created(Task $task): void {}

    public function updated(Task $task): void {}

    public function deleting(Task $task): void
    {
        $task->comments()->delete();
    }

    public function deleted(Task $task): void {}

    public function restored(Task $task): void {}

    public function forceDeleted(Task $task): void {}
}
