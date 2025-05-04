<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controller;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan daftar Task dengan pagination, serta menerapkan filter pencarian dan sorting.
     */
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

        return Inertia::render('TaskCrud', [
            'tasks'   => $tasks,
            // Jika diperlukan, kirim juga filter yang aktif untuk dipakai di komponen Vue.
            'filters' => $request->only('search', 'sort_by', 'sort_order'),
        ]);
    }

    /**
     * Menyimpan Task baru.
     */
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
            'details'    => $request->filled('details') ? json_decode($request->details, true) : null,
            'due_date'   => $request->due_date,
            'completed'  => $request->has('completed') ? (bool)$request->completed : false,
        ]);

        return redirect()->back()->with('success', 'Task created successfully.');
    }

    /**
     * Menampilkan detail Task beserta komentar dan audit logs.
     */
    public function show(Task $task)
    {
        $task->load('comments'); // Pastikan relasi 'comments' didefinisikan di model Task
        return Inertia::render('TaskDetail', [
            'task'       => $task,
            'audit_logs' => $task->audits, // Pastikan Anda menggunakan package atau trait auditor
        ]);
    }

    /**
     * Mengupdate data Task.
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title'    => 'sometimes|required',
            'details'  => 'nullable|json',
            'due_date' => 'nullable|date',
        ]);

        $data = $request->only(['title', 'due_date']);
        
        if ($request->filled('details')) {
            $data['details'] = json_decode($request->details, true);
        }
        
        if ($request->has('completed')) {
            $data['completed'] = (bool)$request->completed;
        }

        $task->update($data);

        return redirect()->back()->with('success', 'Task updated successfully.');
    }

    /**
     * Menghapus Task.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->back()->with('success', 'Task deleted successfully.');
    }
}
