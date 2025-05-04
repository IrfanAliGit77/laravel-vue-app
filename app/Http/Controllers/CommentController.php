<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controller;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan daftar komentar dengan filter pencarian dan sorting.
     */
    public function index(Request $request)
    {
        $query = Comment::with('task');
        
        if ($request->filled('search')) {
            $query->where('comment', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('sort_by')) {
            $query->orderBy($request->sort_by, $request->sort_order ?? 'asc');
        }
        
        $comments = $query->paginate(10);
        
        return Inertia::render('CommentCrud', [
            'comments' => $comments,
            // Kirim filter agar bisa digunakan kembali di front-end
            'filters'  => $request->only(['search', 'sort_by', 'sort_order']),
        ]);
    }

    /**
     * Menyimpan comment baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'task_id'     => 'required|exists:tasks,id',
            'comment'     => 'required',
            'extra'       => 'nullable|json',
            'commented_at'=> 'nullable|date'
        ]);

        Comment::create([
            'task_id'     => $request->task_id,
            'comment'     => $request->comment,
            'extra'       => $request->filled('extra') ? json_decode($request->extra, true) : null,
            'commented_at'=> $request->commented_at,
            'is_approved' => $request->has('is_approved') ? (bool)$request->is_approved : false,
        ]);

        return redirect()->back()->with('success', 'Comment created successfully.');
    }

    /**
     * Menampilkan detail comment beserta audit logs.
     */
    public function show(Comment $comment)
    {
        // Pastikan relasi 'audits' sudah didefinisikan pada model Comment
        $comment->load('audits');

        return Inertia::render('CommentDetail', [
            'comment'    => $comment,
            'audit_logs' => $comment->audits,
        ]);
    }

    /**
     * Mengupdate data comment.
     */
    public function update(Request $request, Comment $comment)
    {
        $request->validate([
            'comment'     => 'sometimes|required',
            'extra'       => 'nullable|json',
            'commented_at'=> 'nullable|date'
        ]);

        $comment->update($request->only(['comment', 'commented_at']));

        if ($request->filled('extra')) {
            $comment->extra = json_decode($request->extra, true);
            $comment->save();
        }
        if ($request->has('is_approved')) {
            $comment->is_approved = (bool)$request->is_approved;
            $comment->save();
        }

        return redirect()->back()->with('success', 'Comment updated successfully.');
    }

    /**
     * Menghapus comment.
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();
        return redirect()->back()->with('success', 'Comment deleted successfully.');
    }
}
