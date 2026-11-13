<?php

declare(strict_types=1);

namespace App\Actions\Whatsapp;

use App\Models\WhatsappAgent;

/**
 * Saves a WhatsApp number. A new one goes last in the admin's order and starts active.
 */
class SaveWhatsappAgent
{
    public function execute(?WhatsappAgent $agent, string $name, string $number): WhatsappAgent
    {
        if ($agent === null) {
            $last = WhatsappAgent::query()->max('position');

            $agent = new WhatsappAgent(['position' => is_numeric($last) ? (int) $last + 1 : 0, 'is_active' => true]);
        }

        $agent->fill(['name' => $name, 'number' => $number])->save();

        return $agent;
    }
}
