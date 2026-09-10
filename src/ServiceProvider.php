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

            Data::setup();
        });
    }
}
