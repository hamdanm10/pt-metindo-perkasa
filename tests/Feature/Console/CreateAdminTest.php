<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_active_admin_with_a_hashed_password_and_a_lowercase_email(): void
    {
        $this->assertSame(0, Artisan::call('admin:create', ['--name' => 'Ada', '--email' => 'Ada@Example.Test', '--password' => 'Secret123']));

        $user = User::query()->where('email', 'ada@example.test')->firstOrFail();

        $this->assertSame(Role::Admin, $user->role);
        $this->assertTrue($user->is_active);
        $this->assertSame('en', $user->locale);
        $this->assertTrue(Hash::check('Secret123', $user->password));
        $this->assertNotSame('Secret123', $user->password);
    }

    public function test_it_refuses_a_second_admin(): void
    {
        User::factory()->admin()->create();

        $this->assertSame(1, Artisan::call('admin:create', ['--name' => 'Ada', '--email' => 'ada@example.test', '--password' => 'Secret123']));
        $this->assertStringContainsString(__('users.errors.admin_exists'), Artisan::output());

        $this->assertSame(1, User::query()->where('role', Role::Admin->value)->count());
    }

    public function test_it_refuses_an_email_that_already_exists(): void
    {
        User::factory()->create(['email' => 'ada@example.test']);

        $this->assertSame(1, Artisan::call('admin:create', ['--name' => 'Ada', '--email' => 'ADA@example.test', '--password' => 'Secret123']));

        $this->assertSame(1, User::query()->count());
    }

    public function test_it_refuses_a_weak_password(): void
    {
        foreach (['short1A', 'alllowercase1', 'NoDigitsHere'] as $password) {
            $this->assertSame(1, Artisan::call('admin:create', ['--name' => 'Ada', '--email' => 'ada@example.test', '--password' => $password]));
        }

        $this->assertSame(0, User::query()->count());
    }

    public function test_users_cannot_be_given_a_role_through_mass_assignment(): void
    {
        $user = new User;
        $user->fill(['name' => 'X', 'email' => 'x@example.test', 'password' => 'Secret123', 'role' => 'admin', 'is_active' => false]);

        $this->assertNotSame(Role::Admin, $user->role);
        $this->assertNull($user->getAttribute('is_active'));
    }
}
