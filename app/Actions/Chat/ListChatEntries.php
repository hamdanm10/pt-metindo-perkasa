<?php

declare(strict_types=1);

namespace App\Actions\Chat;

use App\Models\ChatEntry;

/**
 * Every chat question in tree order (a question, then its follow-ups, then the next question), with
 * how deep it sits and its outline number (1, 1.1, 1.2, 2, ...), so the page can indent it. Two queries however many questions there are.
 *
 * @phpstan-type Row array{entry: ChatEntry, depth: int, number: string, label: string, locales: list<string>, first: bool, last: bool}
 */
class ListChatEntries
{
    /**
     * @return list<Row>
     */
    public function execute(): array
    {
        $entries = ChatEntry::query()
            ->with('translations:id,chat_entry_id,locale,question')
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        $byParent = [];

        foreach ($entries as $entry) {
            $byParent[(int) $entry->parent_id][] = $entry;
        }

        $rows = [];
        $this->walk($byParent, 0, 0, '', $rows);

        return $rows;
    }

    /**
     * @param  array<int, list<ChatEntry>>  $byParent
     * @param  list<Row>  $rows
     */
    private function walk(array $byParent, int $parent, int $depth, string $prefix, array &$rows): void
    {
        $siblings = $byParent[$parent] ?? [];
        $lastIndex = count($siblings) - 1;

        foreach ($siblings as $index => $entry) {
            $rows[] = [
                'entry' => $entry,
                'depth' => $depth,
                'number' => $number = $prefix.($index + 1),
                'label' => $this->label($entry),
                'locales' => $this->locales($entry),
                'first' => $index === 0,
                'last' => $index === $lastIndex,
            ];

            $this->walk($byParent, $entry->id, $depth + 1, $number.'.', $rows);
        }
    }

    /**
     * The question in the first language that has one, in landing order.
     */
    private function label(ChatEntry $entry): string
    {
        foreach (config()->array('locales.landing') as $locale) {
            $translation = $entry->translations->firstWhere('locale', $locale);

            if ($translation !== null) {
                return $translation->question;
            }
        }

        return '—';
    }

    /**
     * @return list<string>
     */
    private function locales(ChatEntry $entry): array
    {
        $filled = $entry->translations->pluck('locale')->all();

        return array_values(array_filter(
            array_map('strval', config()->array('locales.landing')),
            fn (string $locale): bool => in_array($locale, $filled, true),
        ));
    }
}
