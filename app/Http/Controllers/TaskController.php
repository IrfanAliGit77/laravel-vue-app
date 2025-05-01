<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Task::with('project');
        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }
        if ($request->filled('sort_by')) {
            $query->orderBy($request->sort_by, $request->sort_order ?? 'asc');
        }
        $tasks = $query->paginate(10);
        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $request->validate([
           'project_id' => 'required|exists:projects,id',
           'title'      => 'required',
           'details'    => 'nullable|json',
           'due_date'   => 'nullable|date',
        ]);

        $task = Task::create([
           'project_id' => $request->project_id,
           'title'      => $request->title,
           'details'    => json_decode($request->details, true),
           'due_date'   => $request->due_date,
           'completed'  => $request->has('completed') ? (bool)$request->completed : false,
        ]);

        return response()->json($task, 201);
    }

    public function show(Task $task)
    {
        return response()->json([
           'task'       => $task->load('comments'),
           'audit_logs' => $task->audits
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
           'title'   => 'sometimes|required',
           'details' => 'nullable|json',
           'due_date'=> 'nullable|date',
        ]);

        $task->update($request->only(['title', 'due_date']));
        if ($request->filled('details')) {
            $task->details = json_decode($request->details, true);
            $task->save();
        }
        if ($request->has('completed')) {
            $task->completed = (bool)$request->completed;
            $task->save();
        }

        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(null, 204);
    }
}
