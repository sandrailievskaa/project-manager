<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        $isAssigned = $user->projects->contains($project->id);
        $isTeamLead = $project->user_id === $user->id;

        return $isAssigned || $isTeamLead;
    }

    public function attachUser(User $user, Project $project): bool
    {
        return $project->user_id === $user->id;
    }

    public function detachUser(User $user, Project $project): bool
    {
        return $project->user_id === $user->id;
    }
}
