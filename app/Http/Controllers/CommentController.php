<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Note;
use App\Models\Task;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function indexNote(Note $note)
    {
        $this->authorize('view', [Comment::class, $note]);

        return response()->json([
            'comments' => $note->comments
        ]);
    }

    public function indexTask(Note $note, Task $task)
    {
        if ($task->note_id !== $note->id) {
            return response()->json([
                'message' => 'Task nepatrí k tejto note.'
            ], 404);
        }

        $this->authorize('view', [Comment::class, $note]);

        return response()->json([
            'comments' => $task->comments
        ]);
    }

    public function show(Comment $comment)
    {
        $this->authorize('view', [Comment::class, $comment->commentable]);

        return response()->json([
            'comment' => $comment
        ]);
    }

    public function storeNote(Request $request, Note $note)
    {
        $this->authorize('create', [Comment::class, $note]);

        $validated = $request->validate([
            'body' => ['required', 'string']
        ]);

        $comment = $note->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body']
        ]);

        return response()->json([
            'comment' => $comment
        ]);
    }

    public function storeTask(Request $request, Note $note, Task $task)
    {
        if ($task->note_id !== $note->id) {
            return response()->json([
                'message' => 'Task nepatrí k tejto note.'
            ], 404);
        }
        $this->authorize('create', [Comment::class, $task->note]);

        $validated = $request->validate([
            'body' => ['required', 'string']
        ]);

        $comment = $task->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body']
        ]);

        return response()->json([
            'comment' => $comment
        ], 201);
    }

    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $comment->update([
            'body' => $request->body
        ]);

        return response()->json([
            'comment' => $comment
        ]);
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json([
            'message' => 'Deleted'
        ]);
    }

}
