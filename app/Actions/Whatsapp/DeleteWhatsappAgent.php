<?php

declare(strict_types=1);

namespace App\Actions\Whatsapp;

use App\Models\WhatsappAgent;

class DeleteWhatsappAgent
{
    public function execute(WhatsappAgent $agent): void
    {
        $agent->delete();
    }
}
