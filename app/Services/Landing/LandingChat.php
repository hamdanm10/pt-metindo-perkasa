<?php

declare(strict_types=1);

namespace App\Services\Landing;

/**
 * The rules of the rule-based chat assistant, in the active language. Every topic has a label for
 * its quick button, keywords that a typed question is matched against, and an answer built only
 * from what the site already says (the number of process categories, the contact details in the
 * configuration). Topics whose data is missing are left out. Nothing here is generated: the
 * browser only picks the topic whose keywords appear in the question.
 *
 * @phpstan-type Topic array{id: string, label: string, answer: string, keywords: list<string>, link: array{label: string, url: string}|null}
 */
class LandingChat
{
    public function __construct(private readonly LandingContent $content) {}

    /**
     * @return list<Topic>
     */
    public function topics(): array
    {
        $contact = config()->array('landing.contact');
        $email = $this->text($contact['email'] ?? null);
        $phone = $this->text($contact['phone'] ?? null);
        $hours = $this->text($contact['hours'] ?? null);
        $address = $this->text($contact['address'] ?? null);
        $categories = count($this->content->categories());

        $topics = [
            $this->topic('processes', $this->line('chat.topics.processes.answer', ['count' => $categories]), 'landing.processes.index'),
            $this->topic('services', $this->line('chat.topics.services.answer'), 'landing.services'),
            $this->topic('quote', $this->line('chat.topics.quote.answer'), 'landing.contact'),
        ];

        $lines = array_values(array_filter([
            $email !== null ? $this->line('chat.topics.contact.email', ['email' => $email]) : null,
            $phone !== null ? $this->line('chat.topics.contact.phone', ['phone' => $phone]) : null,
        ]));

        if ($lines !== []) {
            $topics[] = $this->topic('contact', implode(' ', $lines), 'landing.contact');
        }

        if ($hours !== null) {
            $topics[] = $this->topic('hours', $this->line('chat.topics.hours.answer', ['hours' => $hours]), 'landing.contact');
        }

        if ($address !== null) {
            $topics[] = $this->topic('location', $this->line('chat.topics.location.answer', ['address' => $address]), 'landing.about');
        }

        $topics[] = $this->topic('company', $this->line('chat.topics.company.answer'), 'landing.about');

        return $topics;
    }

    /**
     * @return Topic
     */
    private function topic(string $id, string $answer, string $route): array
    {
        $keywords = trans("chat.topics.{$id}.keywords");

        return [
            'id' => $id,
            'label' => $this->line("chat.topics.{$id}.label"),
            'answer' => $answer,
            'keywords' => is_array($keywords) ? array_values(array_map(fn (mixed $word): string => mb_strtolower((string) $word), $keywords)) : [],
            'link' => ['label' => $this->line("chat.topics.{$id}.link"), 'url' => route($route)],
        ];
    }

    /**
     * A translated line. A missing key or a list instead of a line gives an empty string.
     *
     * @param  array<string, string|int>  $replace
     */
    private function line(string $key, array $replace = []): string
    {
        $text = __($key, $replace);

        return is_string($text) ? $text : '';
    }

    private function text(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }
}
