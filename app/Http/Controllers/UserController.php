<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\UserData;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Role;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $users = User::query()
            ->orderBy('role')
            ->latest()
            ->paginate(15)
            ->through(
                fn($user) =>
                UserData::fromModel($user)
            );

        return Inertia::render('Users/Index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        $this->authorize('create', User::class);

        return Inertia::render('Users/Create');
    }

    public function store(UserRequest $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validated();
        $validated['name'] = ucfirst($validated['name']);
        $validated['password'] = Hash::make(Str::password(32));
        $user = User::create($validated);

        //Password::sendResetLink(['email' => $user->email]);

        return to_route('users');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return Inertia::render('Users/Edit', [
            'user' => $user,
            'roles' => array_column(Role::cases(), 'value'),
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validated();
        $user->update($validated);

        return to_route('users');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return to_route('users');
    }
}
