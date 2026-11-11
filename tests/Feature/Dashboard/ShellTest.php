<?php

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShellTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_greets_each_role_by_name_and_shows_the_role_and_an_empty_state(): void
    {
        foreach (['admin' => '/app/dashboard', 'staff' => '/app/dashboard', 'customer' => '/customer/dashboard'] as $role => $url) {
            $user = User::factory()->{$role}()->create(['name' => "Tester {$role}"]);

            $this->actingAs($user)->get($url)
                ->assertOk()
                ->assertSee(__('dashboard.home.greeting', ['name' => "Tester {$role}"]))
                ->assertSee(__("dashboard.roles.{$role}"))
                ->assertSee(__('dashboard.home.empty_text'));
        }
    }

    public function test_the_top_bar_has_a_user_dropdown_with_the_account_details_and_sign_out(): void
    {
        $user = User::factory()->staff()->create(['name' => 'Rina Wijaya', 'email' => 'rina@example.test']);

        $this->actingAs($user)->get('/app/dashboard')
            ->assertSee('aria-label="'.__('dashboard.account_menu').'"', false)
            ->assertSee('rina@example.test')
            ->assertSee('RW')
            ->assertSee('action="'.url('/app/logout').'"', false)
            ->assertSee(__('auth.logout'));
    }

    public function test_the_top_bar_has_a_language_dropdown_listing_both_languages(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/app/dashboard')
            ->assertSee('aria-label="'.__('dashboard.language').'"', false)
            ->assertSee('English')
            ->assertSee('Bahasa Indonesia')
            ->assertSee('action="'.url('/app/locale').'"', false)
            ->assertSee('aria-current="true"', false);
    }

    public function test_the_sidebar_is_a_drawer_on_small_screens_with_a_toggle_on_the_left(): void
    {
        $response = $this->actingAs(User::factory()->admin()->create())->get('/app/dashboard')
            ->assertSee('id="sidebar"', false)
            ->assertSee('data-sidebar-toggle', false)
            ->assertSee('aria-controls="sidebar"', false)
            ->assertSee('aria-expanded="false"', false)
            ->assertSee('aria-label="'.__('dashboard.open_menu').'"', false)
            ->assertSee('aria-label="'.__('dashboard.close_menu').'"', false)
            ->assertSee('id="sidebar-backdrop"', false);

        $html = (string) $response->getContent();

        $this->assertLessThan(strpos($html, 'aria-label="'.__('dashboard.language').'"'), strpos($html, 'data-sidebar-toggle'));
        $this->assertStringNotContainsString('<details class="relative lg:hidden"', $html);
    }

    public function test_the_shell_uses_the_landing_style_brand_lockup_menu_label_and_dark_dropdowns(): void
    {
        $html = (string) $this->actingAs(User::factory()->admin()->create())->get('/app/dashboard')->getContent();

        $this->assertStringContainsString('brand-lockup__name', $html);
        $this->assertStringContainsString('dd-panel', $html);
        $this->assertSame(2, substr_count($html, 'dd-summary'));
        $this->assertStringContainsString('uppercase', $html);
        $this->assertStringNotContainsString('Bricolage', $html);
    }

    public function test_the_navbar_holds_the_logo_and_both_dropdowns_above_the_sidebar(): void
    {
        $html = (string) $this->actingAs(User::factory()->admin()->create())->get('/app/dashboard')->getContent();

        $navbarEnd = (int) strpos($html, '</header>');
        $this->assertGreaterThan(0, $navbarEnd);

        $navbar = substr($html, (int) strpos($html, '<header'), $navbarEnd);
        $this->assertStringContainsString('brand-lockup', $navbar);
        $this->assertStringContainsString('aria-label="'.__('dashboard.language').'"', $navbar);
        $this->assertStringContainsString('aria-label="'.__('dashboard.account_menu').'"', $navbar);
        $this->assertStringContainsString('data-sidebar-toggle', $navbar);

        $this->assertLessThan(strpos($html, 'id="sidebar"'), $navbarEnd);
        $this->assertStringNotContainsString('brand-lockup', substr($html, (int) strpos($html, 'id="sidebar"')));
    }

    public function test_the_content_sits_in_a_centered_container(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/app/dashboard')
            ->assertSee('mx-auto w-full max-w-6xl', false);
    }

    public function test_the_menu_lists_only_the_area_home_and_marks_it_current(): void
    {
        $this->actingAs(User::factory()->staff()->create())->get('/app/dashboard')
            ->assertSee('href="'.url('/app/dashboard').'"', false)
            ->assertDontSee('href="'.url('/customer/dashboard').'"', false)
            ->assertSee('aria-current="page"', false);

        $this->actingAs(User::factory()->customer()->create())->get('/customer/dashboard')
            ->assertSee('href="'.url('/customer/dashboard').'"', false)
            ->assertDontSee('href="'.url('/app/dashboard').'"', false);
    }

    public function test_dashboard_pages_are_not_indexed_and_declare_the_language(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/app/dashboard')
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false)
            ->assertSee('<html lang="en" dir="ltr">', false);
    }

    public function test_a_signed_in_user_can_switch_the_language_and_it_is_remembered(): void
    {
        $user = User::factory()->staff()->create();

        $this->actingAs($user)->from('/app/dashboard')->post('/app/locale', ['locale' => 'id'])
            ->assertRedirect('/app/dashboard');

        $this->assertSame('id', $user->refresh()->locale);
        $this->assertSame('id', session('locale'));

        $this->get('/app/dashboard')
            ->assertSee('<html lang="id" dir="ltr">', false)
            ->assertSee(__('dashboard.home.greeting', ['name' => $user->name], 'id'));
    }

    public function test_a_guest_can_switch_the_language_on_the_login_page(): void
    {
        $this->from('/app/login')->post('/app/locale', ['locale' => 'id'])->assertRedirect('/app/login');

        $this->assertSame('id', session('locale'));

        $this->get('/app/login')->assertSee(__('auth.staff_title', [], 'id'));
        $this->assertSame(0, User::query()->count());
    }

    public function test_a_language_picked_on_the_login_page_becomes_the_account_language(): void
    {
        $user = User::factory()->staff()->create(['locale' => 'en']);

        $this->withSession(['locale' => 'id'])->post('/app/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect('/app/dashboard');

        $this->assertSame('id', $user->refresh()->locale);
        $this->get('/app/dashboard')->assertSee(__('dashboard.home.greeting', ['name' => $user->name], 'id'));
    }

    public function test_signing_in_without_a_chosen_language_keeps_the_stored_one(): void
    {
        $user = User::factory()->staff()->create(['locale' => 'id']);

        $this->post('/app/login', ['email' => $user->email, 'password' => 'password']);

        $this->assertSame('id', $user->refresh()->locale);
    }

    public function test_login_pages_offer_the_language_switcher(): void
    {
        $this->get('/app/login')->assertSee('name="locale"', false)->assertSee('action="'.url('/app/locale').'"', false);
        $this->get('/customer/login')->assertSee('action="'.url('/customer/locale').'"', false);
    }

    public function test_locales_outside_the_dashboard_list_are_rejected(): void
    {
        $user = User::factory()->staff()->create(['locale' => 'en']);

        foreach (['zh', 'ar', 'xx', ''] as $locale) {
            $this->actingAs($user)->from('/app/dashboard')->post('/app/locale', ['locale' => $locale])
                ->assertSessionHasErrors('locale');
        }

        $this->assertSame('en', $user->refresh()->locale);
        $this->assertNull(session('locale'));
    }

    public function test_the_redirect_back_stays_on_this_site(): void
    {
        $user = User::factory()->customer()->create();

        $this->actingAs($user)->from('https://elsewhere.example/page')->post('/customer/locale', ['locale' => 'id'])
            ->assertRedirect(route('customer.dashboard'));

        $this->post('/app/locale', ['locale' => 'id']);

        auth()->logout();

        $this->from('https://elsewhere.example/page')->post('/customer/locale', ['locale' => 'id'])
            ->assertRedirect(route('customer.login'));
    }
}
