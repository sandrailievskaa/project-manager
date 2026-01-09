<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;

class TaskController extends Controller
{
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $project = Project::findOrFail($request->project_id);
        $task = new Task([
            'title' => $request->title,
            'description' => $request->description,
            'project_id' => $request->project_id,
            'user_id' => $request->user_id,
            'status' => $request->status,
        ]);
        $task->setRelation('project', $project);

        $this->authorize('create', $task);

        $task->save();

        return redirect()->back()->with('success', 'Task created successfully.');
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->load('project');

        $data = $request->only([
            'title',
            'description',
            'user_id',
            'status',
        ]);

        if ($request->has('status')) {
            $this->authorize('updateStatus', $task);
        }

        if ($request->has('user_id')) {
            $newUserId = $request->user_id ? (int) $request->user_id : null;
            $this->authorize('updateUser', [$task, $newUserId]);
        }

        $task->update($data);

        return redirect()->back()->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->load('project');

        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->back()->with('success', 'Task deleted successfully.');
    }
}
