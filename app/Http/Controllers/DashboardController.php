<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Food;
use App\Models\FoodGroup;
use App\Models\User;
use App\Models\UserType;
use App\Support\ChallengeSheet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response|RedirectResponse
    {
        $role = $this->role($request);

        if ($role === UserRole::Administrador) {
            return Inertia::render('dashboard/Admin', [
                'counts' => UserType::query()->withCount('users')->orderBy('id')->get(),
            ]);
        }

        if ($role === UserRole::Atleta) {
            $user = $request->user();
            $user->load(['checkins', 'achievements', 'athleteProfile']);

            if (! $user->athleteProfile?->welcomed_at) {
                return to_route('athlete.start.first-day');
            }

            if (! $user->athleteProfile->goals_set_at) {
                return to_route('athlete.start.goals');
            }
            $saved = $user->athleteProfile?->weekly_skills ?? [];
            $skillWeeks = [];

            foreach (ChallengeSheet::WEEKS as $week => [$from, $to]) {
                $skillWeeks[] = [
                    'week' => $week,
                    'period' => Carbon::parse(ChallengeSheet::dateForDay($from))->format('d/m').' a '.Carbon::parse(ChallengeSheet::dateForDay($to))->format('d/m'),
                    'picks' => array_pad(array_slice($saved[$week - 1] ?? [], 0, 3), 3, ''),
                ];
            }

            $profile = $user->athleteProfile;

            return Inertia::render('dashboard/Atleta', [
                'total' => collect(ChallengeSheet::weeks($user))->sum('total'),
                'skillWeeks' => $skillWeeks,
                'baseline' => [
                    'weight' => $profile->weight,
                    'waist' => $profile->waist,
                    'abdomen' => $profile->abdomen,
                    'hip' => $profile->hip,
                    'photo' => $profile->photo ? '/storage/'.$profile->photo : null,
                ],
            ]);
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

    public function foods(Request $request): Response
    {
        $this->abortUnless($request, UserRole::Administrador);

        return Inertia::render('dashboard/Foods', [
            'groups' => FoodGroup::query()->with('foods')->orderBy('position')->orderBy('id')->get(),
        ]);
    }

    public function storeFood(Request $request): RedirectResponse
    {
        $this->abortUnless($request, UserRole::Administrador);

        $data = $request->validate([
            'category' => 'required|string|max:80',
            'group' => 'nullable|string|max:80',
            'name' => 'required|string|max:120',
        ]);

        $group = FoodGroup::query()->firstOrCreate(
            ['category' => trim($data['category']), 'name' => trim($data['group'] ?? '')],
            ['position' => ((int) FoodGroup::query()->max('position')) + 1],
        );

        $group->foods()->create([
            'name' => trim($data['name']),
            'position' => ((int) $group->foods()->max('position')) + 1,
        ]);

        return to_route('admin.foods');
    }

    public function toggleFood(Request $request, Food $food): RedirectResponse
    {
        $this->abortUnless($request, UserRole::Administrador);

        $food->update(['active' => ! $food->active]);

        return to_route('admin.foods');
    }

    public function destroyFood(Request $request, Food $food): RedirectResponse
    {
        $this->abortUnless($request, UserRole::Administrador);

        $group = $food->group;
        $food->delete();

        if ($group && $group->foods()->doesntExist() && ($group->notes ?? []) === []) {
            $group->delete();
        }

        return to_route('admin.foods');
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
