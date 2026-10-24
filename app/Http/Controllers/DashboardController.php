<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $role = $this->role($request);

        if ($role === UserRole::Administrador) {
            return Inertia::render('dashboard/Admin', [
                'counts' => UserType::query()->withCount('users')->orderBy('id')->get(),
            ]);
        }

        if ($role === UserRole::Atleta) {
            return Inertia::render('dashboard/Atleta');
        }

        return Inertia::render('dashboard/Equipe', [
            'athleteCount' => User::query()->where('user_type_id', UserType::idFor(UserRole::Atleta))->count(),
        ]);
    }

    public function users(Request $request): Response
    {
        $this->abortUnless($request, UserRole::Administrador);

        return Inertia::render('dashboard/Users', [
            'users' => User::query()->with('userType:id,name')->orderBy('name')->get(['id', 'name', 'email', 'user_type_id']),
        ]);
    }

    public function createUser(Request $request): Response
    {
        $this->abortUnless($request, UserRole::Administrador);

        return Inertia::render('dashboard/UserCreate', [
            'types' => UserType::query()->orderBy('id')->get(['id', 'name']),
        ]);
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $this->abortUnless($request, UserRole::Administrador);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Password::defaults()],
            'user_type_id' => 'required|integer|exists:user_types,id',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'user_type_id' => $data['user_type_id'],
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();

        return to_route('admin.users');
    }

    public function athletes(Request $request): Response
    {
        $role = $this->role($request);

        if ($role === UserRole::Administrador || $role === UserRole::Atleta) {
            abort(403);
        }

        return Inertia::render('dashboard/Athletes', [
            'athletes' => User::query()
                ->where('user_type_id', UserType::idFor(UserRole::Atleta))
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
        ]);
    }

    private function role(Request $request): UserRole
    {
        return UserRole::from($request->user()->userType->name);
    }

    private function abortUnless(Request $request, UserRole $role): void
    {
        abort_unless($this->role($request) === $role, 403);
    }
}
