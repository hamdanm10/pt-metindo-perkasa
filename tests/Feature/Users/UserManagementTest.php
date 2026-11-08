<?php

declare(strict_types=1);

namespace Tests\Feature\Users;

use App\Enums\Role;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\SetDashboardLocale;
use App\Livewire\Users\Create;
use App\Livewire\Users\Edit;
use App\Livewire\Users\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Livewire\Mechanisms\PersistentMiddleware\PersistentMiddleware;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_only_admin_can_open_the_account_pages(): void
    {
        $target = User::factory()->customer()->create();

        foreach (['/app/users', '/app/users/create', "/app/users/{$target->id}/edit"] as $url) {
            $this->actingAs($this->admin())->get($url)->assertOk();
            $this->actingAs(User::factory()->staff()->create())->get($url)->assertForbidden();
            $this->actingAs(User::factory()->customer()->create())->get($url)->assertForbidden();
        }
    }

    public function test_guests_are_sent_to_the_login(): void
    {
        $this->get('/app/users')->assertRedirect('/app/login');
    }

    public function test_the_accounts_menu_item_is_only_for_admin(): void
    {
        $this->actingAs($this->admin())->get('/app/dashboard')->assertSee('href="'.url('/app/users').'"', false);
        $this->actingAs(User::factory()->staff()->create())->get('/app/dashboard')->assertDontSee('href="'.url('/app/users').'"', false);
    }

    public function test_livewire_actions_are_refused_for_other_roles(): void
    {
        $target = User::factory()->customer()->create();

        Livewire::actingAs(User::factory()->staff()->create())->test(Index::class)->assertForbidden();
        Livewire::actingAs(User::factory()->staff()->create())->test(Create::class)->assertForbidden();
        Livewire::actingAs(User::factory()->staff()->create())->test(Edit::class, ['user' => $target])->assertForbidden();
    }

    public function test_the_role_and_locale_middleware_is_persistent_for_livewire_requests(): void
    {
        $persistent = app(PersistentMiddleware::class)->getPersistentMiddleware();

        $this->assertContains(EnsureRole::class, $persistent);
        $this->assertContains(SetDashboardLocale::class, $persistent);
    }

    public function test_the_forms_have_no_language_field_and_editing_keeps_the_language(): void
    {
        $user = User::factory()->customer()->create(['locale' => 'id']);

        $this->actingAs($this->admin())->get('/app/users/create')->assertDontSee('form-locale', false);
        $this->get("/app/users/{$user->id}/edit")->assertDontSee('form-locale', false);

        Livewire::actingAs($this->admin())->test(Edit::class, ['user' => $user])->set('form.name', 'Renamed')->call('save')->assertHasNoErrors();

        $this->assertSame('id', $user->refresh()->locale);
    }

    public function test_the_list_searches_and_filters(): void
    {
        User::factory()->staff()->create(['name' => 'Budi Santoso', 'email' => 'budi@example.test']);
        User::factory()->customer()->create(['name' => 'Citra', 'email' => 'citra@example.test']);
        User::factory()->customer()->inactive()->create(['name' => 'Dewi', 'email' => 'dewi@example.test']);

        Livewire::actingAs($this->admin())->test(Index::class)
            ->set('search', 'BUDI')->assertSee('Budi Santoso')->assertDontSee('Citra')
            ->set('search', 'citra@EXAMPLE')->assertSee('Citra')->assertDontSee('Budi Santoso')
            ->set('search', '')->set('role', 'customer')->assertSee('Citra')->assertSee('Dewi')->assertDontSee('Budi Santoso')
            ->set('status', 'inactive')->assertSee('Dewi')->assertDontSee('Citra')
            ->set('role', '')->set('status', 'active')->assertDontSee('Dewi');
    }

    public function test_the_list_is_paginated_and_does_not_add_queries_per_row(): void
    {
        $admin = $this->admin();
        User::factory()->count(4)->create();

        DB::enableQueryLog();
        Livewire::actingAs($admin)->test(Index::class);
        $few = count(DB::getQueryLog());

        User::factory()->count(25)->create();

        DB::flushQueryLog();
        $component = Livewire::actingAs($admin)->test(Index::class);
        $many = count(DB::getQueryLog());

        $this->assertSame($few, $many);
        $this->assertCount(15, $component->viewData('users')->items());
        $this->assertSame(30, $component->viewData('users')->total());
    }

    public function test_admin_creates_an_account(): void
    {
        Livewire::actingAs($this->admin())->test(Create::class)
            ->set('form.name', 'New Staff')
            ->set('form.email', ' New.Staff@Example.Test ')
            ->set('form.role', 'staff')
            ->set('form.password', 'Secret123')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect('/app/users');

        $user = User::query()->where('email', 'new.staff@example.test')->firstOrFail();

        $this->assertSame(Role::Staff, $user->role);
        $this->assertSame('en', $user->locale);
        $this->assertTrue($user->is_active);
        $this->assertTrue(Hash::check('Secret123', $user->password));
    }

    public function test_creating_rejects_bad_input(): void
    {
        User::factory()->create(['email' => 'taken@example.test']);

        Livewire::actingAs($this->admin())->test(Create::class)
            ->set('form.name', '')
            ->set('form.email', 'TAKEN@example.test')
            ->set('form.role', 'superuser')
            ->set('form.password', 'weak')
            ->call('save')
            ->assertHasErrors(['form.name', 'form.email', 'form.role', 'form.password']);

        $this->assertSame(1, User::query()->count() - 1);
    }

    public function test_admin_edits_an_account_and_may_keep_its_own_email(): void
    {
        $user = User::factory()->customer()->create(['email' => 'old@example.test']);

        Livewire::actingAs($this->admin())->test(Edit::class, ['user' => $user])
            ->set('form.name', 'Renamed')
            ->set('form.role', 'staff')
            ->call('save')
            ->assertHasNoErrors();

        $user->refresh();

        $this->assertSame('Renamed', $user->name);
        $this->assertSame(Role::Staff, $user->role);
        $this->assertSame('old@example.test', $user->email);
    }

    public function test_an_admin_cannot_remove_their_own_admin_role(): void
    {
        $admin = $this->admin();
        User::factory()->admin()->create();

        Livewire::actingAs($admin)->test(Edit::class, ['user' => $admin])
            ->set('form.role', 'staff')
            ->call('save')
            ->assertHasErrors(['form.role']);

        $this->assertSame(Role::Admin, $admin->refresh()->role);
    }

    public function test_resetting_a_password_replaces_it_and_ends_other_sessions(): void
    {
        config(['session.driver' => 'database']);
        $user = User::factory()->staff()->create();
        DB::table('sessions')->insert(['id' => 'abc', 'user_id' => $user->id, 'payload' => '', 'last_activity' => time()]);

        Livewire::actingAs($this->admin())->test(Edit::class, ['user' => $user])
            ->set('newPassword', 'weak')->call('resetPassword')->assertHasErrors(['newPassword'])
            ->set('newPassword', 'Fresh1234')->call('resetPassword')->assertHasNoErrors()->assertSet('newPassword', '');

        $this->assertTrue(Hash::check('Fresh1234', $user->refresh()->password));
        $this->assertFalse(Hash::check('password', $user->password));
        $this->assertSame(0, DB::table('sessions')->where('user_id', $user->id)->count());
    }

    public function test_deactivating_and_activating_an_account(): void
    {
        config(['session.driver' => 'database']);
        $user = User::factory()->customer()->create();
        DB::table('sessions')->insert(['id' => 'abc', 'user_id' => $user->id, 'payload' => '', 'last_activity' => time()]);

        Livewire::actingAs($this->admin())->test(Edit::class, ['user' => $user])
            ->call('toggleActive')->assertHasNoErrors();

        $this->assertFalse($user->refresh()->is_active);
        $this->assertSame(0, DB::table('sessions')->where('user_id', $user->id)->count());

        Auth::logout();

        $this->post('/customer/login', ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');

        Livewire::actingAs($this->admin())->test(Edit::class, ['user' => $user])->call('toggleActive');

        $this->assertTrue($user->refresh()->is_active);
    }

    public function test_an_admin_cannot_deactivate_themselves(): void
    {
        $admin = $this->admin();

        Livewire::actingAs($admin)->test(Edit::class, ['user' => $admin])->call('toggleActive')->assertForbidden();

        $this->assertTrue($admin->refresh()->is_active);
    }

    public function test_pages_follow_the_dashboard_language(): void
    {
        $admin = User::factory()->admin()->create(['locale' => 'id']);

        $this->actingAs($admin)->get('/app/users')->assertSee(__('users.title', [], 'id'))->assertSee(__('users.create', [], 'id'));
    }

    public function test_translation_keys_match_in_both_locales(): void
    {
        $keys = fn (string $code): array => array_keys(Arr::dot(require lang_path("{$code}/users.php")));

        $this->assertEqualsCanonicalizing($keys('en'), $keys('id'));
    }
}
