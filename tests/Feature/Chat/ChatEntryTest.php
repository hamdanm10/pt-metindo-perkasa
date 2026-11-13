<?php

declare(strict_types=1);

namespace Tests\Feature\Chat;

use App\Livewire\Chat\Editor;
use App\Livewire\Chat\Index;
use App\Livewire\Forms\ChatEntryForm;
use App\Models\ChatEntry;
use App\Models\User;
use App\Services\Landing\LandingChat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class ChatEntryTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    /**
     * @param  array<string, array{question: string, answer: string}|array{question: string, answer: string, link_label: string, link_url: string}>  $texts  keyed by locale
     */
    private function entry(array $texts, ?ChatEntry $parent = null, int $position = 0): ChatEntry
    {
        $entry = ChatEntry::query()->create(['parent_id' => $parent?->id, 'position' => $position]);

        foreach ($texts as $locale => $text) {
            $entry->translations()->create(['locale' => $locale] + $text);
        }

        return $entry;
    }

    public function test_only_the_admin_reaches_the_chat_pages(): void
    {
        $entry = $this->entry(['en' => ['question' => 'Q', 'answer' => 'A']]);
        $urls = ['/app/chat', '/app/chat/create', "/app/chat/{$entry->id}/edit"];

        foreach ($urls as $url) {
            $this->get($url)->assertRedirect(route('app.login'));
        }

        foreach ($urls as $url) {
            $this->actingAs(User::factory()->staff()->create())->get($url)->assertForbidden();
            $this->actingAs(User::factory()->customer()->create())->get($url)->assertForbidden();
            $this->actingAs($this->admin())->get($url)->assertOk();
        }
    }

    public function test_a_question_is_saved_only_in_the_languages_that_are_filled(): void
    {
        Livewire::actingAs($this->admin())->test(Editor::class)
            ->set('form.translations.en.question', '  What do you plate?  ')
            ->set('form.translations.en.answer', 'Zinc and nickel.')
            ->set('form.translations.id.question', 'Apa yang Anda lapis?')
            ->set('form.translations.id.answer', 'Seng dan nikel.')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('app.chat.index'));

        $entry = ChatEntry::query()->with('translations')->firstOrFail();

        $this->assertNull($entry->parent_id);
        $this->assertEqualsCanonicalizing(['en', 'id'], $entry->translations->pluck('locale')->all());
        $this->assertSame('What do you plate?', $entry->translations->where('locale', 'en')->firstOrFail()->question);
        $this->assertNull($entry->translations->where('locale', 'en')->firstOrFail()->link_url);
    }

    public function test_at_least_one_language_is_required_and_a_filled_language_needs_a_question_and_an_answer(): void
    {
        Livewire::actingAs($this->admin())->test(Editor::class)
            ->call('save')
            ->assertHasErrors('form.translations');

        Livewire::actingAs($this->admin())->test(Editor::class)
            ->set('form.translations.en.question', 'Only a question')
            ->call('save')
            ->assertHasErrors(['form.translations.en.answer'])
            ->assertHasNoErrors('form.translations.id.question');

        $this->assertSame(0, ChatEntry::query()->count());
    }

    public function test_a_link_needs_both_its_text_and_an_address_that_is_safe(): void
    {
        $fill = fn ($component, string $label, string $url) => $component
            ->set('form.translations.en.question', 'Q')
            ->set('form.translations.en.answer', 'A')
            ->set('form.translations.en.link_label', $label)
            ->set('form.translations.en.link_url', $url)
            ->call('save');

        $component = Livewire::actingAs($this->admin())->test(Editor::class);

        $fill($component, 'Contact', '')->assertHasErrors('form.translations.en.link_url');
        $fill($component, '', '/en/contact')->assertHasErrors('form.translations.en.link_label');

        foreach (['javascript:alert(1)', '//evil.test/x', 'ftp://files.test', 'contact', 'https://'] as $bad) {
            $fill($component, 'Go', $bad)->assertHasErrors('form.translations.en.link_url');
        }

        foreach (['/en/contact', 'https://example.test/page?x=1'] as $good) {
            $this->assertTrue(ChatEntryForm::isSafeUrl($good));
        }

        $fill($component, 'Contact us', '/en/contact')->assertHasNoErrors();

        $this->assertSame('/en/contact', ChatEntry::query()->firstOrFail()->translations()->firstOrFail()->link_url);
    }

    public function test_follow_up_questions_can_go_as_deep_as_wanted_and_are_listed_with_outline_numbers(): void
    {
        $one = $this->entry(['en' => ['question' => 'Question one', 'answer' => 'A']]);
        $this->entry(['en' => ['question' => 'Question two', 'answer' => 'A']], null, 1);
        $oneOne = $this->entry(['en' => ['question' => 'Question one-one', 'answer' => 'A']], $one);
        $this->entry(['en' => ['question' => 'Question one-two', 'answer' => 'A']], $one, 1);
        $deep = $oneOne;

        foreach (range(1, 5) as $level) {
            $deep = $this->entry(['en' => ['question' => "Deep {$level}", 'answer' => 'A']], $deep);
        }

        Livewire::actingAs($this->admin())->withQueryParams(['parent' => (string) $one->id])->test(Editor::class)
            ->assertSee('Question one')
            ->set('form.translations.en.question', 'Added from the page')
            ->set('form.translations.en.answer', 'A')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame($one->id, ChatEntry::query()->where('position', 2)->firstOrFail()->parent_id);

        $html = (string) Livewire::actingAs($this->admin())->test(Index::class)->html();

        foreach (['>1<', '>1.1<', '>1.1.1<', '>1.1.1.1.1.1<', '>1.2<', '>1.3<', '>2<'] as $number) {
            $this->assertStringContainsString($number, $html);
        }

        $this->assertLessThan(strpos($html, 'Question two'), (int) strpos($html, 'Added from the page'));
    }

    public function test_languages_are_tabs_with_one_panel_open_and_marks_for_filled_and_wrong_ones(): void
    {
        $component = Livewire::actingAs($this->admin())->test(Editor::class)
            ->assertSet('activeLocale', 'en')
            ->set('form.translations.id.question', 'Hanya pertanyaan')
            ->assertSeeHtml('wire:click="selectLocale(\'ja\')"');

        $this->assertSame(1, substr_count($component->html(), 'class="mt-6 space-y-6"'));
        $this->assertSame(4, substr_count($component->html(), 'mt-6 space-y-6 hidden'));
        $this->assertSame(1, substr_count($component->html(), __('chat_manager.filled')));

        $component->call('selectLocale', 'ja')->assertSet('activeLocale', 'ja')
            ->call('selectLocale', 'xx')->assertSet('activeLocale', 'ja');

        $this->assertSame(1, substr_count($component->call('save')->html(), '<span class="font-bold" aria-hidden="true">!</span>'));
    }

    public function test_a_follow_up_needs_an_existing_parent(): void
    {
        $this->actingAs($this->admin())->get('/app/chat/create?parent=999999')->assertNotFound();
    }

    public function test_editing_changes_the_text_and_drops_an_emptied_language(): void
    {
        $entry = $this->entry([
            'en' => ['question' => 'Old', 'answer' => 'Old answer'],
            'id' => ['question' => 'Lama', 'answer' => 'Jawaban lama'],
        ]);

        Livewire::actingAs($this->admin())->test(Editor::class, ['entry' => $entry])
            ->assertSet('form.translations.id.question', 'Lama')
            ->set('form.translations.en.question', 'New')
            ->set('form.translations.id.question', '')
            ->set('form.translations.id.answer', '')
            ->call('save')
            ->assertHasNoErrors();

        $entry->refresh()->load('translations');

        $this->assertSame(['en'], $entry->translations->pluck('locale')->all());
        $this->assertSame('New', $entry->translations->firstOrFail()->question);
    }

    public function test_questions_move_up_and_down_among_their_siblings_only(): void
    {
        $a = $this->entry(['en' => ['question' => 'A', 'answer' => 'x']], null, 0);
        $b = $this->entry(['en' => ['question' => 'B', 'answer' => 'x']], null, 1);
        $child = $this->entry(['en' => ['question' => 'C', 'answer' => 'x']], $a, 0);
        $admin = $this->admin();

        Livewire::actingAs($admin)->test(Index::class)->call('move', $b->id, 'up');

        $this->assertSame(0, $b->refresh()->position);
        $this->assertSame(1, $a->refresh()->position);
        $this->assertSame(0, $child->refresh()->position);

        Livewire::actingAs($admin)->test(Index::class)->call('move', $b->id, 'up');
        $this->assertSame(0, $b->refresh()->position);

        Livewire::actingAs($admin)->test(Index::class)->call('move', $b->id, 'down');
        $this->assertSame(1, $b->refresh()->position);
    }

    public function test_deleting_a_question_removes_its_follow_ups_and_all_their_texts(): void
    {
        $top = $this->entry(['en' => ['question' => 'Top', 'answer' => 'x']]);
        $mid = $this->entry(['en' => ['question' => 'Mid', 'answer' => 'x']], $top);
        $this->entry(['en' => ['question' => 'Low', 'answer' => 'x'], 'id' => ['question' => 'Rendah', 'answer' => 'x']], $mid);
        $other = $this->entry(['en' => ['question' => 'Other', 'answer' => 'x']], null, 1);

        Livewire::actingAs($this->admin())->test(Index::class)
            ->call('delete', $top->id)
            ->assertSee(__('chat_manager.messages.deleted'));

        $this->assertSame([$other->id], ChatEntry::query()->pluck('id')->all());
        $this->assertSame(1, DB::table('chat_entry_translations')->count());
    }

    public function test_staff_cannot_change_the_chat_through_the_components(): void
    {
        $entry = $this->entry(['en' => ['question' => 'Q', 'answer' => 'A']]);
        $staff = User::factory()->staff()->create();

        Livewire::actingAs($staff)->test(Index::class)->assertForbidden();
        Livewire::actingAs($staff)->test(Editor::class)->assertForbidden();

        $this->assertSame(1, ChatEntry::query()->count());
        $this->assertNotNull($entry->fresh());
    }

    public function test_the_landing_gets_a_tree_in_the_page_language_and_hides_what_has_no_text_there(): void
    {
        $top = $this->entry([
            'en' => ['question' => 'Hello?', 'answer' => 'Hi', 'link_label' => 'Contact', 'link_url' => '/en/contact'],
            'id' => ['question' => 'Halo?', 'answer' => 'Hai'],
        ]);
        $this->entry(['en' => ['question' => 'More?', 'answer' => 'More']], $top);
        $onlyId = $this->entry(['id' => ['question' => 'Hanya id', 'answer' => 'x']], null, 1);
        $this->entry(['id' => ['question' => 'Anak', 'answer' => 'x'], 'en' => ['question' => 'Child of id only', 'answer' => 'x']], $onlyId);
        $hiddenParent = $this->entry(['zh' => ['question' => '你好', 'answer' => 'x']], null, 2);
        $this->entry(['en' => ['question' => 'Orphan', 'answer' => 'x']], $hiddenParent);

        $chat = app(LandingChat::class);
        $en = $chat->topics('en');

        $this->assertSame(['Hello?'], array_column($en, 'label'));
        $this->assertSame(['label' => 'Contact', 'url' => '/en/contact'], $en[0]['link']);
        $this->assertSame(['More?'], array_column($en[0]['children'], 'label'));
        $this->assertSame([], $en[0]['children'][0]['children']);

        $id = $chat->topics('id');

        $this->assertSame(['Halo?', 'Hanya id'], array_column($id, 'label'));
        $this->assertNull($id[0]['link']);
        $this->assertSame(['Anak'], array_column($id[1]['children'], 'label'));
        $this->assertSame([], $chat->topics('ja'));
    }

    public function test_the_landing_follows_the_order_set_by_the_admin_and_sees_changes_at_once(): void
    {
        $a = $this->entry(['en' => ['question' => 'First', 'answer' => 'x']], null, 0);
        $b = $this->entry(['en' => ['question' => 'Second', 'answer' => 'x']], null, 1);
        $chat = app(LandingChat::class);

        $this->assertSame(['First', 'Second'], array_column($chat->topics('en'), 'label'));

        Livewire::actingAs($this->admin())->test(Index::class)->call('move', $b->id, 'up');
        $this->assertSame(['Second', 'First'], array_column($chat->topics('en'), 'label'));

        Livewire::actingAs($this->admin())->test(Editor::class, ['entry' => $a])
            ->set('form.translations.en.question', 'Renamed')
            ->call('save');
        $this->assertSame(['Second', 'Renamed'], array_column($chat->topics('en'), 'label'));

        Livewire::actingAs($this->admin())->test(Index::class)->call('delete', $b->id);
        $this->assertSame(['Renamed'], array_column($chat->topics('en'), 'label'));
    }

    public function test_the_chat_button_is_hidden_while_the_language_has_no_questions(): void
    {
        config(['landing.contact.whatsapp_agents' => null, 'landing.contact.whatsapp' => null]);

        $this->get('/en')->assertOk()->assertDontSee('data-widgets', false)->assertDontSee('data-widget-toggle="chat"', false);

        $this->entry(['id' => ['question' => 'Halo?', 'answer' => 'Hai']]);

        $this->get('/en')->assertDontSee('data-widget-toggle="chat"', false);
        $this->get('/id')->assertSee('data-widget-toggle="chat"', false)->assertSee('Halo?');

        config(['landing.contact.whatsapp' => '6281200000001']);

        $this->get('/en')->assertSee('data-widget-toggle="whatsapp"', false)->assertDontSee('data-widget-toggle="chat"', false);
    }

    public function test_the_list_does_not_run_more_queries_when_there_are_more_questions(): void
    {
        $admin = $this->admin();
        $parent = $this->entry(['en' => ['question' => 'Q', 'answer' => 'A']]);

        $count = function () use ($admin): int {
            DB::flushQueryLog();
            DB::enableQueryLog();
            Livewire::actingAs($admin)->test(Index::class);

            return count(DB::getQueryLog());
        };

        $this->entry(['en' => ['question' => 'Q2', 'answer' => 'A']], $parent);
        $few = $count();

        foreach (range(1, 6) as $i) {
            $this->entry(['en' => ['question' => "More {$i}", 'answer' => 'A'], 'id' => ['question' => "Lagi {$i}", 'answer' => 'A']], $parent, $i);
        }

        $this->assertSame($few, $count());
    }

    public function test_the_menu_has_the_chat_for_the_admin_only_and_the_texts_exist_in_every_language(): void
    {
        $this->actingAs($this->admin())->get('/app/dashboard')->assertSee(route('app.chat.index'), false);
        $this->actingAs(User::factory()->staff()->create())->get('/app/dashboard')->assertDontSee(route('app.chat.index'), false);

        foreach (['en', 'id'] as $locale) {
            foreach (['title', 'fields.question', 'messages.bad_link', 'confirm_delete'] as $key) {
                $this->assertNotSame("chat_manager.{$key}", __("chat_manager.{$key}", [], $locale));
            }
        }

        $this->assertSame(
            array_keys(trans('chat_manager', [], 'en')),
            array_keys(trans('chat_manager', [], 'id')),
        );

        foreach (config('locales.landing') as $code) {
            $this->assertNotSame('chat.chat.again', __('chat.chat.again', [], $code));
            $this->assertArrayNotHasKey('topics', trans('chat', [], $code));
        }
    }
}
