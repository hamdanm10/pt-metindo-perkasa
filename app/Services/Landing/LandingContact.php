<?php

declare(strict_types=1);

namespace App\Services\Landing;

/**
 * The WhatsApp customer service numbers of the floating button. They come from
 * `landing.contact.whatsapp_agents` ("Name|628123;Name 2|628456"), or from the single
 * `landing.contact.whatsapp` number when the list is empty. No numbers, no button.
 *
 * @phpstan-type Agent array{name: string, number: string, display: string}
 */
class LandingContact
{
    /**
     * @return list<Agent>
     */
    public function agents(): array
    {
        $agents = [];

        foreach (explode(';', (string) config('landing.contact.whatsapp_agents')) as $entry) {
            $parts = explode('|', $entry, 2);
            $number = preg_replace('/\D+/', '', $parts[1] ?? $parts[0]) ?? '';

            if ($number === '') {
                continue;
            }

            $name = count($parts) === 2 ? trim($parts[0]) : '';

            $agents[] = ['name' => $name !== '' ? $name : __('chat.whatsapp.default_name'), 'number' => $number, 'display' => $this->display($number)];
        }

        if ($agents === []) {
            $single = preg_replace('/\D+/', '', (string) config('landing.contact.whatsapp')) ?? '';

            if ($single !== '') {
                $agents[] = ['name' => __('chat.whatsapp.default_name'), 'number' => $single, 'display' => $this->display($single)];
            }
        }

        return $agents;
    }

    /** A chat link for a number, with the greeting already typed in. */
    public function url(string $number): string
    {
        return 'https://wa.me/'.$number.'?text='.rawurlencode(__('chat.whatsapp.message'));
    }

    private function display(string $number): string
    {
        return '+'.$number;
    }
}
