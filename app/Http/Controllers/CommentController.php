<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request): RedirectResponse
    {
        $task = \App\Models\Task::findOrFail($request->task_id);
        $task->load('project');

        $comment = new Comment;
        $comment->setRelation('task', $task);

        $this->authorize('create', $comment);

        Comment::create([
            'text' => $request->text,
            'task_id' => $request->task_id,
            'user_id' => $request->user()->id,
        ]);

        return redirect()->back()->with('success', 'Comment added successfully.');
    }

    public function update(UpdateCommentRequest $request, Comment $comment): RedirectResponse
    {
        $comment->update([
            'text' => $request->text,
        ]);

        return redirect()->back()->with('success', 'Comment updated successfully.');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully.');
    }
}
