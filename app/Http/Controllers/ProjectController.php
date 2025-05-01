<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Search, filter, and sort functionality
        $query = Project::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }
        if ($request->filled('sort_by')) {
            $query->orderBy($request->sort_by, $request->sort_order ?? 'asc');
        }
        $projects = $query->paginate(10);
        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $request->validate([
           'name'        => 'required',
           'description' => 'required',
           'due_date'    => 'nullable|date',
           'metadata'    => 'nullable|json',
           'document'    => 'nullable|mimes:pdf|max:512'  // max 512 KB; size range checked below
        ]);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            if ($file->getSize() < 100 * 1024 || $file->getSize() > 500 * 1024) {
                return response()->json(['error' => 'File size must be between 100KB and 500KB'], 422);
            }
            $path = $file->store('documents', 'public');
        } else {
            $path = null;
        }

        $project = Project::create([
            'name'        => $request->name,
            'description' => $request->description,
            'due_date'    => $request->due_date,
            'metadata'    => json_decode($request->metadata, true),
            'document'    => $path,
            'status'      => $request->has('status') ? (bool)$request->status : true,
        ]);

        return response()->json($project, 201);
    }

    public function show(Project $project)
    {
        // Also return audit trail
        return response()->json([
            'project'    => $project,
            'audit_logs' => $project->audits
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
           'name'        => 'sometimes|required',
           'description' => 'sometimes|required',
           'due_date'    => 'nullable|date',
           'metadata'    => 'nullable|json',
           'document'    => 'nullable|mimes:pdf|max:512'
        ]);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            if ($file->getSize() < 100 * 1024 || $file->getSize() > 500 * 1024) {
                return response()->json(['error' => 'File size must be between 100KB and 500KB'], 422);
            }
            $path = $file->store('documents', 'public');
            $project->document = $path;
        }

        $project->update($request->only(['name', 'description', 'due_date', 'status']));
        if ($request->filled('metadata')) {
            $project->metadata = json_decode($request->metadata, true);
            $project->save();
        }

        return response()->json($project);
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(null, 204);
    }
}
