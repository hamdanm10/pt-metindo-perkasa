<?php

declare(strict_types=1);

namespace Tests\Feature\Landing;

use App\Models\WhatsappAgent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreatesServices;
use Tests\TestCase;

class NumberPickerTest extends TestCase
{
    use CreatesServices;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        config(['landing.contact.email' => 'hello@example.test']);
        $this->createServices();
    }

    private function threeNumbers(): void
    {
        foreach ([['Sales', '6281200000001'], ['Support', '6281200000002'], ['Billing', '6281200000003']] as $position => [$name, $number]) {
            WhatsappAgent::factory()->create(['name' => $name, 'number' => $number, 'position' => $position]);
        }
        WhatsappAgent::factory()->inactive()->create(['name' => 'Off', 'number' => '6281200000009', 'position' => 9]);
    }

    private function html(string $path): string
    {
        $content = $this->get($path)->assertOk()->getContent();

        return is_string($content) ? $content : '';
    }

    public function test_the_service_enquiry_lists_every_active_number(): void
    {
        $this->threeNumbers();

        $html = $this->html('/en/services/electroplating-service');

        $this->assertStringContainsString('data-number-picker=', $html);
        $this->assertStringContainsString('data-number-dialog', $html);

        foreach (['6281200000001', '6281200000002', '6281200000003'] as $number) {
            $this->assertStringContainsString('href="https://wa.me/'.$number.'?text=', $html);
        }

        $this->assertStringNotContainsString('6281200000009', $html);
        $this->assertStringContainsString('Billing', $html);
        $this->assertStringContainsString('>Enquire<', $html);
    }

    public function test_the_service_enquiry_is_a_plain_link_with_one_number_and_absent_with_none(): void
    {
        WhatsappAgent::factory()->create(['number' => '6281200000001']);

        $html = $this->html('/en/services/electroplating-service');
        $this->assertStringNotContainsString('data-number-picker', $html);
        $this->assertStringContainsString('href="https://wa.me/6281200000001?text=', $html);

        WhatsappAgent::query()->get()->each->delete();

        $this->assertStringNotContainsString('wa.me', $this->html('/en/services/electroplating-service'));
    }

    public function test_the_phone_lines_of_the_footer_cta_and_contact_page_list_all_numbers_and_follow_the_admin(): void
    {
        $this->threeNumbers();

        foreach (['/en', '/en/contact'] as $path) {
            $html = $this->html($path);

            foreach (['6281200000001', '6281200000002', '6281200000003'] as $number) {
                $this->assertStringContainsString('href="tel:+'.$number.'"', $html, $path);
            }

            $this->assertStringNotContainsString('tel:+6281200000009', $html);
        }

        WhatsappAgent::query()->where('number', '6281200000002')->firstOrFail()->update(['number' => '6281200000077']);

        $html = $this->html('/en');
        $this->assertStringContainsString('tel:+6281200000077', $html);
        $this->assertStringNotContainsString('tel:+6281200000002', $html);
    }

    public function test_the_picker_texts_exist_in_every_landing_locale(): void
    {
        $this->threeNumbers();

        foreach (config('locales.landing') as $code) {
            $html = $this->html("/{$code}/contact");

            foreach (['whatsapp.title', 'whatsapp.hint', 'call.title', 'call.hint', 'call.action'] as $key) {
                $this->assertNotSame("chat.{$key}", __("chat.{$key}", [], $code));
            }

            $this->assertStringContainsString(__('chat.call.title', [], $code), $html);
        }
    }

    public function test_the_page_query_count_does_not_grow_with_more_numbers(): void
    {
        WhatsappAgent::factory()->create(['number' => '6281200000001']);
        $this->get('/en/contact');

        Cache::flush();
        DB::enableQueryLog();
        $this->get('/en/contact');
        $few = count(DB::getQueryLog());

        WhatsappAgent::factory()->count(3)->create();
        Cache::flush();
        DB::flushQueryLog();
        $this->get('/en/contact');

        $this->assertSame($few, count(DB::getQueryLog()));
    }
}
