<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class DatabaseSafetyTest extends TestCase
{
    public function test_tests_run_on_a_database_ending_with_testing(): void
    {
        $database = config('database.connections.'.config('database.default').'.database');

        $this->assertSame('pgsql', config('database.default'));
        $this->assertStringEndsWith('_testing', (string) $database);
    }
}
