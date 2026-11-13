<?php

declare(strict_types=1);

namespace App\Services\Landing;

use App\Models\WhatsappAgent;
use Illuminate\Support\Facades\Cache;

/**
 * The WhatsApp customer service numbers of the floating button, as the admin set them in the
 * dashboard: active numbers only, in the admin's order. No numbers, no button. Cached and cleared
 * whenever a number changes (see `WhatsappAgent`).
 *
 * @phpstan-type Agent array{name: string, number: string, display: string}
 */
class LandingContact
{
    private const KEY = 'landing.whatsapp';

    /**
     * @return list<Agent>
     */
    public function agents(): array
    {
        /** @var list<Agent> $agents */
        $agents = Cache::rememberForever(self::KEY, fn (): array => WhatsappAgent::query()
            ->where('is_active', true)
            ->orderBy('position')
            ->orderBy('id')
            ->get(['name', 'number'])
            ->map(fn (WhatsappAgent $agent): array => ['name' => $agent->name, 'number' => $agent->number, 'display' => $this->display($agent->number)])
            ->all());

        return $agents;
    }

    public function flush(): void
    {
        Cache::forget(self::KEY);
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
