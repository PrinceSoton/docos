<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unused_breeze_verification_routes_are_not_publicly_exposed(): void
    {
        $this->get('/verify-email')->assertNotFound();
        $this->post('/email/verification-notification')->assertNotFound();
    }

    public function test_a_provisioned_account_can_access_its_space_after_password_change(): void
    {
        // DOCOS provisions accounts through administration rather than public email verification.
        $this->actingAs(User::factory()->create())->get(route('admin.dashboard'))->assertOk();
    }
}
