<?php

declare(strict_types=1);

namespace Tests\Feature\Landing;

use Tests\TestCase;

class PagesTest extends TestCase
{
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
        $paths = ['about', 'products', 'products/zinc-plating', 'services', 'articles', 'articles/quality-control-in-plating', 'contact'];

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
            ->assertSee('aria-current="page"', false);
    }

    public function test_page_titles_use_the_active_language(): void
    {
        $this->get('/id/about')->assertSee('<title>'.__('landing.nav.about', [], 'id').' | PT Metindo Perkasa</title>', false);
        $this->get('/ja/services')->assertSee(__('pages.services.title', [], 'ja'));
    }

    public function test_page_title_escapes_special_characters_only_once(): void
    {
        $this->get('/en/articles')->assertSee('<title>Articles &amp; News | PT Metindo Perkasa</title>', false);
    }

    public function test_every_category_page_lists_its_processes(): void
    {
        foreach (config('landing.categories') as $category) {
            $response = $this->get('/en/products/'.$category['slug'])->assertOk()->assertSee($category['name']);

            foreach ($category['processes'] as $process) {
                $response->assertSee($process);
            }
        }
    }

    public function test_unknown_category_and_article_return_not_found(): void
    {
        $this->get('/en/products/unknown')->assertNotFound();
        $this->get('/en/articles/unknown')->assertNotFound();
    }

    public function test_article_filter_narrows_the_list(): void
    {
        $news = config('landing.articles');

        $this->get('/en/articles?type=news')
            ->assertSee(__('articles.items.quality-control-in-plating.title', [], 'en'))
            ->assertDontSee(__('articles.items.choosing-the-right-pre-treatment.title', [], 'en'));

        $this->get('/en/articles?type=article')
            ->assertSee(__('articles.items.choosing-the-right-pre-treatment.title', [], 'en'))
            ->assertDontSee(__('articles.items.quality-control-in-plating.title', [], 'en'));

        $this->get('/en/articles?type=invalid')
            ->assertSee(__('articles.items.quality-control-in-plating.title', [], 'en'))
            ->assertSee(__('articles.items.choosing-the-right-pre-treatment.title', [], 'en'));

        $this->assertCount(4, $news);
    }

    public function test_articles_are_unavailable_without_sample_content(): void
    {
        config(['landing.sample_content' => false]);

        $this->get('/en/articles')->assertNotFound();
        $this->get('/en/articles/quality-control-in-plating')->assertNotFound();
        $this->get('/en/about')->assertOk()->assertDontSee('href="'.url('/en/articles').'"', false);
        $this->get('/en')->assertDontSee('/en/articles', false);
    }

    public function test_menu_links_every_existing_page_and_marks_the_current_one(): void
    {
        $response = $this->get('/en/products/zinc-plating')->assertOk();

        foreach (['about', 'products', 'services', 'articles', 'contact'] as $path) {
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
