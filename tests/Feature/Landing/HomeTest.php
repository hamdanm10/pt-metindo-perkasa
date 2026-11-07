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

    public function test_sample_articles_follow_the_sample_content_flag(): void
    {
        config(['landing.sample_content' => true]);
        $this->get('/en')->assertSee(__('landing.sample_note', [], 'en'));

        config(['landing.sample_content' => false]);
        $this->get('/en')->assertDontSee(__('landing.sample_note', [], 'en'));
    }

    public function test_home_marks_the_elements_that_animate(): void
    {
        $this->get('/en')
            ->assertSee('data-anim="hero-title"', false)
            ->assertSee('data-anim="hero-art"', false)
            ->assertSee('data-plate', false)
            ->assertSee('data-anim="media"', false)
            ->assertSee('data-anim="rows"', false)
            ->assertSee('data-anim="card"', false)
            ->assertSee("classList.add('anim-hide')", false)
            ->assertSee('__introReady', false);
    }

    public function test_art_component_shows_its_fallback_when_the_file_is_missing(): void
    {
        $html = (string) $this->blade('<x-landing.art name="does-not-exist"><span>fallback</span></x-landing.art>');

        $this->assertStringContainsString('fallback', $html);
        $this->assertStringNotContainsString('<img', $html);
    }

    public function test_category_icon_falls_back_to_a_mark_when_the_icon_is_missing(): void
    {
        $html = (string) $this->blade('<x-landing.category-icon slug="no-such-icon" />');

        $this->assertStringContainsString('bg-brand-600', $html);
        $this->assertStringNotContainsString('<img', $html);
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

    public function test_hero_uses_the_vector_art_when_it_exists(): void
    {
        $this->get('/en')->assertSee('images/landing/hero-waves.svg', false);
    }

    public function test_art_component_renders_only_when_the_file_exists(): void
    {
        $this->assertSame('', trim((string) $this->blade('<x-landing.art name="does-not-exist" />')));

        $name = 'test-art-'.uniqid();
        $path = public_path("images/landing/{$name}.svg");

        @mkdir(dirname($path), 0777, true);
        file_put_contents($path, '<svg xmlns="http://www.w3.org/2000/svg"/>');

        try {
            $html = (string) $this->blade('<x-landing.art name="'.$name.'" />');

            $this->assertStringContainsString("images/landing/{$name}.svg", $html);
            $this->assertStringContainsString('alt=""', $html);
        } finally {
            unlink($path);
        }
    }
}
