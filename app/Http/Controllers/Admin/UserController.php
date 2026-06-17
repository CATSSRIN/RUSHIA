<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function indexWarehouses()
    {
        $warehouses = User::where('is_warehouse', true)->latest()->paginate(20);
        return view('admin.warehouses.index', compact('warehouses'));
    }

    public function createWarehouse()
    {
        return view('admin.warehouses.create');
    }

    public function storeWarehouse(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $u = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'is_warehouse' => true,
        ]);

        \App\Models\ActivityLog::log('create_warehouse', 'Membuat akun Warehouse baru: ' . $u->name . ' (' . $u->email . ')');

        return redirect()->route('admin.warehouses.index')->with('success', 'Warehouse account created successfully.');
    }

    public function destroyWarehouse(User $user)
    {
        abort_unless($user->is_warehouse, 404);

        \App\Models\ActivityLog::log('delete_warehouse', 'Menghapus akun Warehouse: ' . $user->name . ' (' . $user->email . ')');

        $user->delete();

        return redirect()->route('admin.warehouses.index')->with('success', 'Warehouse account deleted successfully.');
    }

    public function index()
    {
        $users = User::where('is_admin', false)->where('is_warehouse', false)->withCount('ships')->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create', ['isAdmin' => false]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $u = User::create([
            'name'         => $request->name,
            'company_name' => $request->company_name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'is_admin'     => false,
        ]);

        \App\Models\ActivityLog::log('create_user', 'Membuat akun User baru: ' . $u->name . ' (' . $u->email . ')');

        return redirect()->route('admin.users.index')->with('success', 'User account created successfully.');
    }

    public function indexAdmins()
    {
        $admins = User::where('is_admin', true)->latest()->paginate(20);
        return view('admin.admins.index', compact('admins'));
    }

    public function showAdmin(User $user)
    {
        abort_unless($user->is_admin, 404);
        return view('admin.admins.show', compact('user'));
    }

    public function destroyAdmin(User $user)
    {
        abort_unless($user->is_admin, 404);
        abort_if($user->id === auth()->id(), 403, 'You cannot delete your own admin account.');

        \App\Models\ActivityLog::log('delete_admin', 'Menghapus akun Admin: ' . $user->name . ' (' . $user->email . ')');

        $user->delete();

        return redirect()->route('admin.admins.index')->with('success', 'Admin account deleted successfully.');
    }

    public function createAdmin()
    {
        return view('admin.users.create', ['isAdmin' => true]);
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $u = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => true,
        ]);

        \App\Models\ActivityLog::log('create_admin', 'Membuat akun Admin baru: ' . $u->name . ' (' . $u->email . ')');

        return redirect()->route('admin.admins.index')->with('success', 'Admin account created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password'     => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->name         = $request->name;
        $user->company_name = $request->company_name;
        $user->email        = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        \App\Models\ActivityLog::log('update_user', 'Memperbarui akun User/Admin: ' . $user->name . ' (' . $user->email . ')');

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        \App\Models\ActivityLog::log('delete_user', 'Menghapus akun User: ' . $user->name . ' (' . $user->email . ')');

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
