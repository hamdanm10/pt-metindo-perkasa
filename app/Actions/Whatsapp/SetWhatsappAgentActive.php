<?php

declare(strict_types=1);

namespace App\Actions\Whatsapp;

use App\Models\WhatsappAgent;

/**
 * Turns a number on or off on the landing without deleting it.
 */
class SetWhatsappAgentActive
{
    public function execute(WhatsappAgent $agent, bool $active): void
    {
        $agent->update(['is_active' => $active]);
    }
}
