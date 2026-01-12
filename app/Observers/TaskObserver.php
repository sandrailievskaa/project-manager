<?php

namespace App\Observers;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskObserver
{
    public function updated(Task $task): void
    {
        if (! $task->wasChanged('status')) {
            return;
        }

        $oldStatus = $task->getOriginal('status');
        $newStatus = $task->status;

        $user = Auth::user();

        $task->comments()->create([
            'user_id' => $user?->id,
            'text' => sprintf(
                'Status changed from "%s" to "%s"%s.',
                $oldStatus->getLabel(),
                $newStatus->getLabel(),
                $user ? ' by '.$user->name : ''
            ),
        ]);
    }

    public function deleting(Task $task): void
    {
        $task->comments()->delete();
    }
}
