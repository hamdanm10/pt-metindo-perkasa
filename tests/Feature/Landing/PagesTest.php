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
            'landing.contact.whatsapp_agents' => null,
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
            ->assertSee('data-svc-steps', false)
            ->assertSee('data-svc-art', false);
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

    public function test_desktop_header_menu_lists_contact_beside_the_lets_talk_button(): void
    {
        $html = (string) $this->get('/en/about')->getContent();
        $start = (int) strpos($html, '<header data-header');
        $header = substr($html, $start, (int) strpos($html, '</header>', $start) - $start);
        $nav = substr($header, (int) strpos($header, 'header-links'));

        $this->assertStringContainsString(route('landing.contact', ['locale' => 'en']), substr($nav, 0, (int) strpos($nav, '</nav>')));
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

    public function test_the_language_dropdown_is_a_dark_panel_listing_codes_and_full_names(): void
    {
        $html = (string) $this->get('/en/about')->getContent();

        $this->assertStringContainsString('lang-panel', $html);
        $this->assertStringContainsString('lang-list', $html);
        $this->assertStringNotContainsString('shadow-xl ring-1 ring-ink/10', substr($html, (int) strpos($html, '<header data-header'), 6000));

        foreach (config('locales.landing') as $code) {
            $locale = config("locales.locales.{$code}");

            $this->assertStringContainsString('hreflang="'.$locale['html_lang'].'"', $html);
            $this->assertMatchesRegularExpression('/>\s*'.preg_quote($locale['name'], '/').'\s*</', $html);
        }

        $this->assertStringContainsString('aria-current="true"', $html);
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

    public function test_process_index_is_a_tab_list_with_a_panel_per_category_and_no_catalogue_label(): void
    {
        $categories = config('landing.categories');
        $html = (string) $this->get('/en/processes')->getContent();

        $this->assertSame(count($categories), preg_match_all('/data-ptab\s/', $html));
        $this->assertSame(count($categories), preg_match_all('/<article role="tabpanel"/', $html));
        $this->assertSame(1, substr_count($html, 'data-active'));
        $this->assertStringContainsString('role="tablist"', $html);
        $this->assertStringContainsString('data-count="'.count($categories).'"', $html);
        $this->assertStringNotContainsString('Process catalogue', $html);

        foreach ($categories as $category) {
            $slug = $category['slug'];

            $this->assertStringContainsString('id="tab-'.$slug.'" aria-controls="panel-'.$slug.'"', $html);
            $this->assertStringContainsString('id="panel-'.$slug.'" aria-labelledby="tab-'.$slug.'"', $html);
            $this->assertStringContainsString('href="'.route('landing.processes.show', ['locale' => 'en', 'category' => $slug]).'"', $html);
            $this->assertStringContainsString(__("pages.processes.categories.{$slug}", [], 'en'), $html);

            foreach ($category['processes'] as $process) {
                $this->assertStringContainsString(e($process), $html);
            }
        }

        // The button label is split into one span per word, so look at the text only.
        $text = (string) preg_replace('/\s+/', ' ', strip_tags($html));

        $this->assertStringContainsString(__('pages.processes.view_all', ['count' => count($categories[0]['processes'])], 'en'), $text);
    }

    public function test_process_index_explains_the_four_levels_of_the_catalogue(): void
    {
        $html = (string) $this->get('/en/processes')->getContent();

        $this->assertStringContainsString('data-steps', $html);
        $this->assertSame(4, substr_count($html, 'data-step>') + substr_count($html, 'data-step '));

        foreach (['category', 'process', 'diagram', 'materials'] as $step) {
            $this->assertStringContainsString(__("pages.processes.organised.steps.{$step}.title", [], 'en'), $html);
            $this->assertStringContainsString(__("pages.processes.organised.steps.{$step}.text", [], 'en'), $html);
        }
    }

    public function test_process_index_texts_exist_in_every_language(): void
    {
        foreach (config('locales.landing') as $code) {
            $response = $this->get("/{$code}/processes")->assertOk();

            foreach (['title', 'intro', 'count', 'organised.title', 'organised.intro', 'organised.steps.diagram.text'] as $key) {
                $text = __("pages.processes.{$key}", [], $code);

                $this->assertNotSame("pages.processes.{$key}", $text);
                $response->assertSee($text, false);
            }

            // These two carry a number, so check them filled in.
            $count = count(config('landing.categories.0.processes'));
            $response->assertSeeText(__('pages.processes.view_all', ['count' => $count], $code), false)
                ->assertSee(__('pages.processes.position', ['number' => '01', 'total' => count(config('landing.categories'))], $code), false);
        }
    }

    public function test_services_page_has_a_block_with_an_illustration_per_service_and_a_jump_link(): void
    {
        $html = (string) $this->get('/en/services')->getContent();

        foreach (['electroplating', 'chemical-supply', 'consultation'] as $slug) {
            $this->assertStringContainsString('id="service-'.$slug.'"', $html);
            $this->assertStringContainsString('href="#service-'.$slug.'"', $html);
            // The same illustrations as the services list on the home page.
            $this->assertStringContainsString('images/landing/photos/service-'.$slug.'.webp', $html);
        }

        $this->assertSame(3, substr_count($html, 'data-svc-art '));
        $this->assertSame(4, substr_count($html, 'data-svc-step '));
        $this->assertStringNotContainsString('What we do', $html);

        foreach (['electroplating', 'chemical_supply', 'consultation'] as $service) {
            $this->assertStringContainsString(__("home.services.items.{$service}.title", [], 'en'), $html);
            $this->assertStringContainsString(__("home.services.items.{$service}.text", [], 'en'), $html);
        }

        $this->assertStringContainsString('href="'.route('landing.contact', ['locale' => 'en']).'"', $html);
        $this->assertSame(2, substr_count($html, __('pages.services.see_processes', [], 'en')));
    }

    public function test_services_page_texts_exist_in_every_language(): void
    {
        foreach (config('locales.landing') as $code) {
            $response = $this->get("/{$code}/services")->assertOk();

            foreach (['title', 'intro', 'jump', 'see_processes', 'how.title', 'how.intro', 'how.steps.delivery.text'] as $key) {
                $text = __("pages.services.{$key}", [], $code);

                $this->assertNotSame("pages.services.{$key}", $text);
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
