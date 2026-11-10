<?php

declare(strict_types=1);

namespace Tests\Feature\Landing;

use App\Services\Landing\LandingGallery;
use Tests\TestCase;

class HomeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        config([
            'landing.intro_video_id' => 'abc123',
            'landing.contact.email' => 'hello@example.test',
            'landing.contact.phone' => '+62 812 0000 0000',
        ]);
    }

    public function test_home_shows_the_hero_heading_in_every_locale(): void
    {
        foreach (config('locales.landing') as $code) {
            $this->get("/{$code}")
                ->assertOk()
                ->assertSee(__('home.hero.text', [], $code));
        }
    }

    public function test_home_features_four_categories_and_links_to_all_processes(): void
    {
        $response = $this->get('/en')->assertOk();
        $categories = config('landing.categories');

        $this->assertCount(13, $categories);

        foreach (array_slice($categories, 0, 4) as $category) {
            $response->assertSee($category['name'])
                ->assertSee('href="'.url('/en/processes/'.$category['slug']).'"', false);
        }

        $response->assertDontSee($categories[4]['name']);
        // Four cards in the catalog deck, and one card per team photo in the About deck.
        $this->assertSame(4 + count(app(LandingGallery::class)->slides()), substr_count((string) $response->getContent(), 'data-deck-card'));
        $this->assertSame(4, substr_count((string) $response->getContent(), 'data-catalog-tile '));
        $response->assertSee('data-catalog-stage', false)
            ->assertSee('data-catalog-panel', false)
            ->assertSee('id="catalog"', false)
            ->assertSee('href="'.url('/en/processes').'"', false)
            ->assertSeeText(__('home.catalog.all', [], 'en'));
    }

    public function test_catalog_cards_show_placeholder_images_and_no_label(): void
    {
        config(['landing.sample_content' => true]);

        $response = $this->get('/en')->assertOk();

        foreach (array_slice(config('landing.categories'), 0, 4) as $category) {
            $response->assertSee('[PHOTO] images/landing/photos/product-'.$category['slug'].'.webp');
        }

        $response->assertDontSee('data-catalog-badge', false)
            ->assertDontSee('data-catalog-label', false);
    }

    public function test_catalog_description_and_button_exist_in_every_language(): void
    {
        foreach (config('locales.landing') as $code) {
            $this->get("/{$code}")
                ->assertSee(__('home.catalog.text', [], $code))
                ->assertSeeText(__('home.catalog.all', [], $code))
                ->assertSee('href="'.url("/{$code}/processes").'"', false);
        }
    }

    public function test_video_section_loads_nothing_from_youtube_before_a_click(): void
    {
        $this->get('/en')
            ->assertSee('data-video="abc123"', false)
            ->assertSee('data-video-play', false)
            ->assertDontSee('youtube', false)
            ->assertDontSee('<iframe', false);
    }

    public function test_video_thumbnail_is_a_placeholder_until_the_file_exists_and_never_comes_from_youtube(): void
    {
        config(['landing.sample_content' => true]);

        $this->get('/en')
            ->assertSee('[PHOTO] images/landing/photos/video-thumbnail.webp')
            ->assertSee(__('home.video.play'))
            ->assertDontSee('ytimg', false);

        $path = public_path('images/landing/photos/video-thumbnail.webp');
        @mkdir(dirname($path), 0777, true);
        file_put_contents($path, 'x');

        try {
            $this->get('/en')
                ->assertSee('photos/video-thumbnail.webp', false)
                ->assertDontSee('[PHOTO] images/landing/photos/video-thumbnail.webp');
        } finally {
            unlink($path);
        }
    }

    public function test_video_section_has_the_animation_hooks(): void
    {
        $this->get('/en')
            ->assertSee('data-video-parallax', false)
            ->assertSee('data-video-circle', false)
            ->assertSee('data-video-label', false);
    }

    public function test_video_section_shows_a_visible_play_label_in_every_language(): void
    {
        foreach (config('locales.landing') as $code) {
            $this->get("/{$code}")->assertSee(__('home.video.play', [], $code));
        }
    }

    public function test_video_section_is_absent_without_a_video_id(): void
    {
        config(['landing.intro_video_id' => null]);

        $this->get('/en')->assertDontSee('data-video', false);
    }

    public function test_contact_links_use_the_configured_email_and_phone(): void
    {
        $this->get('/en')
            ->assertSee('href="mailto:hello@example.test"', false)
            ->assertSee('href="tel:+6281200000000"', false);
    }

    public function test_cta_is_a_card_with_buttons_contact_line_and_three_graphics(): void
    {
        $response = $this->get('/en')->assertOk();

        $response->assertSee('data-cta-card', false)
            ->assertSee(__('landing.cta.title'))
            ->assertSee(__('landing.cta.text'))
            ->assertSee('href="'.route('landing.contact', ['locale' => 'en']).'"', false)
            ->assertSee('href="mailto:hello@example.test"', false)
            ->assertSee('href="tel:+6281200000000"', false)
            ->assertSee('data-cta-art', false);

        // The three CTA graphics and the three of the About headline have different clip ids.
        preg_match_all('/<clipPath id="(art-[a-z]+-[0-9a-f]{6})"/', (string) $response->getContent(), $found);
        $this->assertSame(count($found[1]), count(array_unique($found[1])));
        $this->assertCount(4, $found[1]);
    }

    public function test_cta_is_absent_without_an_email(): void
    {
        config(['landing.contact.email' => null]);

        $this->get('/en')->assertOk()->assertDontSee('data-cta-card', false);
    }

    public function test_contact_links_are_absent_without_configuration(): void
    {
        config(['landing.contact.email' => null, 'landing.contact.phone' => null]);

        $this->get('/en')
            ->assertDontSee('mailto:', false)
            ->assertDontSee('tel:', false);
    }

    public function test_intro_dialog_is_present_with_a_labelled_close_button(): void
    {
        $this->get('/id')
            ->assertSee('id="intro" role="dialog" aria-modal="true"', false)
            ->assertSee('data-intro-close', false)
            ->assertSee('data-intro-item', false)
            ->assertSeeText(__('home.intro.begin', [], 'id'));
    }

    public function test_home_marks_the_elements_that_animate(): void
    {
        $this->get('/en')
            ->assertSee('data-anim="hero-title"', false)
            ->assertSee('data-anim="hero-item"', false)
            ->assertSee('data-hero-panel', false)
            ->assertSee('data-anim="grow"', false)
            ->assertSee("classList.add('anim-hide')", false)
            ->assertSee('__introReady', false);
    }

    public function test_photo_component_follows_files_and_the_sample_content_flag(): void
    {
        config(['landing.sample_content' => true]);
        $this->assertStringContainsString('[PHOTO] images/landing/photos/missing.webp', (string) $this->blade('<x-landing.photo name="missing" />'));

        config(['landing.sample_content' => false]);
        $this->assertSame('', trim((string) $this->blade('<x-landing.photo name="missing" />')));

        $name = 'test-photo-'.uniqid();
        $path = public_path("images/landing/photos/{$name}.webp");

        @mkdir(dirname($path), 0777, true);
        file_put_contents($path, 'x');

        try {
            $html = (string) $this->blade('<x-landing.photo name="'.$name.'" alt="Factory" />');

            $this->assertStringContainsString("photos/{$name}.webp", $html);
            $this->assertStringContainsString('alt="Factory"', $html);
            $this->assertStringNotContainsString('[PHOTO]', $html);
        } finally {
            unlink($path);
        }
    }

    public function test_brand_assets_are_the_new_monogram(): void
    {
        foreach (['images/brand/logo.svg', 'images/brand/logo-white.svg', 'favicon.svg', 'favicon.ico', 'apple-touch-icon.png'] as $file) {
            $this->assertFileExists(public_path($file));
        }

        $this->assertStringContainsString('#D6283A', (string) file_get_contents(public_path('images/brand/logo.svg')));
        $this->assertStringContainsString('#FFFFFF', (string) file_get_contents(public_path('images/brand/logo-white.svg')));

        $this->get('/en')
            ->assertSee('images/brand/logo-white.svg', false)
            ->assertSee('rel="icon" href="'.asset('favicon.svg').'"', false)
            ->assertSee('rel="apple-touch-icon" href="'.asset('apple-touch-icon.png').'"', false);
    }

    public function test_header_links_the_contact_page_as_the_call_to_action(): void
    {
        foreach (config('locales.landing') as $code) {
            $this->get("/{$code}")->assertSeeText(__('landing.talk', [], $code))->assertSee('href="'.url("/{$code}/contact").'"', false);
        }
    }

    public function test_hero_has_no_sector_ticker(): void
    {
        $this->get('/en')->assertDontSee('marquee', false);
    }

    public function test_services_list_is_complete_and_open_without_javascript(): void
    {
        $response = $this->get('/en')
            ->assertSee('data-services-track', false)
            ->assertSee('data-services-pin', false)
            ->assertSee(__('home.services.intro'));

        $this->assertSame(3, substr_count((string) $response->getContent(), 'data-acc-item'));

        foreach (['electroplating', 'chemical_supply', 'consultation'] as $service) {
            $response->assertSee(__("home.services.items.{$service}.title"))
                ->assertSee(__("home.services.items.{$service}.text"));
        }

        $response->assertDontSee('aria-expanded', false)
            ->assertSee('[PHOTO] images/landing/photos/service-chemical-supply.webp');
    }

    public function test_every_service_has_a_full_description_in_every_language(): void
    {
        foreach (config('locales.landing') as $code) {
            foreach (['electroplating', 'chemical_supply', 'consultation'] as $service) {
                $this->assertGreaterThan(
                    $code === 'en' || $code === 'id' ? 150 : 50,
                    mb_strlen(__("home.services.items.{$service}.text", [], $code)),
                    "{$code} {$service} description is too short",
                );
            }
        }
    }

    public function test_services_learn_more_exists_in_every_language(): void
    {
        foreach (config('locales.landing') as $code) {
            $this->get("/{$code}")
                ->assertSeeText(__('home.services.more', [], $code))
                ->assertSee(__('home.services.intro', [], $code));
        }
    }

    public function test_clients_section_shows_the_client_and_partner_logos(): void
    {
        config(['landing.sample_content' => false]);

        $response = $this->get('/en')->assertOk();

        $this->assertSame(2, substr_count((string) $response->getContent(), 'data-clients-slide '));

        $response->assertSee('data-chip', false)
            ->assertSee('data-clients-prev', false)
            ->assertSee('data-clients-next', false)
            ->assertSee('data-label="Clients"', false)
            ->assertSee('data-label="Partners"', false)
            ->assertSee('images/landing/clients/client-1.png', false)
            ->assertSee('alt="Client 1"', false)
            ->assertSee('images/landing/partners/partner-1.png', false)
            ->assertDontSee('[LOGO]')
            ->assertDontSee('Furniture &amp; Hardware', false)
            ->assertDontSee('Oil &amp; Gas', false);
    }

    public function test_sample_mode_shows_a_client_and_a_partner_testimonial_with_a_logo_and_production_none(): void
    {
        config(['landing.sample_content' => true]);

        $response = $this->get('/en')->assertOk();

        $this->assertSame(2, substr_count((string) $response->getContent(), 'data-clients-testimonial'));
        $response->assertSee('Sample Customer')
            ->assertSee('Sample Partner')
            ->assertSee('images/landing/clients/client-3.png', false);

        config(['landing.sample_content' => false]);

        $this->get('/en')->assertOk()->assertDontSee('data-clients-testimonial', false);
    }

    public function test_clients_section_falls_back_to_placeholders_and_partner_names_without_logo_files(): void
    {
        $empty = sys_get_temp_dir().'/landing-logos-'.uniqid();
        mkdir($empty);
        config(['landing.logo_directory' => $empty, 'landing.sample_content' => true, 'landing.partners' => ['Alpha', 'Beta']]);

        try {
            $response = $this->get('/en')->assertOk();

            $this->assertSame(2, substr_count((string) $response->getContent(), 'data-clients-slide '));
            $this->assertSame(9, substr_count((string) $response->getContent(), '[LOGO]'));
            $response->assertSee('Alpha');

            config(['landing.sample_content' => false]);

            $response = $this->get('/en')->assertOk();
            $this->assertSame(1, substr_count((string) $response->getContent(), 'data-clients-slide '));
            $response->assertDontSee('data-clients-next', false);
        } finally {
            rmdir($empty);
        }
    }

    public function test_client_logos_are_read_from_the_logo_folder(): void
    {
        $directory = sys_get_temp_dir().'/landing-logos-'.uniqid();
        mkdir($directory.'/clients', 0777, true);
        file_put_contents($directory.'/clients/acme-motors.svg', '<svg xmlns="http://www.w3.org/2000/svg"/>');
        config(['landing.logo_directory' => $directory, 'landing.sample_content' => false, 'landing.partners' => ['Alpha']]);

        try {
            $this->get('/en')->assertOk()
                ->assertSee('images/landing/clients/acme-motors.svg', false)
                ->assertSee('alt="Acme Motors"', false)
                ->assertDontSee('[LOGO]');
        } finally {
            unlink($directory.'/clients/acme-motors.svg');
            rmdir($directory.'/clients');
            rmdir($directory);
        }
    }

    public function test_the_chip_labels_exist_in_every_language(): void
    {
        foreach (config('locales.landing') as $code) {
            $this->get("/{$code}")
                ->assertSee('data-label="'.__('home.clients.clients', [], $code).'"', false)
                ->assertSee('data-label="'.__('home.clients.partners', [], $code).'"', false);
        }
    }

    public function test_clients_section_texts_exist_in_every_language(): void
    {
        foreach (config('locales.landing') as $code) {
            $this->get("/{$code}")
                ->assertSee(__('home.clients.eyebrow', [], $code))
                ->assertSee(__('home.clients.title', [], $code))
                ->assertSee(__('home.clients.next', [], $code));
        }
    }

    public function test_gallery_shows_the_team_photos_with_a_caption_for_each(): void
    {
        config(['landing.sample_content' => false]);

        $response = $this->get('/en')->assertOk();

        $this->assertSame(3, substr_count((string) $response->getContent(), 'data-gallery-slide '));

        foreach (['celebration', 'teamwork', 'togetherness'] as $key) {
            $response->assertSee('images/landing/photos/gallery-'.$key.'.webp', false)
                ->assertSee('data-label="'.__("home.gallery.slides.{$key}.label").'"', false)
                ->assertSee('data-caption="'.__("home.gallery.slides.{$key}.caption").'"', false);
        }

        $response->assertSee('data-gallery-count', false)
            ->assertSee('01 / 03')
            ->assertSee(__('home.gallery.label'))
            ->assertDontSee('[PHOTO]');
    }

    public function test_gallery_captions_exist_in_every_language(): void
    {
        foreach (config('locales.landing') as $code) {
            $response = $this->get("/{$code}")->assertOk();

            foreach (['celebration', 'teamwork', 'togetherness'] as $key) {
                $caption = __("home.gallery.slides.{$key}.caption", [], $code);

                $this->assertNotSame("home.gallery.slides.{$key}.caption", $caption);
                $response->assertSee($caption, false);
            }
        }
    }

    public function test_about_section_shows_the_headline_lines_graphics_text_button_and_photo_deck(): void
    {
        $response = $this->get('/en')->assertOk();

        foreach (__('home.about.lines') as $line) {
            $response->assertSee($line);
        }

        // Three graphics in the About headline and three in the call-to-action card.
        $this->assertSame(6, substr_count((string) $response->getContent(), 'data-about-art'));

        // Hooks the hover animation moves: the chart, the bands, and the hexagons.
        $response->assertSee('data-about-headline', false)
            ->assertSee('data-art-chart', false)
            ->assertSee('data-art-bar', false)
            ->assertSee('data-art-hex', false);

        $response->assertSee(__('home.about.label'))
            ->assertSee(__('home.about.text'))
            ->assertSee('data-about-deck-panel', false)
            ->assertSee('href="'.route('landing.about', ['locale' => 'en']).'"', false);
    }

    public function test_about_texts_exist_in_every_language(): void
    {
        foreach (config('locales.landing') as $code) {
            $response = $this->get("/{$code}")->assertOk();

            foreach ([...__('home.about.lines', [], $code), __('home.about.label', [], $code), __('home.about.text', [], $code)] as $text) {
                $this->assertIsString($text);
                $this->assertNotSame('', $text);
                $response->assertSee($text, false);
            }

            if ($code !== 'en') {
                $this->assertNotSame(__('home.about.text', [], 'en'), __('home.about.text', [], $code));
            }
        }
    }

    public function test_home_shows_three_static_sample_article_cards_while_there_is_no_article(): void
    {
        config(['landing.sample_content' => true]);

        $response = $this->get('/en')->assertOk();

        $this->assertSame(3, substr_count((string) $response->getContent(), '[SAMPLE] Article title'));
        $response->assertSee(__('home.articles.title'))
            ->assertSee('[PHOTO] article thumbnail')
            ->assertSee('class="title-hover"', false)
            ->assertSee(__('articles.tags.news'))
            ->assertSee('data-btn', false)
            ->assertSeeText(__('home.articles.all'))
            ->assertDontSee('data-anim="rows"', false)
            ->assertDontSee('href="'.url('/en/articles').'"', false);
    }

    public function test_sample_article_cards_are_hidden_outside_sample_mode(): void
    {
        config(['landing.sample_content' => false]);

        $this->get('/en')->assertOk()->assertDontSee('[SAMPLE] Article title');
    }
}
