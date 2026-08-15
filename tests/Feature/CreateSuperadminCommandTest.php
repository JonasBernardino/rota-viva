<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateSuperadminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_creates_superadmin_user(): void
    {
        $this->artisan('platform:create-superadmin', [
            'email' => 'superadmin@example.com',
            '--name' => 'Superadmin',
            '--password' => 'senha-segura',
        ])->assertSuccessful();

        $user = User::where('email', 'superadmin@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->can_manage_platform);
        $this->assertFalse($user->can_access_admin_panel);
    }
}
