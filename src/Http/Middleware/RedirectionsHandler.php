<?php

namespace Pecotamic\Redirect\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Pecotamic\Redirect\Data\Data;
use Statamic\Support\Str;

class RedirectionsHandler
{
    public function handle(Request $request, Closure $next)
    {
        $url = Str::start($request->getRequestUri(), '/');
        $url = Str::substr(Str::finish($url, '/'), 0, -1);

        if ($redirect = Data::get($request)->redirectMatching(url: $url)) {
            $responseCode = (int) $redirect->responseCode();

            if (in_array($responseCode, [301, 302])) {
                return new RedirectResponse($redirect->target(), $responseCode);
            }

            abort($responseCode);
        }

        return $next($request);
    }
}
