<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Fortify\Features;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();

        $environment = $app->environment();
        $connection = $app['config']->get('database.default');
        $database = $app['config']->get(
            "database.connections.{$connection}.database"
        );

        if (
            $environment !== 'testing' ||
            ! is_string($database) ||
            ! str_ends_with($database, '_test')
        ) {
            throw new RuntimeException(
                "Unsafe test database [{$database}]. "
                .'Tests must run in the testing environment '
                .'and use a database ending in _test.'
            );
        }

        return $app;
    }

    protected function skipUnlessFortifyHas(
        string $feature,
        ?string $message = null
    ): void {
        if (! Features::enabled($feature)) {
            $this->markTestSkipped(
                $message ?? "Fortify feature [{$feature}] is not enabled."
            );
        }
    }
}
