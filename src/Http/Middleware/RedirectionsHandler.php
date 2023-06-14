<?php

namespace Pecotamic\Redirect\Http\Middleware;

use Closure;
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
            http_response_code((int) $redirect->responseCode());

            if (in_array((int) $redirect->responseCode(), [301, 302])) {
                header('Location: '.$redirect->target());
            }

            exit();
        }

        return $next($request);
    }
}
