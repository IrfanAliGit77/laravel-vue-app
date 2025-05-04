<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
        $this->middleware(function ($request, $next) {
            // Pastikan user memiliki relasi role sebelum mengakses propertinya
            if (!Auth::user()->role || Auth::user()->role->name !== 'Administrator') {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }
  

    // Menampilkan halaman User Management dengan data user (paginate) dan list role (untuk pilihan)
    public function index(Request $request)
    {
        $users = User::with('role')->paginate(10);
        $roles = Role::all();

        return Inertia::render('UserManagement', [
            'users' => $users,
            'roles' => $roles,
            // Jika ingin mengirim flash message, Anda bisa mengambilnya dari session jika diperlukan.
        ]);
    }

    // Menyimpan user baru
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role_id'  => 'required|exists:roles,id'
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role_id'  => $request->role_id,
        ]);

        return redirect()->back()->with('success', 'User created successfully.');
    }

    // Menampilkan detail user (bisa dikembangkan jika Anda ingin halaman detail)
    public function show(User $user)
    {
        return Inertia::render('UserManagement/Show', [
            'user' => $user->load('role'),
        ]);
    }

    // Mengupdate data user
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'sometimes|required',
            'email'    => 'sometimes|required|email|unique:users,email,'.$user->id,
            'password' => 'sometimes|min:6',
            'role_id'  => 'required|exists:roles,id'
        ]);

        $data = $request->only(['name', 'email', 'role_id']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    // Menghapus user
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully.');
    }
}
