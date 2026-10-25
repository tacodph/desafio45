<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AthleteProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_an_athlete_can_fill_the_progress(): void
    {
        $this->actingAs(User::factory()->ofRole(UserRole::Administrador)->create())
            ->get('/atleta/evolucao')
            ->assertForbidden();

        $athlete = User::factory()->create();

        $this->actingAs($athlete)
            ->get('/atleta/evolucao')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('dashboard/Evolucao')->where('total', 0));

        $this->actingAs($athlete)
            ->put('/atleta/metas', [
                'movement_level' => 'iniciante',
                'movement_minutes' => 30,
                'water_liters' => 2.5,
                'sleep_goal' => '7h',
                'skill' => '5 pratos',
                'strength_goal' => 'prancha 60s',
                'penalty' => true,
            ])
            ->assertRedirect('/atleta/evolucao');

        $this->assertDatabaseHas('athlete_profiles', [
            'user_id' => $athlete->id,
            'movement_level' => 'iniciante',
            'skill' => '5 pratos',
            'penalty' => true,
        ]);
    }

    public function test_week_one_points_include_daily_score_and_bonuses(): void
    {
        $athlete = User::factory()->create();
        $days = [];

        foreach ([1, 2, 3] as $day) {
            $days[] = [
                'day' => $day,
                'checked_in' => true,
                'movement' => true,
                'water' => true,
                'sleep' => true,
                'strength' => true,
                'skill' => true,
                'plus_count' => $day === 1 ? 3 : 0,
                'notes' => '',
            ];
        }

        $this->actingAs($athlete)
            ->put('/atleta/dias', ['days' => $days])
            ->assertRedirect('/atleta/evolucao');

        $this->actingAs($athlete)
            ->post('/atleta/conquistas', [
                'achieved_on' => '2026-11-06',
                'type' => 'first_time',
                'detail' => 'Primeira prancha',
            ])
            ->assertRedirect('/atleta/evolucao');

        $this->actingAs($athlete)
            ->get('/atleta/evolucao')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('weeks.0.daily', 30)
                ->where('weeks.0.plus_bonus', 10)
                ->where('weeks.0.consistency', 10)
                ->where('weeks.0.goals_bonus', 10)
                ->where('weeks.0.achievements', 5)
                ->where('total', 65));
    }
}
