<?php

namespace Pecotamic\Redirects\Data;

use Statamic\Support\Traits\FluentlyGetsAndSets;

class Redirect
{
    public const MATCH_TYPE_EXACT = 'exact';

    public const MATCH_TYPE_STARTS_WITH = 'starts_with';

    use FluentlyGetsAndSets;

    protected string $requestUri;

    protected string $matchType = self::MATCH_TYPE_EXACT;

    protected int $responseCode = 301;

    protected string|null $target;

    public function __construct(array $data)
    {
        $this->requestUri($data['request_uri'])
            ->matchType($data['match_type'])
            ->responseCode($data['response_code'])
            ->target($data['target'] ?? null);
    }

    public function requestUri($requestUri = null)
    {
        return $this->fluentlyGetOrSet('requestUri')->args(func_get_args());
    }

    public function matchType($matchType = null)
    {
        return $this->fluentlyGetOrSet('matchType')->args(func_get_args());
    }

    public function responseCode($responseCode = null)
    {
        return $this->fluentlyGetOrSet('responseCode')->args(func_get_args());
    }

    public function target($target = null)
    {
        return $this->fluentlyGetOrSet('target')->args(func_get_args());
    }
}
