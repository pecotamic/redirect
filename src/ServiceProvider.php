<?php

namespace Pecotamic\Redirect;

use Pecotamic\Redirect\Data\Data;
use Pecotamic\Redirect\Http\Middleware\RedirectionsHandler;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;

class ServiceProvider extends AddonServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        Statamic::booted(function () {
            app('router')->prependMiddlewareToGroup('statamic.web', RedirectionsHandler::class);
            app('router')->prependMiddlewareToGroup('web', RedirectionsHandler::class);

            return $this->bootGlobalSet();
        });
    }

    public function globalSetHandle(): string
    {
        return 'pecotamic_redirects';
    }

    public function bootGlobalSet(): self
    {
        Data::setup();

        return $this;
    }
}
