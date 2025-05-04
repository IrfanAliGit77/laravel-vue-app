<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controller;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Tampilkan halaman Role Management dan kirimkan data roles
    public function index(Request $request)
{
    $roles = Role::when($request->input('search'), function ($query, $search) {
        $query->where('name', 'like', "%{$search}%");
    })->paginate(10);

    return Inertia::render('RoleManagement', [
        'roles' => $roles,
        'filters' => $request->only('search') ?? [], 
        'flash' => [
            'success' => session('success'),
            'error' => session('error')
        ]
    ]);
}
    

    // Simpan role baru
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:roles,name']);

        Role::create(['name' => $request->name]);

        return redirect()->route('roles.index')
                         ->with('success', 'Role created successfully');
    }

    // Update role
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,'.$role->id,
        ]);

        $role->update(['name' => $request->name]);

        return redirect()->route('roles.index')
                         ->with('success', 'Role updated successfully');
    }

    // Hapus role
    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('roles.index')
                         ->with('success', 'Role deleted successfully');
    }
}
