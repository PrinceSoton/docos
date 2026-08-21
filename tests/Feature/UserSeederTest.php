<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seed_is_idempotent(): void
    {
        $seeder = new UserSeeder();

        $seeder->run();
        $seeder->run();

        $this->assertDatabaseCount('users', 3);
        $this->assertDatabaseCount('mentors', 1);
        $this->assertDatabaseCount('stagiaires', 1);
        $this->assertDatabaseCount('config_jours_travail', 1);
    }
}
