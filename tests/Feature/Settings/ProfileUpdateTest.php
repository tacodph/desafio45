<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/settings/profile')
            ->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
                ->where('paymentInfo.status', 'confirmado')
                ->where('paymentInfo.value', 79.9)
                ->where('paymentInfo.dueDate', null));

        $response->assertOk();
    }

    public function test_unpaid_athlete_sees_payment_status_and_due_date_on_profile_settings()
    {
        $user = User::factory()->unpaid()->create();
        $user->payments()->create([
            'status' => \App\Models\ChallengePayment::PENDING,
            'value' => 79.9,
            'due_date' => today()->addDays(2),
            'asaas_payment_id' => 'pay_profile_test',
        ]);

        $this->actingAs($user)
            ->get('/settings/profile')
            ->assertOk()
            ->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
                ->where('paymentInfo.status', 'pendente')
                ->where('paymentInfo.value', '79.90')
                ->where('paymentInfo.dueDate', today()->addDays(2)->format('d/m/Y')));
    }

    public function test_profile_information_can_be_updated()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/settings/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/settings/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/settings/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/settings/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_profile_photo_can_be_uploaded()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch('/settings/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => UploadedFile::fake()->image('perfil.jpg'),
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/settings/profile');

        $user->refresh();

        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $user->avatar));
    }

    public function test_user_can_delete_their_account()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/settings/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/settings/profile')
            ->delete('/settings/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrors('password')
            ->assertRedirect('/settings/profile');

        $this->assertNotNull($user->fresh());
    }
}
