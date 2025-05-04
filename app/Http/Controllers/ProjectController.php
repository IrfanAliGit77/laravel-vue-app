<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controller;

class ProjectController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Menampilkan daftar project dengan fitur pencarian, penyaringan, dan sorting.
     */
    public function index(Request $request)
    {
        $query = Project::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('sort_by')) {
            $query->orderBy($request->sort_by, $request->sort_order ?? 'asc');
        }

        $projects = $query->paginate(10);

        return Inertia::render('ProjectCrud', [
            'projects' => $projects,
            // Kirim filter yang aktif agar dapat dipakai ulang di komponen Vue
            'filters'  => $request->only(['search', 'sort_by', 'sort_order']),
        ]);
    }

    /**
     * Menyimpan project baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required',
            'description' => 'required',
            'due_date'    => 'nullable|date',
            'metadata'    => 'nullable|json',
            'document'    => 'nullable|mimes:pdf|max:512'  // Maksimal 512 KB
        ]);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            // Validasi ukuran file (dari 100KB sampai 500KB)
            if ($file->getSize() < 100 * 1024 || $file->getSize() > 500 * 1024) {
                return redirect()->back()->withErrors(['document' => 'File size must be between 100KB and 500KB']);
            }
            $path = $file->store('documents', 'public');
        } else {
            $path = null;
        }

        Project::create([
            'name'        => $request->name,
            'description' => $request->description,
            'due_date'    => $request->due_date,
            'metadata'    => $request->filled('metadata') ? json_decode($request->metadata, true) : null,
            'document'    => $path,
            'status'      => $request->has('status') ? (bool)$request->status : true,
        ]);

        return redirect()->back()->with('success', 'Project created successfully.');
    }

    /**
     * Menampilkan detail project beserta audit logs.
     */
    public function show(Project $project)
    {
        // Pastikan relasi audit logs sudah didefinisikan di model Project
        $project->load('audits');

        return Inertia::render('ProjectDetail', [
            'project'    => $project,
            'audit_logs' => $project->audits,
        ]);
    }

    /**
     * Mengupdate data project.
     */
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
                return redirect()->back()->withErrors(['document' => 'File size must be between 100KB and 500KB']);
            }
            $path = $file->store('documents', 'public');
            $project->document = $path;
        }

        $project->update($request->only(['name', 'description', 'due_date', 'status']));

        if ($request->filled('metadata')) {
            $project->metadata = json_decode($request->metadata, true);
            $project->save();
        }

        return redirect()->back()->with('success', 'Project updated successfully.');
    }

    /**
     * Menghapus project.
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->back()->with('success', 'Project deleted successfully.');
    }
}
