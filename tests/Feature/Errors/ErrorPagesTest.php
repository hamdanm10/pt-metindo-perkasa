<?php

declare(strict_types=1);

namespace Tests\Feature\Errors;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_not_found_uses_the_url_locale(): void
    {
        $this->get('/id/nothing-here')
            ->assertNotFound()
            ->assertSee('lang="id"', false)
            ->assertSee(__('errors.404.title', [], 'id'))
            ->assertSee(url('/id'), false);
    }

    public function test_arabic_landing_error_is_right_to_left(): void
    {
        $this->get('/ar/nothing-here')
            ->assertNotFound()
            ->assertSee('dir="rtl"', false)
            ->assertSee(__('errors.404.title', [], 'ar'));
    }

    public function test_unknown_url_falls_back_to_english(): void
    {
        $this->get('/nothing-here')
            ->assertNotFound()
            ->assertSee(__('errors.404.title', [], 'en'));
    }

    public function test_dashboard_error_uses_the_user_locale_and_links_to_the_area_home(): void
    {
        $user = User::factory()->create(['role' => Role::Admin, 'locale' => 'id']);

        $this->actingAs($user)->get('/app/nothing-here')
            ->assertNotFound()
            ->assertSee(__('errors.404.title', [], 'id'))
            ->assertSee(route('app.dashboard'), false);
    }

    public function test_forbidden_page_is_branded_for_staff(): void
    {
        $staff = User::factory()->create(['role' => Role::Staff]);

        $this->actingAs($staff)->get('/app/staff')
            ->assertForbidden()
            ->assertSee(__('errors.403.title'));
    }

    public function test_dashboard_error_page_never_uses_a_landing_only_locale(): void
    {
        $this->withSession(['locale' => 'zh'])->get('/app/nothing-here')
            ->assertSee(__('errors.404.title', [], 'en'));
    }

    public function test_error_pages_use_the_login_style_layout_and_the_site_font(): void
    {
        $html = (string) $this->get('/en/nothing-here')->getContent();

        $this->assertStringContainsString('bg-ink', $html);
        $this->assertStringContainsString('bg-brand-600', $html);
        $this->assertStringContainsString('uppercase', $html);
        $this->assertStringNotContainsString('hero-wedge', $html);
        $this->assertStringNotContainsString('font-family:system-ui', $html);
    }

    public function test_error_pages_are_not_indexable(): void
    {
        $this->get('/en/nothing-here')->assertSee('noindex', false);
    }

    public function test_every_error_view_has_keys_in_all_locales(): void
    {
        foreach (['403', '404', '419', '429', '500', '503'] as $code) {
            foreach (config()->array('locales.landing') as $locale) {
                $this->assertNotSame("errors.{$code}.title", __("errors.{$code}.title", [], $locale));
                $this->assertNotSame("errors.{$code}.text", __("errors.{$code}.text", [], $locale));
            }
        }
    }
}
