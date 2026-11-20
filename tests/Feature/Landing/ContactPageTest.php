<?php

declare(strict_types=1);

namespace Tests\Feature\Landing;

use App\Models\WhatsappAgent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'landing.contact.email' => 'hello@example.test',
            'landing.contact.address' => 'Test address 1',
            'landing.contact.hours' => 'Mon-Fri',
        ]);
        WhatsappAgent::factory()->create(['name' => 'Sales', 'number' => '6281200000001', 'position' => 0]);
        WhatsappAgent::factory()->create(['name' => 'Support', 'number' => '6281200000002', 'position' => 1]);
    }

    private function page(string $path = '/en/contact'): string
    {
        $content = $this->get($path)->assertOk()->getContent();

        return is_string($content) ? $content : '';
    }

    public function test_the_details_are_tiles_with_links_and_copy_buttons(): void
    {
        $html = $this->page();

        $this->assertStringContainsString('data-contact-tiles', $html);
        $this->assertStringContainsString('href="tel:+6281200000001"', $html);
        $this->assertStringContainsString('href="tel:+6281200000002"', $html);
        $this->assertStringContainsString('href="mailto:hello@example.test"', $html);
        $this->assertStringContainsString('google.com/maps/search/?api=1&query=Test%20address%201', $html);
        $this->assertStringContainsString('Mon-Fri', $html);
        $this->assertSame(3, substr_count($html, 'data-copy-link='));
        $this->assertStringContainsString('data-copy-link="+6281200000002"', $html);
        $this->assertStringContainsString('data-copy-link="hello@example.test"', $html);
    }

    public function test_the_hero_button_opens_a_picker_with_every_number(): void
    {
        $html = $this->page();

        $this->assertSame(1, substr_count($html, 'data-number-picker='));
        $this->assertSame(1, substr_count($html, 'data-number-dialog'));

        foreach (['6281200000001', '6281200000002'] as $number) {
            $this->assertStringContainsString('href="https://wa.me/'.$number.'?text=', $html);
            $this->assertStringContainsString('href="tel:+'.$number.'"', $html);
        }
    }

    public function test_a_single_number_makes_plain_links_without_a_picker(): void
    {
        WhatsappAgent::query()->where('number', '6281200000002')->firstOrFail()->delete();

        $html = $this->page();

        $this->assertStringNotContainsString('data-number-picker', $html);
        $this->assertStringContainsString('href="https://wa.me/6281200000001?text=', $html);
    }

    public function test_the_required_contact_fields_have_a_red_asterisk(): void
    {
        $this->assertSame(3, substr_count($this->page(), 'text-brand-600" aria-hidden="true"> *</span>'));
    }

    public function test_the_form_writes_the_message_for_whatsapp_or_email_and_stores_nothing(): void
    {
        $html = $this->page();

        $this->assertStringContainsString('data-contact-form', $html);
        $this->assertStringContainsString('novalidate', $html);

        foreach (['name', 'email', 'message'] as $field) {
            $this->assertStringContainsString('name="'.$field.'"', $html);
            $this->assertStringContainsString('id="contact-'.$field.'-error"', $html);
        }

        $this->assertStringContainsString('data-send="whatsapp"', $html);
        $this->assertStringContainsString('data-wa="6281200000001"', $html);
        $this->assertStringContainsString('data-send="mail"', $html);
        $this->assertStringContainsString('data-mail="hello@example.test"', $html);
        $this->assertStringContainsString('data-greeting="'.__('pages.contact.compose.greeting').'"', $html);
        $this->assertStringContainsString(__('pages.contact.form.intro'), $html);

        // There is nothing for the server to receive.
        $this->assertStringNotContainsString('<form data-contact-form novalidate class="mt-[2vw] space-y-[1.6vw] max-lg:mt-6 max-lg:space-y-6" method', $html);
        $this->post('/en/contact')->assertStatus(405);
    }

    public function test_the_recipient_choice_appears_only_with_several_numbers(): void
    {
        $this->assertSame(2, substr_count($this->page(), 'name="agent"'));

        WhatsappAgent::query()->where('name', 'Support')->firstOrFail()->delete();

        $this->assertStringNotContainsString('name="agent"', $this->page());
    }

    public function test_without_a_way_to_send_there_is_no_form(): void
    {
        WhatsappAgent::query()->get()->each->delete();
        config(['landing.contact.email' => null]);

        $html = $this->page();

        $this->assertStringNotContainsString('data-contact-form', $html);
        $this->assertStringContainsString('Test address 1', $html);
    }

    public function test_the_map_of_the_group_is_shown(): void
    {
        $html = $this->page();

        $this->assertStringContainsString(__('pages.contact.find'), $html);
        $this->assertSame(6, substr_count($html, 'data-pin="'));
        $this->assertStringContainsString('data-map-stage', $html);

    }

    public function test_the_page_texts_exist_in_every_language(): void
    {
        foreach (config('locales.landing') as $code) {
            $html = $this->page("/{$code}/contact");

            foreach (['title', 'intro', 'copy', 'copied', 'open_map', 'find', 'form.title', 'form.intro', 'form.name', 'form.whatsapp', 'form.mail', 'form.required', 'form.invalid_email', 'form.done', 'compose.greeting', 'compose.subject'] as $key) {
                $this->assertNotSame("pages.contact.{$key}", __("pages.contact.{$key}", [], $code));
            }

            $this->assertStringContainsString(__('pages.contact.form.title', [], $code), $html);
        }
    }
}
