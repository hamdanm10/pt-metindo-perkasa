<?php

declare(strict_types=1);

namespace Tests\Feature\Landing;

use App\Models\ArticleTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        config([
            'landing.sample_content' => true,
            'landing.contact.email' => 'hello@example.test',
            'landing.contact.phone' => '+62 812 0000 0000',
            'landing.contact.address' => 'Test address',
            'landing.contact.hours' => 'Mon-Fri',
            'landing.contact.whatsapp' => '6281200000000',
        ]);
    }

    public function test_every_inner_page_renders_in_every_locale(): void
    {
        $paths = ['about', 'processes', 'processes/zinc-plating', 'services', 'contact'];

        foreach (config('locales.landing') as $code) {
            $locale = config("locales.locales.{$code}");

            foreach ($paths as $path) {
                $this->get("/{$code}/{$path}")
                    ->assertOk()
                    ->assertSee('<html lang="'.$locale['html_lang'].'" dir="'.$locale['dir'].'">', false);
            }
        }
    }

    public function test_inner_pages_mark_headings_and_steps_for_animation(): void
    {
        $this->get('/en/about')->assertSee('data-anim="hero-title"', false)->assertSee('data-anim="heading"', false);
        $this->get('/en/services')
            ->assertSee('data-anim="steps"', false)
            ->assertSee('data-step-number', false);
    }

    public function test_header_is_sticky_and_has_a_sentinel_to_detect_scrolling(): void
    {
        $this->get('/en/about')
            ->assertSee('<header data-header class="sticky top-0 z-50', false)
            ->assertSee('data-header-sentinel', false)
            ->assertSee('header-chip--logo', false)
            ->assertSee('header-chip--nav', false)
            ->assertSee('aria-current="page"', false);
    }

    public function test_header_menu_has_no_home_item_and_the_chosen_language_shows_a_short_code(): void
    {
        $html = (string) $this->get('/en/about')->getContent();
        $start = (int) strpos($html, '<header data-header');
        $header = substr($html, $start, (int) strpos($html, '</header>', $start) - $start);

        $this->assertStringNotContainsString('aria-current="page">Home', $header);
        $this->assertStringNotContainsString('>'.__('landing.nav.home', [], 'en').'<', str_replace(['<span class="uline">', '</span>'], ['>', '<'], $header));
        $this->assertMatchesRegularExpression('/>\s*EN\s*</', $header);
        $this->assertMatchesRegularExpression('/>\s*Bahasa Indonesia\s*</', $header);
        $this->assertMatchesRegularExpression('/>\s*English\s*</', $header);
    }

    public function test_footer_has_the_logo_with_the_name_back_to_top_button_menu_and_no_marquee(): void
    {
        $html = (string) $this->get('/en/about')->getContent();
        $start = (int) strpos($html, '<footer');
        $footer = substr($html, $start, (int) strpos($html, '</footer>', $start) - $start);

        $this->assertStringContainsString('footer-brand__first', $footer);
        $this->assertStringContainsString('Metindo', $footer);
        $this->assertStringNotContainsString('footer-wordmark', $footer);
        $this->assertStringContainsString('data-to-top', $footer);
        $this->assertStringContainsString('aria-label="'.__('landing.footer.top', [], 'en').'"', $footer);
        $this->assertStringContainsString(__('landing.footer.description', [], 'en'), $footer);
        $this->assertStringContainsString(__('landing.footer.menu', [], 'en'), $footer);
        $this->assertStringContainsString('href="'.route('landing.home', ['locale' => 'en']).'"', $footer);
        $this->assertStringNotContainsString('marquee', strtolower($footer));
    }

    public function test_footer_texts_exist_in_every_language(): void
    {
        foreach (config('locales.landing') as $code) {
            $response = $this->get("/{$code}/about")->assertOk();

            foreach (['menu', 'top', 'contact', 'description'] as $key) {
                $text = __("landing.footer.{$key}", [], $code);

                $this->assertNotSame("landing.footer.{$key}", $text);
                $response->assertSee($text, false);
            }
        }
    }

    public function test_mobile_menu_is_a_full_screen_panel_opened_by_a_hamburger(): void
    {
        $html = (string) $this->get('/en/about')->getContent();

        $this->assertStringContainsString('data-menu-toggle', $html);
        $this->assertStringContainsString('aria-controls="mobile-menu"', $html);
        $this->assertStringContainsString('aria-expanded="false"', $html);
        $this->assertStringContainsString('id="mobile-menu" data-menu hidden', $html);

        $start = (int) strpos($html, 'id="mobile-menu"');
        $menu = substr($html, $start, (int) strpos($html, '</aside>', $start) - $start);

        foreach (['landing.about', 'landing.processes.index', 'landing.services', 'landing.contact'] as $route) {
            $this->assertStringContainsString('href="'.route($route, ['locale' => 'en']).'"', $menu);
        }

        $this->assertStringNotContainsString('href="'.route('landing.home', ['locale' => 'en']).'"', $menu);
        $this->assertStringContainsString('aria-current="page"', $menu);
        $this->assertMatchesRegularExpression('/>\s*Bahasa Indonesia\s*</', $menu);
        $this->assertStringNotContainsString('<details class="pointer-events-auto relative lg:hidden"', $html);
    }

    public function test_page_titles_use_the_active_language(): void
    {
        $this->get('/id/about')->assertSee('<title>'.__('landing.nav.about', [], 'id').' | PT Metindo Perkasa</title>', false);
        $this->get('/ja/services')->assertSee(__('pages.services.title', [], 'ja'));
    }

    public function test_page_title_escapes_special_characters_only_once(): void
    {
        ArticleTranslation::factory()->published('en')->create(['title' => 'Plating & Coating', 'meta_title' => null, 'slug' => 'plating-coating']);

        $this->get('/en/articles/plating-coating')->assertSee('<title>Plating &amp; Coating | PT Metindo Perkasa</title>', false);
    }

    public function test_every_category_page_lists_its_processes(): void
    {
        foreach (config('landing.categories') as $category) {
            $response = $this->get('/en/processes/'.$category['slug'])->assertOk()->assertSee($category['name']);

            foreach ($category['processes'] as $process) {
                $response->assertSee($process);
            }
        }
    }

    public function test_the_category_pages_are_called_processes_in_every_language_and_products_is_free(): void
    {
        foreach (config('locales.landing') as $code) {
            $this->get("/{$code}")->assertSee(__('landing.nav.processes', [], $code));
            $this->get("/{$code}/processes")->assertOk();
            $this->get("/{$code}/products")->assertNotFound();
        }
    }

    public function test_unknown_category_and_article_return_not_found(): void
    {
        $this->get('/en/processes/unknown')->assertNotFound();
        $this->get('/en/articles/unknown')->assertNotFound();
    }

    public function test_menu_links_every_existing_page_and_marks_the_current_one(): void
    {
        ArticleTranslation::factory()->published('en')->create();

        $response = $this->get('/en/processes/zinc-plating')->assertOk();

        foreach (['about', 'processes', 'services', 'articles', 'contact'] as $path) {
            $response->assertSee('href="'.url("/en/{$path}").'"', false);
        }

        $response->assertSee('aria-current="page"', false);
    }

    public function test_contact_shows_only_configured_details(): void
    {
        $this->get('/en/contact')
            ->assertSee('Test address')
            ->assertSee('href="tel:+6281200000000"', false)
            ->assertSee('href="mailto:hello@example.test"', false)
            ->assertSee('href="https://wa.me/6281200000000"', false);

        config(['landing.contact' => ['email' => null, 'phone' => null, 'address' => null, 'hours' => null, 'whatsapp' => null]]);

        $this->get('/en/contact')
            ->assertSee(__('pages.contact.empty', [], 'en'))
            ->assertDontSee('wa.me', false)
            ->assertDontSee('mailto:', false);
    }

    public function test_about_subsidiaries_are_pins_on_a_map_with_a_card_each(): void
    {
        $html = (string) $this->get('/en/about')->getContent();

        $this->assertSame(6, substr_count($html, 'data-pin="'));
        $this->assertSame(6, substr_count($html, 'data-sub="'));
        $this->assertStringContainsString('data-map-land', $html);
        $this->assertStringContainsString('aria-label="'.__('pages.about.subsidiaries.map', [], 'en').'"', $html);
        $this->assertMatchesRegularExpression('/data-pin="jakarta"[^>]*style="left: 26\.27%; top: 70\.6%"/s', $html);
        $this->assertStringContainsString('google.com/maps/search/?api=1&query=3.5952,98.6722', $html);
    }

    public function test_about_has_no_map_when_the_subsidiaries_are_not_shown(): void
    {
        config(['landing.sample_content' => false]);

        $this->get('/en/about')->assertOk()->assertDontSee('data-map-stage', false);
    }

    public function test_about_texts_and_map_label_exist_in_every_language(): void
    {
        foreach (config('locales.landing') as $code) {
            $response = $this->get("/{$code}/about")->assertOk();

            foreach (['pages.about.title', 'pages.about.intro', 'pages.about.approach.title', 'pages.about.vision.text', 'pages.about.subsidiaries.map'] as $key) {
                $text = __($key, [], $code);

                $this->assertNotSame($key, $text);
                $response->assertSee($text, false);
            }
        }
    }

    public function test_about_hides_certifications_and_gates_subsidiaries(): void
    {
        $this->get('/en/about')
            ->assertDontSee('ISO 9001')
            ->assertSee('PT Metindo Surface Medan')
            ->assertSee('rel="noopener noreferrer"', false);

        config(['landing.sample_content' => false]);

        $this->get('/en/about')->assertDontSee('PT Metindo Surface Medan');
    }
}
