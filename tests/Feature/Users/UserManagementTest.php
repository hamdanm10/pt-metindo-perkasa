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
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    /**
     * @return array<string, array{string, string, Role}>
     */
    public static function kinds(): array
    {
        return [
            'staff' => ['staff', 'staff', Role::Staff],
            'customers' => ['customers', 'customer', Role::Customer],
        ];
    }

    #[DataProvider('kinds')]
    public function test_only_admin_can_open_the_account_pages(string $path, string $kind, Role $role): void
    {
        $target = User::factory()->create(['role' => $role]);

        foreach (["/app/{$path}", "/app/{$path}/create", "/app/{$path}/{$target->id}/edit"] as $url) {
            $this->actingAs($this->admin())->get($url)->assertOk();
            $this->actingAs(User::factory()->staff()->create())->get($url)->assertForbidden();
            $this->actingAs(User::factory()->customer()->create())->get($url)->assertForbidden();
        }
    }

    public function test_the_old_accounts_address_is_gone(): void
    {
        $this->actingAs($this->admin())->get('/app/users')->assertNotFound();
    }

    public function test_guests_are_sent_to_the_login(): void
    {
        $this->get('/app/staff')->assertRedirect('/app/login');
        $this->get('/app/customers')->assertRedirect('/app/login');
    }

    public function test_the_menu_has_staff_and_customers_under_user_management_for_admin_only(): void
    {
        $this->actingAs($this->admin())->get('/app/dashboard')
            ->assertSee('href="'.url('/app/staff').'"', false)
            ->assertSee('href="'.url('/app/customers').'"', false)
            ->assertDontSee('href="'.url('/app/users').'"', false);

        $this->actingAs(User::factory()->staff()->create())->get('/app/dashboard')
            ->assertDontSee('href="'.url('/app/staff').'"', false)
            ->assertDontSee('href="'.url('/app/customers').'"', false);
    }

    public function test_livewire_actions_are_refused_for_other_roles(): void
    {
        $target = User::factory()->customer()->create();

        Livewire::actingAs(User::factory()->staff()->create())->test(Index::class, ['kind' => 'customer'])->assertForbidden();
        Livewire::actingAs(User::factory()->staff()->create())->test(Create::class, ['kind' => 'customer'])->assertForbidden();
        Livewire::actingAs(User::factory()->staff()->create())->test(Edit::class, ['user' => $target, 'kind' => 'customer'])->assertForbidden();
    }

    public function test_the_role_and_locale_middleware_is_persistent_for_livewire_requests(): void
    {
        $persistent = app(PersistentMiddleware::class)->getPersistentMiddleware();

        $this->assertContains(EnsureRole::class, $persistent);
        $this->assertContains(SetDashboardLocale::class, $persistent);
    }

    public function test_the_forms_have_no_role_or_language_field_and_editing_keeps_the_language(): void
    {
        $user = User::factory()->customer()->create(['locale' => 'id']);

        foreach (['/app/customers/create', "/app/customers/{$user->id}/edit"] as $url) {
            $this->actingAs($this->admin())->get($url)
                ->assertDontSee('form-locale', false)
                ->assertDontSee('form-role', false);
        }

        Livewire::actingAs($this->admin())->test(Edit::class, ['user' => $user, 'kind' => 'customer'])->set('form.name', 'Renamed')->call('save')->assertHasNoErrors();

        $this->assertSame('id', $user->refresh()->locale);
    }

    public function test_each_list_shows_only_its_own_role_and_never_the_admin(): void
    {
        $admin = $this->admin();
        User::factory()->staff()->create(['name' => 'Budi Santoso']);
        User::factory()->customer()->create(['name' => 'Citra Lestari']);

        Livewire::actingAs($admin)->test(Index::class, ['kind' => 'staff'])
            ->assertSee('Budi Santoso')->assertDontSee('Citra Lestari')->assertDontSee($admin->email);

        Livewire::actingAs($admin)->test(Index::class, ['kind' => 'customer'])
            ->assertSee('Citra Lestari')->assertDontSee('Budi Santoso')->assertDontSee($admin->email);
    }

    public function test_the_pages_use_the_titles_of_their_kind(): void
    {
        $heading = fn (string $url): string => preg_match('/<h1[^>]*>\s*(.*?)\s*<\/h1>/s', (string) $this->get($url)->getContent(), $found) === 1 ? $found[1] : '';

        $this->actingAs($this->admin());

        $this->assertSame(__('users.kinds.staff.title'), $heading('/app/staff'));
        $this->assertSame(__('users.kinds.customer.title'), $heading('/app/customers'));
        $this->assertSame(__('users.kinds.staff.create_title'), $heading('/app/staff/create'));
        $this->assertSame(__('users.kinds.customer.create_title'), $heading('/app/customers/create'));

        $this->get('/app/staff')->assertSee(__('users.kinds.staff.create'));
        $this->get('/app/customers')->assertSee(__('users.kinds.customer.create'));
    }

    public function test_an_unknown_kind_is_not_found(): void
    {
        Livewire::actingAs($this->admin())->test(Index::class, ['kind' => 'admin'])->assertNotFound();
        Livewire::actingAs($this->admin())->test(Index::class, ['kind' => 'nobody'])->assertNotFound();
    }

    public function test_the_admin_account_cannot_be_opened_from_the_account_pages(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get("/app/staff/{$admin->id}/edit")->assertNotFound();
        $this->get("/app/customers/{$admin->id}/edit")->assertNotFound();
    }

    public function test_a_user_of_the_other_kind_is_not_found_on_the_edit_page(): void
    {
        $customer = User::factory()->customer()->create();
        $staff = User::factory()->staff()->create();

        $this->actingAs($this->admin())->get("/app/staff/{$customer->id}/edit")->assertNotFound();
        $this->get("/app/customers/{$staff->id}/edit")->assertNotFound();
    }

    public function test_the_policy_never_allows_changing_the_admin(): void
    {
        $admin = $this->admin();
        $other = $this->admin();

        $this->assertFalse($admin->can('update', $other));
        $this->assertFalse($admin->can('resetPassword', $other));
        $this->assertFalse($admin->can('setActive', $other));
    }

    public function test_the_password_fields_have_a_show_and_hide_button_and_other_fields_do_not(): void
    {
        $user = User::factory()->staff()->create();

        $create = $this->actingAs($this->admin())->get('/app/staff/create')
            ->assertSee('data-password-toggle="form-password"', false)
            ->assertSee(__('auth.show_password'))
            ->assertSee(__('auth.hide_password'));

        $this->assertSame(1, substr_count((string) $create->getContent(), 'data-password-toggle='));

        $this->get("/app/staff/{$user->id}/edit")->assertSee('data-password-toggle="newPassword"', false);
    }

    public function test_required_fields_get_a_red_asterisk_and_optional_ones_do_not(): void
    {
        $html = (string) $this->actingAs($this->admin())->get('/app/staff/create')->getContent();

        $this->assertSame(3, substr_count($html, 'text-brand-600" aria-hidden="true"> *</span>'));

        $filters = (string) $this->get('/app/staff')->getContent();
        $this->assertStringNotContainsString('aria-hidden="true"> *</span>', $filters);
    }

    public function test_the_list_searches_and_filters_by_status(): void
    {
        User::factory()->customer()->create(['name' => 'Citra', 'email' => 'citra@example.test']);
        User::factory()->customer()->create(['name' => 'Budi', 'email' => 'budi@example.test']);
        User::factory()->customer()->inactive()->create(['name' => 'Dewi', 'email' => 'dewi@example.test']);

        Livewire::actingAs($this->admin())->test(Index::class, ['kind' => 'customer'])
            ->set('search', 'BUDI')->assertSee('Budi')->assertDontSee('Citra')
            ->set('search', 'citra@EXAMPLE')->assertSee('Citra')->assertDontSee('Budi')
            ->set('search', '')->set('status', 'inactive')->assertSee('Dewi')->assertDontSee('Citra')
            ->set('status', 'active')->assertDontSee('Dewi')->assertSee('Citra');
    }

    public function test_the_list_is_paginated_and_does_not_add_queries_per_row(): void
    {
        $admin = $this->admin();
        User::factory()->customer()->count(4)->create();

        DB::enableQueryLog();
        Livewire::actingAs($admin)->test(Index::class, ['kind' => 'customer']);
        $few = count(DB::getQueryLog());

        User::factory()->customer()->count(25)->create();

        DB::flushQueryLog();
        $component = Livewire::actingAs($admin)->test(Index::class, ['kind' => 'customer']);
        $many = count(DB::getQueryLog());

        $this->assertSame($few, $many);
        $this->assertCount(15, $component->viewData('users')->items());
        $this->assertSame(29, $component->viewData('users')->total());
    }

    public function test_the_table_scrolls_inside_its_own_wrapper(): void
    {
        $this->actingAs($this->admin())->get('/app/staff')->assertSee('relative overflow-x-auto', false);
    }

    #[DataProvider('kinds')]
    public function test_admin_creates_an_account_with_the_role_of_the_page(string $path, string $kind, Role $role): void
    {
        Livewire::actingAs($this->admin())->test(Create::class, ['kind' => $kind])
            ->set('form.name', 'New Person')
            ->set('form.email', ' New.Person@Example.Test ')
            ->set('form.password', 'Secret123')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect("/app/{$path}");

        $user = User::query()->where('email', 'new.person@example.test')->firstOrFail();

        $this->assertSame($role, $user->role);
        $this->assertSame('en', $user->locale);
        $this->assertTrue($user->is_active);
        $this->assertTrue(Hash::check('Secret123', $user->password));
    }

    #[DataProvider('kinds')]
    public function test_the_password_field_starts_with_the_default_password_and_saving_uses_what_is_in_the_field(string $path, string $kind, Role $role): void
    {
        $component = Livewire::actingAs($this->admin())->test(Create::class, ['kind' => $kind])
            ->assertSet('form.password', 'MetindoPerkasa2026');

        $component->set('form.name', 'Default Person')->set('form.email', 'default@example.test')->call('save')->assertHasNoErrors();

        $this->assertTrue(Hash::check('MetindoPerkasa2026', User::query()->where('email', 'default@example.test')->firstOrFail()->password));

        Livewire::actingAs($this->admin())->test(Create::class, ['kind' => $kind])
            ->set('form.name', 'Own Password')->set('form.email', 'own@example.test')->set('form.password', 'Another123')->call('save')->assertHasNoErrors();

        $this->assertTrue(Hash::check('Another123', User::query()->where('email', 'own@example.test')->firstOrFail()->password));
    }

    public function test_the_edit_page_does_not_prefill_a_password(): void
    {
        $user = User::factory()->staff()->create();

        Livewire::actingAs($this->admin())->test(Edit::class, ['user' => $user, 'kind' => 'staff'])->assertSet('newPassword', '');
    }

    public function test_the_server_still_rejects_a_weak_password_in_the_field(): void
    {
        Livewire::actingAs($this->admin())->test(Create::class, ['kind' => 'staff'])
            ->set('form.name', 'Weak')->set('form.email', 'weak@example.test')->set('form.password', 'weak')->call('save')
            ->assertHasErrors(['form.password']);
    }

    public function test_a_role_sent_from_the_browser_is_ignored_and_no_admin_can_be_created(): void
    {
        Livewire::actingAs($this->admin())->test(Create::class, ['kind' => 'staff'])
            ->set('form.name', 'Sneaky')
            ->set('form.email', 'sneaky@example.test')
            ->set('form.password', 'Secret123')
            ->set('form.role', 'admin')
            ->call('save');

        $this->assertSame(Role::Staff, User::query()->where('email', 'sneaky@example.test')->firstOrFail()->role);
        $this->assertSame(1, User::query()->where('role', Role::Admin->value)->count());
    }

    public function test_creating_rejects_bad_input(): void
    {
        User::factory()->create(['email' => 'taken@example.test']);

        Livewire::actingAs($this->admin())->test(Create::class, ['kind' => 'staff'])
            ->set('form.name', '')
            ->set('form.email', 'TAKEN@example.test')
            ->set('form.password', 'weak')
            ->call('save')
            ->assertHasErrors(['form.name', 'form.email', 'form.password']);

        $this->assertSame(0, User::query()->where('role', Role::Staff->value)->count());
    }

    public function test_admin_edits_an_account_and_may_keep_its_own_email_and_the_role_stays(): void
    {
        $user = User::factory()->customer()->create(['email' => 'old@example.test']);

        Livewire::actingAs($this->admin())->test(Edit::class, ['user' => $user, 'kind' => 'customer'])
            ->set('form.name', 'Renamed')
            ->call('save')
            ->assertHasNoErrors();

        $user->refresh();

        $this->assertSame('Renamed', $user->name);
        $this->assertSame(Role::Customer, $user->role);
        $this->assertSame('old@example.test', $user->email);
    }

    public function test_resetting_a_password_replaces_it_and_ends_other_sessions(): void
    {
        config(['session.driver' => 'database']);
        $user = User::factory()->staff()->create();
        DB::table('sessions')->insert(['id' => 'abc', 'user_id' => $user->id, 'payload' => '', 'last_activity' => time()]);

        Livewire::actingAs($this->admin())->test(Edit::class, ['user' => $user, 'kind' => 'staff'])
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

        Livewire::actingAs($this->admin())->test(Edit::class, ['user' => $user, 'kind' => 'customer'])
            ->call('toggleActive')->assertHasNoErrors();

        $this->assertFalse($user->refresh()->is_active);
        $this->assertSame(0, DB::table('sessions')->where('user_id', $user->id)->count());

        Auth::logout();

        $this->post('/customer/login', ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');

        Livewire::actingAs($this->admin())->test(Edit::class, ['user' => $user, 'kind' => 'customer'])->call('toggleActive');

        $this->assertTrue($user->refresh()->is_active);
    }

    public function test_pages_follow_the_dashboard_language(): void
    {
        $admin = User::factory()->admin()->create(['locale' => 'id']);

        $this->actingAs($admin)->get('/app/staff')->assertSee(__('users.kinds.staff.title', [], 'id'))->assertSee(__('users.kinds.staff.create', [], 'id'));
        $this->get('/app/customers')->assertSee(__('users.kinds.customer.title', [], 'id'));
    }

    public function test_translation_keys_match_in_both_locales(): void
    {
        $keys = fn (string $code): array => array_keys(Arr::dot(require lang_path("{$code}/users.php")));

        $this->assertEqualsCanonicalizing($keys('en'), $keys('id'));
    }
}
