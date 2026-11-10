<?php

declare(strict_types=1);

namespace Tests\Feature\Landing;

use App\Services\Landing\LandingContact;
use Tests\TestCase;

class ContactWidgetsTest extends TestCase
{
    public function test_agents_are_read_from_the_list_and_bad_entries_are_skipped(): void
    {
        config(['landing.contact.whatsapp_agents' => 'Sales|+62 812-0000-0001; Support|6281200000002 ;Broken|abc;|;6281200000003']);

        $agents = app(LandingContact::class)->agents();

        $this->assertSame(['Sales', 'Support', __('chat.whatsapp.default_name')], array_column($agents, 'name'));
        $this->assertSame(['6281200000001', '6281200000002', '6281200000003'], array_column($agents, 'number'));
        $this->assertSame('+6281200000001', $agents[0]['display']);
    }

    public function test_the_single_whatsapp_number_is_used_when_there_is_no_list(): void
    {
        config(['landing.contact.whatsapp_agents' => null, 'landing.contact.whatsapp' => '+62 812-0000-0009']);

        $agents = app(LandingContact::class)->agents();

        $this->assertCount(1, $agents);
        $this->assertSame('6281200000009', $agents[0]['number']);
    }

    public function test_the_chat_link_carries_the_greeting(): void
    {
        $url = app(LandingContact::class)->url('6281200000001');

        $this->assertStringStartsWith('https://wa.me/6281200000001?text=', $url);
        $this->assertStringContainsString(rawurlencode(__('chat.whatsapp.message')), $url);
    }

    public function test_both_buttons_and_the_agent_list_are_on_every_landing_page(): void
    {
        config(['landing.contact.whatsapp_agents' => 'Sales|6281200000001;Support|6281200000002']);

        foreach (['/en', '/id/about', '/zh/processes', '/ar/services'] as $path) {
            $this->get($path)->assertOk()
                ->assertSee('data-widgets', false)
                ->assertSee('data-widget-toggle="chat"', false)
                ->assertSee('data-widget-toggle="whatsapp"', false)
                ->assertSee('href="https://wa.me/6281200000001?text=', false)
                ->assertSee('href="https://wa.me/6281200000002?text=', false)
                ->assertSee('Sales')
                ->assertSee('+6281200000002');
        }
    }

    public function test_without_numbers_only_the_chat_button_is_shown(): void
    {
        config(['landing.contact.whatsapp_agents' => null, 'landing.contact.whatsapp' => null]);

        $this->get('/en')->assertOk()
            ->assertSee('data-widget-toggle="chat"', false)
            ->assertDontSee('data-widget-toggle="whatsapp"', false)
            ->assertDontSee('wa.me', false);
    }

    public function test_the_chat_rules_come_from_the_site_content(): void
    {
        config(['landing.contact.email' => 'hello@example.test', 'landing.contact.phone' => '+62 800', 'landing.contact.hours' => 'Mon-Fri', 'landing.contact.address' => 'Test street']);

        $rules = $this->rules($this->page('/en'));
        $topics = array_column($rules['topics'], null, 'id');

        $this->assertSame(['processes', 'services', 'quote', 'contact', 'hours', 'location', 'company'], array_keys($topics));
        $this->assertStringContainsString((string) count(config('landing.categories')), $topics['processes']['answer']);
        $this->assertStringContainsString('hello@example.test', $topics['contact']['answer']);
        $this->assertStringContainsString('+62 800', $topics['contact']['answer']);
        $this->assertStringContainsString('Mon-Fri', $topics['hours']['answer']);
        $this->assertStringContainsString('Test street', $topics['location']['answer']);
        $this->assertSame(route('landing.processes.index'), $topics['processes']['link']['url']);
        $this->assertSame(route('landing.about'), $topics['company']['link']['url']);

        foreach ($topics as $topic) {
            $this->assertNotEmpty($topic['keywords']);
            $this->assertSame($topic['keywords'], array_map('mb_strtolower', $topic['keywords']));
        }

        $this->assertSame(__('chat.chat.welcome'), $rules['welcome']);
        $this->assertSame(__('chat.chat.fallback'), $rules['fallback']);
    }

    public function test_topics_without_data_are_left_out(): void
    {
        config(['landing.contact.email' => null, 'landing.contact.phone' => null, 'landing.contact.hours' => null, 'landing.contact.address' => null]);

        $ids = array_column($this->rules($this->page('/en'))['topics'], 'id');

        $this->assertSame(['processes', 'services', 'quote', 'company'], $ids);
    }

    public function test_the_chat_speaks_every_landing_language(): void
    {
        foreach (config('locales.landing') as $code) {
            $response = $this->get("/{$code}")->assertOk();
            $rules = $this->rules($this->page("/{$code}"));

            foreach (['welcome', 'fallback'] as $key) {
                $this->assertNotSame("chat.chat.{$key}", $rules[$key]);
            }

            $this->assertSame(__('chat.chat.title', [], $code), __('chat.chat.title', [], $code));
            $this->assertNotSame('chat.chat.title', __('chat.chat.title', [], $code));
            $response->assertSee(__('chat.open_chat', [], $code), false);

            foreach ($rules['topics'] as $topic) {
                $this->assertNotSame('', $topic['label']);
                $this->assertNotEmpty($topic['keywords']);
            }
        }

        $this->assertNotSame($this->rules($this->page('/en'))['welcome'], $this->rules($this->page('/id'))['welcome']);
    }

    private function page(string $path): string
    {
        $content = $this->get($path)->getContent();

        return is_string($content) ? $content : '';
    }

    /**
     * @return array{welcome: string, fallback: string, topics: list<array{id: string, label: string, answer: string, keywords: list<string>, link: array{label: string, url: string}}>}
     */
    private function rules(string $html): array
    {
        preg_match('/data-chat-rules="([^"]*)"/', $html, $found);

        /** @var array{welcome: string, fallback: string, topics: list<array{id: string, label: string, answer: string, keywords: list<string>, link: array{label: string, url: string}}>} $rules */
        $rules = json_decode(html_entity_decode($found[1] ?? '{}', ENT_QUOTES), true, 512, JSON_THROW_ON_ERROR);

        return $rules;
    }
}
