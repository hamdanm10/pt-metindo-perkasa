<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Landing\LandingServices;
use App\Services\Services\DefaultServices;
use Illuminate\Console\Command;

class ImportDefaultServices extends Command
{
    protected $signature = 'services:import-defaults';

    protected $description = 'Add the three services the landing used to show to the database (existing addresses are skipped)';

    public function handle(DefaultServices $defaults, LandingServices $landing): int
    {
        $added = $defaults->import();
        $landing->flush();

        $this->info("Added {$added} service(s).");

        return self::SUCCESS;
    }
}
