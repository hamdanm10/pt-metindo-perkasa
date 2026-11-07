<?php

declare(strict_types=1);

namespace Tests\Feature\Landing;

use Illuminate\Support\Arr;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_root_redirects_to_the_default_locale(): void
    {
        $this->get('/')->assertRedirect('/en');
    }

    public function test_each_landing_locale_renders_with_its_lang_and_direction(): void
    {
        foreach (config('locales.landing') as $code) {
            $locale = config("locales.locales.{$code}");

            $this->get("/{$code}")
                ->assertOk()
                ->assertSee('<html lang="'.$locale['html_lang'].'" dir="'.$locale['dir'].'">', false);
        }
    }

    public function test_only_arabic_is_right_to_left(): void
    {
        $rtl = collect(config('locales.landing'))
            ->filter(fn (string $code): bool => config("locales.locales.{$code}.dir") === 'rtl')
            ->values()
            ->all();

        $this->assertSame(['ar'], $rtl);
    }

    public function test_unknown_locale_returns_not_found(): void
    {
        $this->get('/xx')->assertNotFound();
        $this->get('/app')->assertNotFound();
        $this->get('/customer')->assertNotFound();
    }

    public function test_page_declares_canonical_and_alternates_for_every_locale(): void
    {
        $response = $this->get('/id')->assertOk();

        $response->assertSee('<link rel="canonical" href="'.url('/id').'">', false);

        foreach (config('locales.landing') as $code) {
            $hreflang = config("locales.locales.{$code}.html_lang");

            $response->assertSee('<link rel="alternate" hreflang="'.$hreflang.'" href="'.url("/{$code}").'">', false);
        }

        $response->assertSee('<link rel="alternate" hreflang="x-default" href="'.url('/en').'">', false);
    }

    public function test_language_switcher_links_to_the_same_page_in_other_locales(): void
    {
        $response = $this->get('/ja')->assertOk();

        foreach (config('locales.landing') as $code) {
            $response->assertSee('href="'.url("/{$code}").'"', false);
        }
    }

    public function test_navigation_text_follows_the_locale(): void
    {
        $this->get('/en')->assertSee(__('landing.nav.home', [], 'en'));
        $this->get('/id')->assertSee(__('landing.nav.home', [], 'id'));
        $this->get('/ar')->assertSee(__('landing.nav.home', [], 'ar'));
    }

    public function test_landing_translation_keys_match_in_every_locale(): void
    {
        $keys = fn (string $code): array => array_keys(
            Arr::dot(require lang_path("{$code}/landing.php"))
        );

        $expected = $keys('en');

        foreach (config('locales.landing') as $code) {
            $this->assertEqualsCanonicalizing($expected, $keys($code), "Keys differ in {$code}");
        }
    }
}
