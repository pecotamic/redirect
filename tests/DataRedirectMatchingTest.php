<?php

namespace Tests;

use Illuminate\Support\Collection;
use Pecotamic\Redirect\Data\Data;

class DataRedirectMatchingTest extends TestCase
{
    public function test_it_matches_an_exact_request_uri(): void
    {
        $data = $this->dataWith([
            $this->redirect(requestUri: '/old', matchType: 'exact', target: '/new'),
        ]);

        $redirect = $data->redirectMatching('/old');

        $this->assertNotNull($redirect);
        $this->assertSame('/new', $redirect->target());
    }

    public function test_exact_match_is_case_insensitive(): void
    {
        $data = $this->dataWith([
            $this->redirect(requestUri: '/Old', matchType: 'exact', target: '/new'),
        ]);

        $this->assertNotNull($data->redirectMatching('/old'));
    }

    public function test_it_matches_a_starts_with_prefix(): void
    {
        $data = $this->dataWith([
            $this->redirect(requestUri: '/blog', matchType: 'starts_with', target: '/news'),
        ]);

        $redirect = $data->redirectMatching('/blog/2020/post');

        $this->assertNotNull($redirect);
        $this->assertSame('/news', $redirect->target());
    }

    public function test_exact_match_takes_priority_over_an_earlier_starts_with_match(): void
    {
        $data = $this->dataWith([
            $this->redirect(requestUri: '/old', matchType: 'starts_with', target: '/prefix-target'),
            $this->redirect(requestUri: '/old', matchType: 'exact', target: '/exact-target'),
        ]);

        $redirect = $data->redirectMatching('/old');

        $this->assertSame('/exact-target', $redirect->target());
    }

    public function test_it_returns_null_when_nothing_matches(): void
    {
        $data = $this->dataWith([
            $this->redirect(requestUri: '/old', matchType: 'exact', target: '/new'),
        ]);

        $this->assertNull($data->redirectMatching('/other'));
    }

    private function dataWith(array $redirects): Data
    {
        return new Data(new Collection($redirects));
    }

    private function redirect(string $requestUri, string $matchType, ?string $target, int $responseCode = 301): array
    {
        return [
            'request_uri' => $requestUri,
            'match_type' => $matchType,
            'response_code' => $responseCode,
            'target' => $target,
        ];
    }
}
