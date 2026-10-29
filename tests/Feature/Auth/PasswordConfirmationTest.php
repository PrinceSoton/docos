<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_login_requires_the_password_change_screen(): void
    {
        $user = User::factory()->create(['password_changed_at' => null]);
        $this->actingAs($user)->get(route('admin.dashboard'))->assertRedirect(route('password.change'));
        $this->get(route('password.change'))->assertOk()->assertSee('Se déconnecter');
    }

    public function test_first_password_change_accepts_the_current_password(): void
    {
        $user = User::factory()->create(['password_changed_at' => null]);
        $this->actingAs($user)->post(route('password.change.update'), [
            'current_password' => 'password', 'password' => 'new-password', 'password_confirmation' => 'new-password',
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.dashboard'));
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
        $this->assertNotNull($user->fresh()->password_changed_at);
    }

    public function test_first_password_change_rejects_an_incorrect_current_password(): void
    {
        $user = User::factory()->create(['password_changed_at' => null]);
        $this->actingAs($user)->post(route('password.change.update'), [
            'current_password' => 'wrong-password', 'password' => 'new-password', 'password_confirmation' => 'new-password',
        ])->assertSessionHasErrors('current_password');
        $this->assertNull($user->fresh()->password_changed_at);
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_a_first_login_user_can_sign_out_without_the_provisional_password(): void
    {
        $user = User::factory()->create(['password_changed_at' => null]);
        $this->actingAs($user)->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
