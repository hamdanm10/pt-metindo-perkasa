<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $database = (string) config('database.connections.'.config('database.default').'.database');

        if (! str_ends_with($database, '_testing')) {
            throw new RuntimeException("Refusing to run tests on database [{$database}]: its name must end with _testing.");
        }
    }
}
