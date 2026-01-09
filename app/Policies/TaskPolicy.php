<?php

namespace App\Policies;

use App\Enums\UserExperience;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function create(User $user, ?Task $task = null): bool
    {
        if ($task === null || ! $task->relationLoaded('project')) {
            return false;
        }

        return $task->project->user_id === $user->id;
    }

    public function updateStatus(User $user, Task $task): bool
    {
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

            return $isAssignedToMe || $isAssignedToJunior;
        }

        if ($user->experience === UserExperience::JUNIOR) {
            return $task->user_id === $user->id;
        }

        return false;
    }

    public function updateUser(User $user, Task $task, ?int $newUserId = null): bool
    {
        if (! $task->relationLoaded('project')) {
            $task->load('project');
        }

        $isTeamLead = $task->project->user_id === $user->id;
        if ($isTeamLead) {
            return true;
        }

        if ($user->experience === UserExperience::MIDDLE) {
            if ($newUserId === null) {
                return false;
            }

            $newUser = User::find($newUserId);
            if (! $newUser) {
                return false;
            }

            $canAssignToSelf = $newUserId === $user->id;
            $canAssignToJunior = $newUser->experience === UserExperience::JUNIOR;

            return $canAssignToSelf || $canAssignToJunior;
        }

        return false;
    }

    public function delete(User $user, Task $task): bool
    {
        if (! $task->relationLoaded('project')) {
            $task->load('project');
        }

        return $task->project->user_id === $user->id;
    }
}
