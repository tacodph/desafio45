<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Achievement;
use App\Models\AthleteProfile;
use App\Models\Checkin;
use App\Support\ChallengeSheet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AthleteProgressController extends Controller
{
    public function show(Request $request): Response
    {
        $this->abortUnlessAthlete($request);

        return Inertia::render('dashboard/Evolucao', ChallengeSheet::payload($request->user()));
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
            'days.*.notes' => 'nullable|string|max:255',
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
                    'notes' => $day['notes'] ?? null,
                ],
            );
        }

        return to_route('athlete.progress');
    }

    public function storeAchievement(Request $request): RedirectResponse
    {
        $this->abortUnlessAthlete($request);

        $data = $request->validate([
            'achieved_on' => 'required|date|after_or_equal:2026-11-06|before_or_equal:2026-12-20',
            'type' => 'required|in:'.implode(',', array_keys(ChallengeSheet::ACHIEVEMENTS)),
            'detail' => 'nullable|string|max:255',
        ]);

        $request->user()->achievements()->create($data);

        return to_route('athlete.progress');
    }

    public function destroyAchievement(Request $request, Achievement $achievement): RedirectResponse
    {
        $this->abortUnlessAthlete($request);
        abort_unless($achievement->user_id === $request->user()->id, 403);

        $achievement->delete();

        return to_route('athlete.progress');
    }

    private function abortUnlessAthlete(Request $request): void
    {
        abort_unless(UserRole::from($request->user()->userType->name) === UserRole::Atleta, 403);
    }
}
