<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttachUserToProjectRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function show(Request $request, Project $project): Response
    {
        $this->authorize('view', $project);

        $user = $request->user();

        $project->load(['teamLead', 'tasks.user', 'tasks.comments.user', 'users']);

        $assignedUsers = $project->users()
            ->select('users.id', 'users.name', 'users.experience')
            ->orderBy('users.name')
            ->get();

        $allAssignedUsers = $assignedUsers;
        if ($project->teamLead && ! $assignedUsers->contains('id', $project->teamLead->id)) {
            $teamLeadData = (object) [
                'id' => $project->teamLead->id,
                'name' => $project->teamLead->name,
                'experience' => $project->teamLead->experience->value,
            ];
            $allAssignedUsers = $assignedUsers->push($teamLeadData)->values();
        }

        $isTeamLead = $project->user_id === $user->id;

        $isAssigned = $user->projects->contains($project->id);

        $allUsers = User::select('id', 'name', 'experience')
            ->orderBy('name')
            ->get();

        $assignedUserIds = $assignedUsers->pluck('id')->toArray();

        $availableUsers = $allUsers->reject(function ($user) use ($assignedUserIds) {
            return in_array($user->id, $assignedUserIds);
        })->values();

        $userExperience = $user->experience->value;

        return Inertia::render('projects/Show', [
            'project' => $project,
            'users' => $allAssignedUsers,
            'assignedUsers' => $assignedUsers,
            'isTeamLead' => $isTeamLead,
            'userExperience' => $userExperience,
            'isAssignedToProject' => $isAssigned,
            'availableUsers' => $availableUsers,
        ]);
    }

    public function attachUser(AttachUserToProjectRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('attachUser', $project);

        $project->users()->attach($request->user_id);

        return redirect()->back()->with('success', 'User added to project successfully.');
    }

    public function detachUser(Request $request, Project $project, int $user): RedirectResponse
    {
        $this->authorize('detachUser', $project);

        $userModel = User::findOrFail($user);

        if (! $project->users()->where('users.id', $userModel->id)->exists()) {
            return redirect()->back()->with('error', 'User is not assigned to this project.');
        }

        $project->users()->detach($userModel->id);

        return redirect()->back()->with('success', 'User removed from project successfully.');
    }
}
