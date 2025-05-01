<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
   public function __construct(){
      $this->middleware('auth');
   }

   public function index(Request $request)
   {
      $query = Comment::with('task');
      if ($request->filled('search')) {
         $query->where('comment', 'like', '%'.$request->search.'%');
      }
      if ($request->filled('sort_by')) {
         $query->orderBy($request->sort_by, $request->sort_order ?? 'asc');
      }
      $comments = $query->paginate(10);
      return response()->json($comments);
   }

   public function store(Request $request)
   {
      $request->validate([
         'task_id'    => 'required|exists:tasks,id',
         'comment'    => 'required',
         'extra'      => 'nullable|json',
         'commented_at' => 'nullable|date'
      ]);

      $comment = Comment::create([
         'task_id'     => $request->task_id,
         'comment'     => $request->comment,
         'extra'       => json_decode($request->extra, true),
         'commented_at'=> $request->commented_at,
         'is_approved' => $request->has('is_approved') ? (bool)$request->is_approved : false,
      ]);

      return response()->json($comment, 201);
   }

   public function show(Comment $comment)
   {
      return response()->json([
         'comment'    => $comment,
         'audit_logs' => $comment->audits
      ]);
   }

   public function update(Request $request, Comment $comment)
   {
      $request->validate([
         'comment'    => 'sometimes|required',
         'extra'      => 'nullable|json',
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
      return response()->json($comment);
   }

   public function destroy(Comment $comment)
   {
      $comment->delete();
      return response()->json(null, 204);
   }
}
