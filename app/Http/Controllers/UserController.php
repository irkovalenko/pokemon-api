<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()?->role !== Role::ADMIN) {
            abort(403);
        }

        $users = User::query()
            ->orderBy('role')
            ->latest()
            ->paginate(15)
            ->through(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at->format('d M Y, H:i:s'),
            ]);

        return Inertia::render('Users/Index', [
            'users' => $users,
        ]);
    }


    public function edit(Request $request, User $user)
    {
        if ($request->user()?->role !== Role::ADMIN) {
            abort(403);
        }

        return Inertia::render('Users/Edit', [
            'user' => $user,
            'roles' => array_column(Role::cases(), 'value'),
        ]);
    }


    public function update(Request $request, User $user)
    {
        if ($request->user()->role !== Role::ADMIN) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'role' => ['required', Rule::enum(Role::class)],
        ]);

        $user->update($validated);

        return to_route('users')->with('success', 'User updated successfully.');
    }
}
