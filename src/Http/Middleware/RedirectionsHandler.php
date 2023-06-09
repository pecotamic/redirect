<?php

namespace Pecotamic\Redirects\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Pecotamic\Redirects\Data\Data;
use Statamic\Support\Str;

class RedirectionsHandler
{
    public function handle(Request $request, Closure $next)
    {
        $url = Str::start($request->getRequestUri(), '/');
        $url = Str::substr(Str::finish($url, '/'), 0, -1);

        if ($redirect = Data::get($request)->redirectMatching(url: $url)) {
            if (in_array((int) $redirect->responseCode(), [301, 302])) {
                return redirect($redirect->target(), (int) $redirect->responseCode());
            }

            abort((int) $redirect->responseCode());
        }

        return $next($request);
    }
}
