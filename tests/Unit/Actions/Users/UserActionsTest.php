<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Users;

use App\Actions\Users\CreateUser;
use App\Actions\Users\ResetUserPassword;
use App\Actions\Users\SetUserActive;
use App\Actions\Users\UpdateUser;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UserActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_updating_changes_the_name_and_email_but_never_the_role(): void
    {
        $user = User::factory()->customer()->create(['email' => 'old@example.test']);

        app(UpdateUser::class)->execute($user, 'New Name', 'New@Example.Test');

        $user->refresh();

        $this->assertSame('New Name', $user->name);
        $this->assertSame('new@example.test', $user->email);
        $this->assertSame(Role::Customer, $user->role);
    }

    public function test_there_is_no_limit_on_the_number_of_admins(): void
    {
        $create = app(CreateUser::class);

        foreach (['a', 'b', 'c', 'd'] as $name) {
            $create->execute($name, "{$name}@example.test", 'Secret123', Role::Admin);
        }

        $this->assertSame(4, User::query()->where('role', Role::Admin->value)->count());
    }

    public function test_the_last_active_admin_cannot_be_deactivated(): void
    {
        $lastAdmin = User::factory()->admin()->create();
        $actor = User::factory()->admin()->inactive()->create();

        $this->expectException(ValidationException::class);

        app(SetUserActive::class)->execute($actor, $lastAdmin, false);
    }

    public function test_an_actor_cannot_deactivate_themselves(): void
    {
        $actor = User::factory()->admin()->create();
        User::factory()->admin()->create();

        $this->expectException(ValidationException::class);

        app(SetUserActive::class)->execute($actor, $actor, false);
    }

    public function test_deactivating_revokes_sessions_only_with_the_database_driver(): void
    {
        $user = User::factory()->customer()->create();
        $actor = User::factory()->admin()->create();
        DB::table('sessions')->insert(['id' => 's1', 'user_id' => $user->id, 'payload' => '', 'last_activity' => time()]);

        config(['session.driver' => 'array']);
        app(SetUserActive::class)->execute($actor, $user, false);
        $this->assertSame(1, DB::table('sessions')->where('user_id', $user->id)->count());

        config(['session.driver' => 'database']);
        app(SetUserActive::class)->execute($actor, $user->refresh(), true);
        app(SetUserActive::class)->execute($actor, $user->refresh(), false);
        $this->assertSame(0, DB::table('sessions')->where('user_id', $user->id)->count());
    }

    public function test_a_password_reset_can_keep_one_session(): void
    {
        config(['session.driver' => 'database']);
        $user = User::factory()->admin()->create();
        DB::table('sessions')->insert([
            ['id' => 'keep', 'user_id' => $user->id, 'payload' => '', 'last_activity' => time()],
            ['id' => 'drop', 'user_id' => $user->id, 'payload' => '', 'last_activity' => time()],
        ]);

        app(ResetUserPassword::class)->execute($user, 'Fresh1234', 'keep');

        $this->assertSame(['keep'], DB::table('sessions')->where('user_id', $user->id)->pluck('id')->all());
    }
}
