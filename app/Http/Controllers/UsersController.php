<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsersController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('name')->get();

        $stats = [
            'total'  => $users->count(),
            'admin'  => $users->where('role', 'admin')->count(),
            'staff'  => $users->where('role', 'staff')->count(),
            'viewer' => $users->where('role', 'viewer')->count(),
        ];

        return view('users.index', compact('users', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'role'                  => ['required', 'in:admin,staff,viewer'],
        ]);

        User::create($data);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', "unique:users,email,{$user->id}"],
            'password'              => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'                  => ['required', 'in:admin,staff,viewer'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Prevent an admin from changing their own role (avoid accidental lockout)
        if ($user->id === auth()->id()) {
            unset($data['role']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
