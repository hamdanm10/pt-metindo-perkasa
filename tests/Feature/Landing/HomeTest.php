<?php

declare(strict_types=1);

namespace Tests\Feature\Landing;

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

    public function test_home_lists_every_configured_category(): void
    {
        $response = $this->get('/en')->assertOk();

        $this->assertCount(13, config('landing.categories'));

        foreach (config('landing.categories') as $category) {
            $response->assertSee($category['name']);
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
                ->assertDontSee('[PHOTO]');
        } finally {
            unlink($path);
        }
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
            ->assertSee(__('home.intro.begin', [], 'id'));
    }

    public function test_home_marks_the_elements_that_animate(): void
    {
        $this->get('/en')
            ->assertSee('data-anim="hero-title"', false)
            ->assertSee('data-anim="hero-item"', false)
            ->assertSee('data-hero-panel', false)
            ->assertSee('data-anim="grow"', false)
            ->assertSee('data-anim="rows"', false)
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
            $this->get("/{$code}")->assertSee(__('landing.talk', [], $code))->assertSee('href="'.url("/{$code}/contact").'"', false);
        }
    }

    public function test_hero_has_no_sector_ticker(): void
    {
        $this->get('/en')->assertDontSee('marquee', false);
    }

    public function test_services_are_an_accordion_that_is_open_without_javascript(): void
    {
        $response = $this->get('/en')
            ->assertSee('data-accordion', false)
            ->assertSee('aria-expanded="true"', false)
            ->assertSee('aria-controls="service-1-panel"', false)
            ->assertSee('role="region"', false);

        $this->assertSame(3, substr_count((string) $response->getContent(), 'data-acc-item'));
        $response->assertSee(__('home.services.items.consultation.text'));
    }
}
