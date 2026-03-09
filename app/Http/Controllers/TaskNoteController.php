<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskNote;
use Illuminate\Http\Request;

class TaskNoteController extends Controller
{
  public function show(Task $task)
  {
    $note = TaskNote::where('task_id', $task->id)
      ->where('user_id', auth()->id())
      ->first();

    return view('tasks.note', compact('task', 'note'));
  }

  public function save(Request $request, Task $task)
  {
    $request->validate(['content' => 'nullable|string|max:5000']);

    TaskNote::updateOrCreate(
      ['task_id' => $task->id, 'user_id' => auth()->id()],
      ['content' => $request->content]
    );

    return response()->json(['success' => true]);
  }
}
