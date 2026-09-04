<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('roles')->latest('id')->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->get(['id', 'name']);
        $user = new User;

        return view('users.create', compact('roles', 'user'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::create([
            'dni' => $data['dni'] ?? null,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => $data['password'],
            'cargo' => $data['cargo'] ?? null,
            'status' => $data['status'] ?? true,
        ]);

        $user->syncRoles($data['roles']);

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->get(['id', 'name']);

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        $user->update([
            'dni' => $data['dni'] ?? null,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'cargo' => $data['cargo'] ?? null,
            'status' => $data['status'] ?? true,
        ]);

        if (! empty($data['password'])) {
            $user->update(['password' => $data['password']]);
        }

        $user->syncRoles($data['roles']);

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()
                ->route('users.index')
                ->withErrors('No puedes eliminar tu propio usuario.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
