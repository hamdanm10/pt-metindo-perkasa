<?php

declare(strict_types=1);

namespace Tests\Feature\Landing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ButtonComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_button_with_an_href_is_a_link_with_two_label_copies_and_an_arrow(): void
    {
        $html = (string) $this->blade('<x-landing.button href="/x">View all</x-landing.button>');

        $this->assertStringContainsString('href="/x"', $html);
        $this->assertStringContainsString('data-btn', $html);
        $this->assertStringContainsString('data-btn-fill', $html);
        $this->assertStringContainsString('data-btn-arrow', $html);
        $this->assertSame(2, substr_count($html, 'data-btn-copy'));
        $this->assertSame(4, substr_count($html, 'data-btn-word'));
        $this->assertStringContainsString('aria-hidden="true" class="invisible"', $html);
    }

    public function test_a_button_without_an_href_is_not_a_link(): void
    {
        $html = (string) $this->blade('<x-landing.button>Soon</x-landing.button>');

        $this->assertStringStartsWith('<span', trim($html));
        $this->assertStringNotContainsString('href=', $html);
    }

    public function test_the_arrow_can_be_left_out_and_the_tag_changed(): void
    {
        $html = (string) $this->blade('<x-landing.button as="button" type="button" :arrow="false">Go</x-landing.button>');

        $this->assertStringStartsWith('<button', trim($html));
        $this->assertStringContainsString('type="button"', $html);
        $this->assertStringNotContainsString('data-btn-arrow', $html);
    }

    public function test_variants_set_the_fill_and_the_text_colour_that_goes_with_it(): void
    {
        $outline = (string) $this->blade('<x-landing.button variant="outline">A</x-landing.button>');
        $light = (string) $this->blade('<x-landing.button variant="light">A</x-landing.button>');

        $this->assertStringContainsString('ring-white', $outline);
        $this->assertStringContainsString('data-hover-text="#1b1f3b"', $outline);
        $this->assertStringContainsString('bg-white', $light);
        $this->assertStringContainsString('data-hover-text="#ffffff"', $light);
    }

    public function test_every_button_on_the_home_page_is_the_shared_component(): void
    {
        config(['landing.contact.email' => 'hello@example.test']);

        // The round icon buttons of the floating chat and WhatsApp are not text buttons; they show only with a number or a question, so this page has none.
        $html = (string) $this->get('/en')->getContent();

        $this->assertStringNotContainsString('data-widgets', $html);

        $this->assertGreaterThanOrEqual(8, substr_count($html, 'data-btn '));
        $this->assertStringNotContainsString('hover:bg-brand-700">', $html);
    }

    public function test_labels_with_special_characters_stay_escaped(): void
    {
        $html = (string) $this->blade('<x-landing.button>{{ $label }}</x-landing.button>', ['label' => '<b>x</b> & y']);

        $this->assertStringNotContainsString('<b>', $html);
        $this->assertStringContainsString('&lt;b&gt;x&lt;/b&gt;', $html);
    }
}
