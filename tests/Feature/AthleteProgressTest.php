<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
            ->assertRedirect('/atleta/evolucao?aba=conquistas');

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

    public function test_a_conquest_can_be_saved_without_a_date(): void
    {
        $athlete = User::factory()->create();

        $this->actingAs($athlete)
            ->post('/atleta/conquistas', [
                'type' => 'cantinho',
                'detail' => 'Fiz marmita na semana',
            ])
            ->assertRedirect('/atleta/evolucao?aba=conquistas');

        $this->assertDatabaseHas('achievements', [
            'user_id' => $athlete->id,
            'type' => 'cantinho',
            'detail' => 'Fiz marmita na semana',
        ]);
    }

    public function test_an_athlete_can_edit_a_conquest_and_a_nutritionist_can_comment(): void
    {
        $athlete = User::factory()->create();

        $this->actingAs($athlete)
            ->post('/atleta/conquistas', [
                'type' => 'cantinho',
                'detail' => 'Fiz marmita na semana',
            ])
            ->assertRedirect('/atleta/evolucao?aba=conquistas');

        $achievement = $athlete->achievements()->first();

        $this->actingAs($athlete)
            ->put("/atleta/conquistas/{$achievement->id}", ['detail' => 'Marmita de frango'])
            ->assertRedirect('/atleta/evolucao?aba=conquistas');

        $this->actingAs($athlete)
            ->put("/atleta/conquistas/{$achievement->id}/comentario", [
                'comment' => 'Não pode',
                'emotion' => '👏',
            ])
            ->assertForbidden();

        $nutri = User::factory()->ofRole(UserRole::Nutricionista)->create();

        $this->actingAs($athlete)
            ->get('/atleta/evolucao')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('nutritionistName', $nutri->name));

        $this->actingAs($nutri)
            ->get("/equipe/atletas/{$athlete->id}/evolucao")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('dashboard/Evolucao')
                ->where('canComment', true)
                ->where('canEdit', false)
                ->where('achievements.0.detail', 'Marmita de frango'));

        $this->actingAs($nutri)
            ->put("/atleta/conquistas/{$achievement->id}/comentario", [
                'comment' => 'Continua assim',
                'emotion' => '👏',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('achievements', [
            'id' => $achievement->id,
            'detail' => 'Marmita de frango',
            'comment' => 'Continua assim',
            'emotion' => '👏',
            'nutritionist' => $nutri->name,
        ]);

        $this->actingAs(User::factory()->ofRole(UserRole::Coach)->create())
            ->get("/equipe/atletas/{$athlete->id}/evolucao")
            ->assertForbidden();
    }

    public function test_an_athlete_picks_three_different_skills_each_week(): void
    {
        $athlete = User::factory()->create();
        $marmita = 'Fazer marmitas para 3 dias da semana.';
        $pratos = 'Aprender a fazer 5 pratos saudáveis que nunca fez.';
        $salada = 'Aprender a fazer uma salada que realmente tenha vontade de comer.';
        $weeks = array_fill(0, 7, ['', '', '']);
        $weeks[0] = [$marmita, $pratos, $salada];
        $weeks[1] = [$marmita, '', ''];

        $this->actingAs($athlete)
            ->put('/atleta/habilidades', ['weeks' => $weeks])
            ->assertRedirect('/dashboard');

        $saved = $athlete->fresh()->athleteProfile->weekly_skills;

        $this->assertSame([$marmita, $pratos, $salada], $saved[0]);
        $this->assertSame([$marmita], $saved[1]);

        $weeks[0] = [$marmita, $marmita, ''];

        $this->actingAs($athlete)
            ->put('/atleta/habilidades', ['weeks' => $weeks])
            ->assertSessionHasErrors('weeks');
    }

    public function test_an_athlete_registers_the_day_one_baseline_once(): void
    {
        Storage::fake('public');
        $athlete = User::factory()->create();

        $this->actingAs($athlete)->get('/dashboard')->assertRedirect('/atleta/boas-vindas');

        $this->actingAs($athlete)
            ->post('/atleta/boas-vindas', [
                'weight' => 70.5,
                'waist' => 80,
                'abdomen' => 85,
                'hip' => 98,
                'photo' => UploadedFile::fake()->image('antes.jpg'),
            ])
            ->assertRedirect('/dashboard');

        $profile = $athlete->fresh()->athleteProfile;

        $this->assertNotNull($profile->welcomed_at);
        $this->assertSame('70.5', $profile->weight);
        $this->assertSame('80.0', $profile->waist);
        Storage::disk('public')->assertExists($profile->photo);

        $this->actingAs($athlete)->get('/dashboard')->assertOk();
        $this->actingAs($athlete)->get('/atleta/boas-vindas')->assertRedirect('/dashboard');
    }

    public function test_an_athlete_saves_a_shopping_list_for_one_week(): void
    {
        $athlete = User::factory()->create();

        $this->get('/objetivos/alimentacao/compras')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('canSave', false)->where('lists', []));

        $this->actingAs($athlete)
            ->put('/objetivos/alimentacao/compras', [
                'week' => 2,
                'items' => ['Patinho', 'Ovos de galinha'],
                'extras' => ['Queijo da fazenda'],
            ])
            ->assertRedirect('/objetivos/alimentacao/compras');

        $this->actingAs($athlete)
            ->get('/objetivos/alimentacao/compras')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('canSave', true)
                ->where('lists.0.week', 2)
                ->where('lists.0.period', '09/11 a 15/11')
                ->where('lists.0.items', ['Patinho', 'Ovos de galinha'])
                ->where('lists.0.extras', ['Queijo da fazenda']));

        $this->actingAs($athlete)
            ->put('/objetivos/alimentacao/compras', [
                'week' => 2,
                'items' => [],
                'extras' => [],
            ])
            ->assertRedirect('/objetivos/alimentacao/compras');

        $this->assertNull($athlete->fresh()->athleteProfile->shopping_lists);

        $this->actingAs(User::factory()->ofRole(UserRole::Coach)->create())
            ->put('/objetivos/alimentacao/compras', [
                'week' => 1,
                'items' => ['Patinho'],
            ])
            ->assertForbidden();
    }
}
