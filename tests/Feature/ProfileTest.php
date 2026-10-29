<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $this->actingAs(User::factory()->create())->get(route('profile.index'))->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->put(route('profile.update'), [
            'nom' => 'Nom Test', 'prenom' => 'Prénom Test', 'email' => 'test@example.test',
        ])->assertSessionHasNoErrors()->assertRedirect(route('profile.index'));
        $this->assertSame('Nom Test', $user->fresh()->nom);
        $this->assertSame('Prénom Test', $user->fresh()->prenom);
        $this->assertSame('test@example.test', $user->fresh()->email);
    }

    public function test_profile_update_rejects_an_email_already_used_by_another_account(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($user)->put(route('profile.update'), [
            'nom' => $user->nom, 'prenom' => $user->prenom, 'email' => $other->email,
        ])->assertSessionHasErrors('email');
        $this->assertSame($user->email, $user->fresh()->email);
    }

    public function test_profile_form_cannot_change_the_account_role(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->put(route('profile.update'), [
            'nom' => $user->nom, 'prenom' => $user->prenom, 'email' => $user->email, 'role' => 'mentor',
        ])->assertSessionHasNoErrors();
        $this->assertSame('admin', $user->fresh()->role);
    }

    public function test_self_service_account_deletion_is_not_available(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->delete(route('profile.index'))->assertStatus(405);
        $this->assertNotNull($user->fresh());
    }
}
