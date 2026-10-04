<?php

namespace Pecotamic\Redirect\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Redirects are matched against the request's path (plus query string) only,
 * never its scheme or host, so a source given as an absolute or
 * protocol-relative URL could never match anything.
 */
class RequestUriPath implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match('/^\/(?!\/)\S*$/', $value)) {
            $fail('redirect::messages.request_uri_must_be_path')->translate();
        }
    }
}
