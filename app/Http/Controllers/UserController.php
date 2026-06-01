<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
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
        if (!$request->user()?->isAdmin()) {
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

    public function create(Request $request)
    {
        if (!$request->user()?->isAdmin()) {
            abort(403);
        }

        return Inertia::render('Users/Create');
    }

    public function store(UserRequest $request)
    {
        if (!$request->user()?->isAdmin()) {
            abort(403);
        }

        $validated = $request->validated();
        $validated['name'] = ucfirst($validated['name']); //capitalize name
        $validated['password'] = bcrypt('changeme123'); //default password
        User::create($validated);

        //Password::sendResetLink(['email' => $validated['email']]);

        return to_route('users')->with('success', 'User created successfully.');
    }


    public function edit(Request $request, User $user)
    {
        if (!$request->user()?->isAdmin()) {
            abort(403);
        }

        return Inertia::render('Users/Edit', [
            'user' => $user,
            'roles' => array_column(Role::cases(), 'value'),
        ]);
    }


    public function update(UserRequest $request, User $user)
    {
        if (!$request->user()?->isAdmin()) {
            abort(403);
        }

        $validated = $request->validated();
        $user->update($validated);

        return to_route('users')->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user)
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'You don\'t have permission to delete users.');
        }

        if ($user->isAdmin()) {
            abort(403, 'Cannot delete an admin.');
        }

        if ($user->pokemons()->exists()) {
            abort(403, 'Cannot delete a user who has pokémons.');
        }
        $user->delete();
        return to_route('users')->with('success', 'User deleted successfully');
    }
}
