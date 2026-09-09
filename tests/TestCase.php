<?php

namespace Tests;

use Closure;
use Pecotamic\Redirect\ServiceProvider;
use Statamic\Facades\CP\Nav;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;

    /**
     * The closure Data::setup() passes to Nav::extend(), captured here since
     * AddonTestCase mocks the CP\Nav facade wholesale (only build()/
     * clearCachedUrls() are expected by default) and would otherwise fail any
     * call to extend() with no expectation set.
     */
    protected ?Closure $capturedNavExtension = null;

    protected function setUp(): void
    {
        parent::setUp();

        Nav::shouldReceive('extend')->andReturnUsing(function (Closure $callback) {
            $this->capturedNavExtension = $callback;
        });
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    }
}
