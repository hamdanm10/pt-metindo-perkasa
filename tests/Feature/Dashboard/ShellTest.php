<?php

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShellTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_greets_each_role_by_name_and_shows_the_role_and_an_empty_state(): void
    {
        foreach (['admin' => '/app/dashboard', 'staff' => '/app/dashboard', 'customer' => '/portal/dashboard', 'distributor' => '/portal/dashboard'] as $role => $url) {
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

    public function test_the_logo_is_in_the_full_height_sidebar_and_the_navbar_holds_both_dropdowns(): void
    {
        $html = (string) $this->actingAs(User::factory()->admin()->create())->get('/app/dashboard')->getContent();

        $sidebarStart = (int) strpos($html, 'id="sidebar"');
        $sidebarEnd = (int) strpos($html, '</aside>');
        $sidebar = substr($html, $sidebarStart, $sidebarEnd - $sidebarStart);

        $this->assertStringContainsString('brand-lockup', $sidebar);
        $this->assertStringContainsString('inset-y-0', substr($html, $sidebarStart - 80, 120));

        $navbar = substr($html, (int) strpos($html, '<header'), (int) strpos($html, '</header>') - (int) strpos($html, '<header'));
        $this->assertStringNotContainsString('brand-lockup', $navbar);
        $this->assertStringContainsString('aria-label="'.__('dashboard.language').'"', $navbar);
        $this->assertStringContainsString('aria-label="'.__('dashboard.account_menu').'"', $navbar);
        $this->assertStringContainsString('data-sidebar-toggle', $navbar);
        $this->assertGreaterThan($sidebarEnd, (int) strpos($html, '<header'));
    }

    public function test_the_sidebar_gathers_related_items_into_icon_dropdowns_and_opens_the_active_one(): void
    {
        $html = (string) $this->actingAs(User::factory()->admin()->create())->get('/app/products')->getContent();

        $this->assertSame(4, substr_count($html, 'data-nav-group'));

        $opens = fn (string $label): bool => preg_match('/<details[^>]*data-nav-group[^>]* open\s*>\s*<summary[^>]*>\s*<svg[^>]*>(?:(?!<\/svg>).)*<\/svg>\s*<span[^>]*>'.preg_quote($label, '/').'<\/span>/s', $html) === 1;

        $this->assertTrue($opens(__('dashboard.nav.parents.catalog')));
        $this->assertFalse($opens(__('dashboard.nav.parents.clients')));
        $this->assertFalse($opens(__('dashboard.nav.parents.qa')));
    }

    public function test_the_sidebar_groups_the_menu_under_labels_with_an_icon_on_each_item(): void
    {
        $html = (string) $this->actingAs(User::factory()->admin()->create())->get('/app/dashboard')->getContent();

        $position = fn (string $text): int => preg_match('/>\s*'.preg_quote($text, '/').'\s*(<\/p>|<svg)/', $html, $m, PREG_OFFSET_CAPTURE) === 1 ? $m[0][1] : 0;

        $this->assertGreaterThan(0, $position(__('dashboard.groups.menu')));
        $this->assertGreaterThan($position(__('dashboard.groups.menu')), $position(__('dashboard.groups.content')));
        $this->assertGreaterThan($position(__('dashboard.groups.content')), $position(__('dashboard.groups.users')));
        $this->assertLessThan($position(__('dashboard.groups.content')), (int) strpos($html, __('dashboard.nav.home')));
        $this->assertGreaterThan($position(__('dashboard.groups.content')), (int) strpos($html, __('dashboard.nav.articles')));
        $this->assertGreaterThan($position(__('dashboard.groups.users')), (int) strpos($html, __('dashboard.nav.staff')));
        $this->assertSame(7, substr_count($html, 'size-5 shrink-0'));

        $staff = (string) $this->actingAs(User::factory()->staff()->create())->get('/app/dashboard')->getContent();
        $this->assertDoesNotMatchRegularExpression('/>\s*'.preg_quote(__('dashboard.groups.content'), '/').'\s*(<\/p>|<svg)/', $staff);
        $this->assertDoesNotMatchRegularExpression('/>\s*'.preg_quote(__('dashboard.groups.users'), '/').'\s*(<\/p>|<svg)/', $staff);
    }

    public function test_notifications_are_toasts_at_the_top_right_not_inline_alerts(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertStringNotContainsString('data-toast', (string) $this->actingAs($admin)->get('/app/staff')->getContent());

        $html = (string) $this->withSession(['status' => 'Account created.'])->get('/app/staff')->getContent();

        $this->assertStringContainsString('data-toast', $html);
        $this->assertStringContainsString('fixed end-4 top-20', $html);
        $this->assertStringContainsString('Account created.', $html);
        $this->assertStringContainsString('data-toast-close', $html);
        $this->assertStringContainsString('aria-label="'.__('dashboard.close_notification').'"', $html);
        $this->assertStringNotContainsString('mb-6 flex items-center gap-3 rounded-lg bg-ink px-5 py-4', $html);
    }

    public function test_an_error_notification_is_an_alert_toast(): void
    {
        $html = (string) $this->withViewErrors(['active' => 'You cannot deactivate your own account.'])->blade('<x-dashboard.flash />');

        $this->assertStringContainsString('data-toast', $html);
        $this->assertStringContainsString('role="alert"', $html);
        $this->assertStringContainsString('data-timeout="8000"', $html);
        $this->assertStringContainsString('You cannot deactivate your own account.', $html);
    }

    public function test_destructive_actions_ask_with_the_site_dialog_not_the_browser_confirm(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();

        $this->actingAs($admin)->get('/app/dashboard')
            ->assertSee('id="confirm-dialog"', false)
            ->assertSee('data-confirm-ok', false)
            ->assertSee(__('dashboard.cancel'));

        $this->get("/app/staff/{$staff->id}/edit")
            ->assertOk()
            ->assertSee('data-confirm="'.__('users.confirm_deactivate').'"', false)
            ->assertDontSee('wire:confirm', false);
    }

    public function test_list_rows_are_centered_vertically(): void
    {
        Product::factory()->create();
        Service::factory()->create();
        ArticleTranslation::factory()->create(['article_id' => Article::factory()->create()->id]);

        foreach (['/app/products', '/app/services', '/app/articles'] as $url) {
            $this->actingAs(User::factory()->admin()->create())->get($url)
                ->assertOk()
                ->assertSee('class="align-middle"', false)
                ->assertDontSee('align-top', false);
        }
    }

    public function test_list_cells_are_cut_with_an_ellipsis_instead_of_wrapping(): void
    {
        $css = (string) file_get_contents(resource_path('css/dashboard.css'));
        $this->assertMatchesRegularExpression('/\.data-table th,\s*\.data-table td\s*\{\s*white-space: nowrap;/', $css);
        $this->assertMatchesRegularExpression('/\.cell-truncate\s*\{[^}]*text-overflow: ellipsis;[^}]*white-space: nowrap;/', $css);

        $long = str_repeat('Very long text ', 15);
        Product::factory()->create(['description' => $long]);
        Service::factory()->create(['description' => $long]);
        Inquiry::factory()->create(['question' => $long]);
        ArticleTranslation::factory()->create(['article_id' => Article::factory()->create()->id, 'title' => $long]);
        User::factory()->staff()->create(['name' => $long]);

        foreach (['/app/products', '/app/services', '/app/customer-inquiries', '/app/articles', '/app/staff'] as $url) {
            $html = (string) $this->actingAs(User::factory()->admin()->create())->get($url)->assertOk()->getContent();
            $table = substr($html, (int) strpos($html, '<table'), (int) strpos($html, '</table>') - (int) strpos($html, '<table'));

            $this->assertStringContainsString('cell-truncate', $table, $url);
            $this->assertStringNotContainsString('flex-wrap', $table, $url);
        }
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
            ->assertDontSee('href="'.url('/portal/dashboard').'"', false)
            ->assertSee('aria-current="page"', false);

        $this->actingAs(User::factory()->customer()->create())->get('/portal/dashboard')
            ->assertSee('href="'.url('/portal/dashboard').'"', false)
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
        $this->get('/portal/login')->assertSee('action="'.url('/portal/locale').'"', false);
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

        $this->actingAs($user)->from('https://elsewhere.example/page')->post('/portal/locale', ['locale' => 'id'])
            ->assertRedirect(route('portal.dashboard'));

        $this->post('/app/locale', ['locale' => 'id']);

        auth()->logout();

        $this->from('https://elsewhere.example/page')->post('/portal/locale', ['locale' => 'id'])
            ->assertRedirect(route('portal.login'));
    }
}
