<?php

namespace Huement\StatComm\Tests;

use Huement\StatComm\ServiceProvider;
use Statamic\Testing\AddonTestCase;
use Livewire\LivewireServiceProvider;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;

    /**
     * Load core package service providers into the Testbench sandbox container.
     */
    protected function getPackageProviders($app): array
    {
        return array_merge(parent::getPackageProviders($app), [
            LivewireServiceProvider::class,
        ]);
    }

    /**
     * Define environment variables and config values for the test sandbox container.
     */
    protected function getEnvironmentSetUp($app): void
    {
        // Inject a dummy 32-byte base64 encryption key so Livewire can compile its views securely
        $app['config']->set('app.key', 'base64:yN9qS1M9v9bXvW2z6R5tY1u2I3o4P5e6A7S8D9f0G1h=');

        // ⚡ FIX: Force the sandbox view engine to map the "statcomm" template namespace
        $app['view']->addNamespace('statcomm', realpath(__DIR__ . '/../resources/views'));
    }
}
