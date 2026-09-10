<?php

namespace Tests;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Pecotamic\Redirect\Data\Data;
use Pecotamic\Redirect\Http\Middleware\RedirectionsHandler;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RedirectionsHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Data::setup();
    }

    protected function tearDown(): void
    {
        Entry::query()->where('collection', 'redirects')->get()->each->delete();

        parent::tearDown();
    }

    public function test_it_returns_a_redirect_response_for_301(): void
    {
        $this->createRedirect('/old', 'exact', 301, '/new');

        $response = $this->handle('/old');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame('/new', $response->getTargetUrl());
    }

    public function test_it_aborts_with_the_configured_status_code(): void
    {
        $this->createRedirect('/gone', 'exact', 410, null);

        try {
            $this->handle('/gone');
            $this->fail('Expected an HttpException to be thrown.');
        } catch (HttpException $e) {
            $this->assertSame(410, $e->getStatusCode());
        }
    }

    public function test_it_passes_through_to_next_when_nothing_matches(): void
    {
        $result = $this->handle('/unmatched');

        $this->assertSame('next-called', $result);
    }

    public function test_it_ignores_unpublished_redirects(): void
    {
        $this->createRedirect('/old', 'exact', 301, '/new', published: false);

        $result = $this->handle('/old');

        $this->assertSame('next-called', $result);
    }

    private function handle(string $uri): mixed
    {
        $handler = new RedirectionsHandler;
        $request = Request::create($uri);

        return $handler->handle($request, fn ($req) => 'next-called');
    }

    private function createRedirect(string $requestUri, string $matchType, int $responseCode, ?string $target, bool $published = true): void
    {
        Entry::make()
            ->collection('redirects')
            ->locale(Site::default()->handle())
            ->published($published)
            ->data([
                'request_uri' => $requestUri,
                'match_type' => $matchType,
                'response_code' => $responseCode,
                'target' => $target,
            ])
            ->save();
    }
}
