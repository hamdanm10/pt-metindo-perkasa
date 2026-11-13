<?php

declare(strict_types=1);

namespace Tests\Feature\Landing;

use App\Models\ChatEntry;
use App\Services\Landing\LandingContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactWidgetsTest extends TestCase
{
    use RefreshDatabase;

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
        $this->addTopic();

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

    public function test_each_panel_sits_in_the_group_of_its_own_button_and_the_chat_has_no_text_box(): void
    {
        config(['landing.contact.whatsapp_agents' => 'Sales|6281200000001']);
        $this->addTopic();
        $html = (string) $this->get('/en')->getContent();

        // Chat button group, then WhatsApp button group, side by side; each panel comes right before its own button.
        $chatPanel = (int) strpos($html, 'data-panel="chat"');
        $chatButton = (int) strpos($html, 'data-widget-toggle="chat"');
        $waPanel = (int) strpos($html, 'data-panel="whatsapp"');
        $waButton = (int) strpos($html, 'data-widget-toggle="whatsapp"');

        $this->assertTrue($chatPanel < $chatButton && $chatButton < $waPanel && $waPanel < $waButton);
        $this->assertStringNotContainsString('data-chat-input', $html);
        $this->assertStringNotContainsString('data-chat-form', $html);
    }

    public function test_the_scrolling_lists_of_the_panels_are_left_to_the_browser_and_not_to_the_page_scroll(): void
    {
        config(['landing.contact.whatsapp_agents' => 'Sales|6281200000001']);
        $this->addTopic();

        $html = (string) $this->get('/en')->getContent();

        $this->assertMatchesRegularExpression('/<div data-chat-log data-lenis-prevent class="[^"]*overscroll-contain/', $html);
        $this->assertMatchesRegularExpression('/<ul data-lenis-prevent class="[^"]*overscroll-contain/', $html);
    }

    public function test_without_numbers_only_the_chat_button_is_shown(): void
    {
        $this->addTopic();
        config(['landing.contact.whatsapp_agents' => null, 'landing.contact.whatsapp' => null]);

        $this->get('/en')->assertOk()
            ->assertSee('data-widget-toggle="chat"', false)
            ->assertDontSee('data-widget-toggle="whatsapp"', false)
            ->assertDontSee('wa.me', false);
    }

    public function test_the_chat_speaks_every_landing_language(): void
    {
        $this->addTopic();

        foreach (config('locales.landing') as $code) {
            $response = $this->get("/{$code}")->assertOk();
            $rules = $this->rules($this->page("/{$code}"));

            foreach (['welcome', 'again'] as $key) {
                $this->assertNotSame("chat.chat.{$key}", $rules[$key]);
            }

            $this->assertNotSame('chat.chat.title', __('chat.chat.title', [], $code));
            $response->assertSee(__('chat.open_chat', [], $code), false);

            foreach ($rules['topics'] as $topic) {
                $this->assertNotSame('', $topic['label']);
                $this->assertNotSame('', $topic['answer']);
            }
        }

        $this->assertNotSame($this->rules($this->page('/en'))['welcome'], $this->rules($this->page('/id'))['welcome']);
    }

    /**
     * One question with an answer in every landing language, so the chat has something to show.
     */
    private function addTopic(): void
    {
        $entry = ChatEntry::query()->create(['parent_id' => null, 'position' => 0]);

        foreach (config('locales.landing') as $code) {
            $entry->translations()->create(['locale' => $code, 'question' => "Question {$code}", 'answer' => "Answer {$code}"]);
        }
    }

    private function page(string $path): string
    {
        $content = $this->get($path)->getContent();

        return is_string($content) ? $content : '';
    }

    /**
     * @return array{welcome: string, again: string, topics: list<array{id: int, label: string, answer: string, link: array{label: string, url: string}|null, children: list<mixed>}>}
     */
    private function rules(string $html): array
    {
        preg_match('/data-chat-rules="([^"]*)"/', $html, $found);

        /** @var array{welcome: string, again: string, topics: list<array{id: int, label: string, answer: string, link: array{label: string, url: string}|null, children: list<mixed>}>} $rules */
        $rules = json_decode(html_entity_decode($found[1] ?? '{}', ENT_QUOTES), true, 512, JSON_THROW_ON_ERROR);

        return $rules;
    }
}
