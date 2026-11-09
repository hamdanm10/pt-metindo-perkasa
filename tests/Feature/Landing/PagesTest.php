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
