<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObjectiveRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_luxury_meal_objective_opens_at_its_canonical_route(): void
    {
        $this->get('/objetivo/luxo')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Objetivos')
                ->where('goal', 'plus'));
    }

    public function test_old_luxury_meal_route_redirects_to_the_canonical_route(): void
    {
        $this->get('/objetivos/plus')
            ->assertRedirect('/objetivo/luxo');
    }
}
