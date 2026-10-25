<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Achievement;
use App\Models\AthleteProfile;
use App\Models\Checkin;
use App\Models\User;
use App\Models\UserType;
use App\Support\ChallengeSheet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AthleteProgressController extends Controller
{
    public function welcome(Request $request): Response|RedirectResponse
    {
        $this->abortUnlessAthlete($request);

        if ($request->user()->athleteProfile?->welcomed_at) {
            return to_route('dashboard');
        }

        return Inertia::render('dashboard/BoasVindas');
    }

    public function storeWelcome(Request $request): RedirectResponse
    {
        $this->abortUnlessAthlete($request);

        $data = $request->validate([
            'weight' => 'nullable|numeric|between:30,300',
            'waist' => 'nullable|numeric|between:40,200',
            'abdomen' => 'nullable|numeric|between:40,200',
            'hip' => 'nullable|numeric|between:40,200',
            'photo' => 'nullable|image|max:5120',
        ]);

        $profile = AthleteProfile::query()->firstOrNew(['user_id' => $request->user()->id]);

        if ($request->hasFile('photo')) {
            if ($profile->photo) {
                Storage::disk('public')->delete($profile->photo);
            }

            $profile->photo = $request->file('photo')->store('baselines', 'public');
        }

        $profile->fill([
            'weight' => $data['weight'] ?? null,
            'waist' => $data['waist'] ?? null,
            'abdomen' => $data['abdomen'] ?? null,
            'hip' => $data['hip'] ?? null,
            'welcomed_at' => now(),
        ])->save();

        return to_route('dashboard');
    }

    public function show(Request $request): Response
    {
        $this->abortUnlessAthlete($request);

        return $this->page($request->user(), true, false);
    }

    public function showAthlete(Request $request, User $athlete): Response
    {
        abort_unless(UserRole::from($request->user()->userType->name) === UserRole::Nutricionista, 403);
        abort_unless($athlete->userType->name === UserRole::Atleta->value, 404);

        return $this->page($athlete, false, true);
    }

    public function updateGoals(Request $request): RedirectResponse
    {
        $this->abortUnlessAthlete($request);

        $data = $request->validate([
            'movement_level' => 'nullable|in:iniciante,intermediario,avancado',
            'movement_minutes' => 'nullable|integer|min:0|max:300',
            'water_liters' => 'nullable|numeric|min:0|max:15',
            'sleep_goal' => 'nullable|string|max:255',
            'skill' => 'nullable|string|max:255',
            'strength_goal' => 'nullable|string|max:255',
            'test_1' => 'nullable|string|max:255',
            'test_15' => 'nullable|string|max:255',
            'test_30' => 'nullable|string|max:255',
            'test_45' => 'nullable|string|max:255',
            'penalty' => 'boolean',
            'penalty_donated_on' => 'nullable|date',
            'penalty_note' => 'nullable|string|max:255',
        ]);

        AthleteProfile::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data,
        );

        return to_route('athlete.progress');
    }

    public function updateSkills(Request $request): RedirectResponse
    {
        $this->abortUnlessAthlete($request);

        $data = $request->validate([
            'weeks' => 'required|array|size:7',
            'weeks.*' => 'array|max:3',
            'weeks.*.*' => 'nullable|string|max:180',
        ]);

        $weeks = collect($data['weeks'])->map(function (array $picks) {
            $chosen = array_values(array_filter($picks, fn ($item) => is_string($item) && $item !== ''));

            if (count($chosen) !== count(array_unique($chosen))) {
                throw ValidationException::withMessages([
                    'weeks' => 'Na mesma semana, escolha habilidades diferentes.',
                ]);
            }

            return $chosen;
        })->all();

        AthleteProfile::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            ['weekly_skills' => $weeks],
        );

        return to_route('dashboard');
    }

    public function updateDays(Request $request): RedirectResponse
    {
        $this->abortUnlessAthlete($request);

        $data = $request->validate([
            'days' => 'required|array|max:45',
            'days.*.day' => 'required|integer|between:1,45|distinct',
            'days.*.checked_in' => 'boolean',
            'days.*.movement' => 'boolean',
            'days.*.water' => 'boolean',
            'days.*.sleep' => 'boolean',
            'days.*.strength' => 'boolean',
            'days.*.skill' => 'boolean',
            'days.*.plus_count' => 'integer|min:0|max:9',
            'days.*.notes' => 'nullable|array',
            'days.*.notes.*' => 'nullable|string|max:255',
        ]);

        foreach ($data['days'] as $day) {
            Checkin::query()->updateOrCreate(
                ['user_id' => $request->user()->id, 'day' => $day['day']],
                [
                    'checked_in' => $day['checked_in'] ?? false,
                    'movement' => $day['movement'] ?? false,
                    'water' => $day['water'] ?? false,
                    'sleep' => $day['sleep'] ?? false,
                    'strength' => $day['strength'] ?? false,
                    'skill' => $day['skill'] ?? false,
                    'plus_count' => $day['plus_count'] ?? 0,
                    'notes' => collect($day['notes'] ?? [])
                        ->only(['movement', 'water', 'sleep', 'strength', 'skill', 'plus'])
                        ->map(fn ($note) => filled($note) ? $note : null)
                        ->filter()
                        ->all() ?: null,
                ],
            );
        }

        return to_route('athlete.progress');
    }

    public function storeAchievement(Request $request): RedirectResponse
    {
        $this->abortUnlessAthlete($request);

        $data = $request->validate([
            'achieved_on' => 'nullable|date|after_or_equal:2026-11-06|before_or_equal:2026-12-20',
            'type' => 'required|in:'.implode(',', array_keys(ChallengeSheet::ACHIEVEMENTS)),
            'detail' => 'required|string|max:255',
        ]);

        $request->user()->achievements()->create([
            ...$data,
            'achieved_on' => $data['achieved_on'] ?? now()->toDateString(),
        ]);

        return to_route('athlete.progress', ['aba' => 'conquistas']);
    }

    public function updateAchievement(Request $request, Achievement $achievement): RedirectResponse
    {
        $this->abortUnlessAthlete($request);
        abort_unless($achievement->user_id === $request->user()->id, 403);

        $achievement->update($request->validate([
            'detail' => 'required|string|max:255',
        ]));

        return to_route('athlete.progress', ['aba' => 'conquistas']);
    }

    public function commentAchievement(Request $request, Achievement $achievement): RedirectResponse
    {
        abort_unless(UserRole::from($request->user()->userType->name) === UserRole::Nutricionista, 403);
        abort_unless($achievement->user->userType->name === UserRole::Atleta->value, 403);

        $achievement->update([
            ...$request->validate([
                'comment' => 'required|string|max:500',
                'emotion' => 'required|string|max:16',
            ]),
            'nutritionist' => $request->user()->name,
        ]);

        return back();
    }

    public function destroyAchievement(Request $request, Achievement $achievement): RedirectResponse
    {
        $this->abortUnlessAthlete($request);
        abort_unless($achievement->user_id === $request->user()->id, 403);

        $achievement->delete();

        return to_route('athlete.progress', ['aba' => 'conquistas']);
    }

    private function page(User $athlete, bool $canEdit, bool $canComment): Response
    {
        return Inertia::render('dashboard/Evolucao', [
            ...ChallengeSheet::payload($athlete),
            'canEdit' => $canEdit,
            'canComment' => $canComment,
            'athleteName' => $canComment ? $athlete->name : null,
            'nutritionistName' => User::query()
                ->where('user_type_id', UserType::idFor(UserRole::Nutricionista))
                ->orderBy('name')
                ->value('name'),
        ]);
    }

    private function abortUnlessAthlete(Request $request): void
    {
        abort_unless(UserRole::from($request->user()->userType->name) === UserRole::Atleta, 403);
    }
}
