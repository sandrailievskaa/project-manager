<?php

namespace App\Policies;

use App\Enums\UserExperience;
use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function create(User $user, ?Comment $comment = null): bool
    {
        if ($comment === null || ! $comment->relationLoaded('task')) {
            return false;
        }

        $task = $comment->task;

        if (! $task->relationLoaded('project')) {
            $task->load('project');
        }

        if (! $task->relationLoaded('user')) {
            $task->load('user');
        }

        $isTeamLead = $task->project->user_id === $user->id;
        if ($isTeamLead) {
            return true;
        }

        if ($user->experience === UserExperience::SENIOR) {
            $isAssigned = $user->projects->contains($task->project->id);

            return $isAssigned;
        }

        if ($user->experience === UserExperience::MIDDLE) {
            $isAssignedToMe = $task->user_id === $user->id;
            $isAssignedToJunior = $task->user && $task->user->experience === UserExperience::JUNIOR;
            $hasCommented = $task->comments()->where('user_id', $user->id)->exists();

            return $isAssignedToMe || $isAssignedToJunior || $hasCommented;
        }

        if ($user->experience === UserExperience::JUNIOR) {
            return $task->user_id === $user->id;
        }

        return false;
    }
}
