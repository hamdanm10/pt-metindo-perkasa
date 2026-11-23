<?php

declare(strict_types=1);

namespace App\Actions\Processes;

use App\Models\Process;
use App\Services\Landing\LandingProcesses;

/**
 * Deletes a process with its translations and steps (the database cascades).
 */
class DeleteProcess
{
    public function __construct(private readonly LandingProcesses $landing) {}

    public function execute(Process $process): void
    {
        $process->delete();

        $this->landing->flush();
    }
}
