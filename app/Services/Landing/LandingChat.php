<?php

declare(strict_types=1);

namespace App\Services\Landing;

use App\Models\ChatEntryTranslation;
use Illuminate\Support\Facades\Cache;

/**
 * The questions of the rule-based chat assistant in one landing language, as a tree: a question has an
 * answer, an optional link and follow-up questions, to any depth. The admin writes them per language;
 * a question with no text in the language is left out together with everything below it. Nothing is
 * generated: the visitor taps a question and the browser shows the answer and the follow-ups.
 * Cached per language and cleared whenever the admin changes the chat.
 *
 * @phpstan-type Topic array{id: int, label: string, answer: string, link: array{label: string, url: string}|null, children: list<array<string, mixed>>}
 */
class LandingChat
{
    /**
     * @return list<Topic>
     */
    public function topics(?string $locale = null): array
    {
        $locale ??= app()->getLocale();

        /** @var list<Topic> $topics */
        $topics = Cache::rememberForever($this->key($locale), fn (): array => $this->build($locale));

        return $topics;
    }

    public function flush(): void
    {
        foreach (config()->array('locales.landing') as $locale) {
            Cache::forget($this->key((string) $locale));
        }
    }

    private function key(string $locale): string
    {
        return "landing.chat.{$locale}";
    }

    /**
     * @return list<Topic>
     */
    private function build(string $locale): array
    {
        $rows = ChatEntryTranslation::query()
            ->join('chat_entries', 'chat_entries.id', '=', 'chat_entry_translations.chat_entry_id')
            ->where('chat_entry_translations.locale', $locale)
            ->orderBy('chat_entries.position')
            ->orderBy('chat_entries.id')
            ->get(['chat_entries.id as entry_id', 'chat_entries.parent_id', 'chat_entry_translations.question', 'chat_entry_translations.answer', 'chat_entry_translations.link_label', 'chat_entry_translations.link_url']);

        $byParent = [];

        foreach ($rows as $row) {
            $byParent[(int) $row->getAttribute('parent_id')][] = $row;
        }

        return $this->branch($byParent, 0);
    }

    /**
     * @param  array<int, list<ChatEntryTranslation>>  $byParent  rows grouped by parent id, 0 for the top level
     * @return list<Topic>
     */
    private function branch(array $byParent, int $parent): array
    {
        $topics = [];

        foreach ($byParent[$parent] ?? [] as $row) {
            $id = (int) $row->getAttribute('entry_id');
            $label = $row->getAttribute('link_label');
            $url = $row->getAttribute('link_url');

            $topics[] = [
                'id' => $id,
                'label' => (string) $row->question,
                'answer' => (string) $row->answer,
                'link' => is_string($label) && $label !== '' && is_string($url) && $url !== '' ? ['label' => $label, 'url' => $url] : null,
                'children' => $this->branch($byParent, $id),
            ];
        }

        return $topics;
    }
}
