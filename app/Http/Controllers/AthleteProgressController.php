<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Achievement;
use App\Models\AthleteProfile;
use App\Models\Checkin;
use App\Models\FoodGroup;
use App\Models\User;
use App\Models\UserType;
use App\Support\ChallengeSheet;
use App\Support\DayLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AthleteProgressController extends Controller
{
    public function firstDay(Request $request): Response
    {
        $this->abortUnlessAthlete($request);

        $profile = $request->user()->athleteProfile;

        return Inertia::render('dashboard/PrimeiroDia', [
            'firstAccess' => ! $profile?->welcomed_at,
            'baseline' => [
                'weight' => $profile?->weight ?? '',
                'waist' => $profile?->waist ?? '',
                'abdomen' => $profile?->abdomen ?? '',
                'hip' => $profile?->hip ?? '',
                'photo' => $profile?->photo ? '/storage/'.$profile->photo : null,
            ],
        ]);
    }

    public function storeFirstDay(Request $request): RedirectResponse
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
            'welcomed_at' => $profile->welcomed_at ?? now(),
        ])->save();

        // No primeiro acesso, o segundo passo é preencher as metas.
        return $profile->goals_set_at ? to_route('athlete.start.first-day') : to_route('athlete.start.goals');
    }

    public function goals(Request $request): Response
    {
        $this->abortUnlessAthlete($request);

        $payload = ChallengeSheet::payload($request->user());

        return Inertia::render('dashboard/Metas', [
            'firstAccess' => ! $request->user()->athleteProfile?->goals_set_at,
            'profile' => $payload['profile'],
            'penalty' => $payload['penalty'],
            'weight' => $request->user()->athleteProfile?->weight,
            'waterMinimum' => DayLog::waterMinimumLiters($request->user()->athleteProfile?->weight),
        ]);
    }
    public function show(Request $request, string $tab): Response
    {
        $this->abortUnlessAthlete($request);

        return $this->page($request->user(), true, false, $tab);
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

        $profile = AthleteProfile::query()->firstOrNew(['user_id' => $request->user()->id]);
        $firstTime = $profile->goals_set_at === null;

        $profile->fill([...$data, 'goals_set_at' => $profile->goals_set_at ?? now()])->save();

        return $firstTime ? to_route('dashboard') : to_route('athlete.start.goals');
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
            'days.*.details' => 'nullable|array',
            'days.*.details.workouts' => 'nullable|array|max:6',
            'days.*.details.workouts.*.type' => 'required|string|max:40',
            'days.*.details.workouts.*.minutes' => 'nullable|integer|min:0|max:600',
            'days.*.details.water_ml' => 'nullable|integer|min:0|max:10000',
            'days.*.details.sleep_hours' => 'nullable|numeric|min:0|max:24',
            'days.*.details.food' => 'nullable|array',
            'days.*.details.food.*' => 'boolean',
            'days.*.details.skill' => 'nullable|string|max:180',
            'days.*.details.manual' => 'nullable|array',
            'days.*.details.manual.*' => 'boolean',
        ]);

        $profile = $request->user()->athleteProfile;

        foreach ($data['days'] as $day) {
            $details = isset($day['details']) ? DayLog::normalize($day['details']) : null;
            $achieved = $details ? DayLog::achieved($details, $profile) : [
                'movement' => $day['movement'] ?? false,
                'water' => $day['water'] ?? false,
                'sleep' => $day['sleep'] ?? false,
                'strength' => $day['strength'] ?? false,
                'skill' => $day['skill'] ?? false,
            ];

            Checkin::query()->updateOrCreate(
                ['user_id' => $request->user()->id, 'day' => $day['day']],
                [
                    'checked_in' => $day['checked_in'] ?? false,
                    ...$achieved,
                    'details' => $details,
                    'plus_count' => $day['plus_count'] ?? 0,
                    'notes' => collect($day['notes'] ?? [])
                        ->only(['movement', 'water', 'sleep', 'strength', 'skill', 'plus', 'day'])
                        ->map(fn ($note) => filled($note) ? $note : null)
                        ->filter()
                        ->all() ?: null,
                ],
            );
        }

        return to_route('athlete.progress.tab', 'dias');
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

        return to_route('athlete.progress.tab', 'conquistas');
    }

    public function updateAchievement(Request $request, Achievement $achievement): RedirectResponse
    {
        $this->abortUnlessAthlete($request);
        abort_unless($achievement->user_id === $request->user()->id, 403);

        $achievement->update($request->validate([
            'detail' => 'required|string|max:255',
        ]));

        return to_route('athlete.progress.tab', 'conquistas');
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

        return to_route('athlete.progress.tab', 'conquistas');
    }

    private function page(User $athlete, bool $canEdit, bool $canComment, ?string $tab = null): Response
    {
        return Inertia::render('dashboard/Evolucao', [
            ...ChallengeSheet::payload($athlete),
            'tab' => $tab,
            'canEdit' => $canEdit,
            'canComment' => $canComment,
            'athleteName' => $canComment ? $athlete->name : null,
            'nutritionistName' => User::query()
                ->where('user_type_id', UserType::idFor(UserRole::Nutricionista))
                ->orderBy('name')
                ->value('name'),
        ]);
    }

    public function shopping(Request $request): Response
    {
        $user = $request->user();
        $canSave = $user && UserRole::tryFrom($user->userType->name) === UserRole::Atleta;

        return Inertia::render('Compras', [
            'weeks' => $this->shoppingWeeks(),
            'lists' => $canSave ? $this->shoppingLists($user) : [],
            'canSave' => $canSave,
            'catalog' => FoodGroup::catalog(),
        ]);
    }

    public function createShopping(Request $request): Response|RedirectResponse
    {
        $this->abortUnlessAthlete($request);

        $used = collect($this->shoppingLists($request->user()))->pluck('week');
        $free = $this->shoppingWeeks()->reject(fn (array $week) => $used->contains($week['week']))->values();

        if ($free->isEmpty()) {
            return to_route('alimentacao.compras');
        }

        return Inertia::render('ComprasForm', [
            'weeks' => $free,
            'week' => $free->first()['week'],
            'list' => null,
            'catalog' => FoodGroup::catalog(),
        ]);
    }

    public function editShopping(Request $request, int $week): Response
    {
        $this->abortUnlessAthlete($request);

        $list = collect($this->shoppingLists($request->user()))->firstWhere('week', $week);
        abort_if($list === null, 404);

        return Inertia::render('ComprasForm', [
            'weeks' => $this->shoppingWeeks()->where('week', $week)->values(),
            'week' => $week,
            'list' => $list,
            'catalog' => FoodGroup::catalog(),
        ]);
    }

    public function storeShopping(Request $request): RedirectResponse
    {
        return $this->saveShopping($request, true);
    }

    public function updateShopping(Request $request): RedirectResponse
    {
        return $this->saveShopping($request, false);
    }

    private function saveShopping(Request $request, bool $new): RedirectResponse
    {
        $this->abortUnlessAthlete($request);

        $data = $request->validate([
            'week' => 'required|integer|between:1,7',
            'items' => 'array|max:300',
            'items.*' => 'string|max:120',
            'extras' => 'array|max:30',
            'extras.*' => 'nullable|string|max:120',
        ]);

        $items = array_values(array_unique(array_filter($data['items'] ?? [])));
        $extras = array_values(array_filter(array_map(
            fn ($item) => is_string($item) ? trim($item) : '',
            $data['extras'] ?? [],
        )));

        $profile = AthleteProfile::query()->firstOrNew(['user_id' => $request->user()->id]);
        $lists = $profile->shopping_lists ?? [];
        $key = (string) $data['week'];

        if ($new && isset($lists[$key])) {
            throw ValidationException::withMessages(['week' => 'Essa semana já tem uma lista. Edite a existente.']);
        }

        if ($items === [] && $extras === []) {
            unset($lists[$key]);
        } else {
            $lists[$key] = ['items' => $items, 'extras' => $extras];
        }

        $profile->shopping_lists = $lists === [] ? null : $lists;
        $profile->save();

        return to_route('alimentacao.compras');
    }

    /** @return \Illuminate\Support\Collection<int, array{week: int, period: string}> */
    private function shoppingWeeks()
    {
        return collect(ChallengeSheet::WEEKS)->map(function (array $range, int $week) {
            [$from, $to] = $range;

            return [
                'week' => $week,
                'period' => Carbon::parse(ChallengeSheet::dateForDay($from))->format('d/m').' a '.Carbon::parse(ChallengeSheet::dateForDay($to))->format('d/m'),
            ];
        })->values();
    }

    /** @return list<array{week: int, period: string, items: list<string>, extras: list<string>}> */
    private function shoppingLists(User $user): array
    {
        $saved = $user->athleteProfile?->shopping_lists ?? [];

        return $this->shoppingWeeks()->map(function (array $week) use ($saved) {
            $row = $saved[(string) $week['week']] ?? null;

            if (! is_array($row) || (($row['items'] ?? []) === [] && ($row['extras'] ?? []) === [])) {
                return null;
            }

            return [
                'week' => $week['week'],
                'period' => $week['period'],
                'items' => array_values($row['items'] ?? []),
                'extras' => array_values($row['extras'] ?? []),
            ];
        })->filter()->values()->all();
    }

    private function abortUnlessAthlete(Request $request): void
    {
        abort_unless(UserRole::from($request->user()->userType->name) === UserRole::Atleta, 403);
    }
}
