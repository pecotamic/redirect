<?php

namespace Tests;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Pecotamic\Redirect\Data\Data;
use Pecotamic\Redirect\Http\Middleware\RedirectionsHandler;
use Statamic\Facades\GlobalSet;
use Statamic\Facades\Site;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RedirectionsHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Data::setup();
    }

    public function test_it_returns_a_redirect_response_for_301(): void
    {
        $this->setRedirects([
            $this->redirect('/old', 'exact', 301, '/new'),
        ]);

        $response = $this->handle('/old');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame('/new', $response->getTargetUrl());
    }

    public function test_it_aborts_with_the_configured_status_code(): void
    {
        $this->setRedirects([
            $this->redirect('/gone', 'exact', 410, null),
        ]);

        try {
            $this->handle('/gone');
            $this->fail('Expected an HttpException to be thrown.');
        } catch (HttpException $e) {
            $this->assertSame(410, $e->getStatusCode());
        }
    }

    public function test_it_passes_through_to_next_when_nothing_matches(): void
    {
        $this->setRedirects([]);

        $result = $this->handle('/unmatched');

        $this->assertSame('next-called', $result);
    }

    private function handle(string $uri): mixed
    {
        $handler = new RedirectionsHandler;
        $request = Request::create($uri);

        return $handler->handle($request, fn ($req) => 'next-called');
    }

    private function setRedirects(array $redirects): void
    {
        GlobalSet::findByHandle('pecotamic_redirects')
            ->in(Site::default()->handle())
            ->data(['redirects' => $redirects])
            ->save();
    }

    private function redirect(string $requestUri, string $matchType, int $responseCode, ?string $target): array
    {
        return [
            'enabled' => true,
            'request_uri' => $requestUri,
            'match_type' => $matchType,
            'response_code' => $responseCode,
            'target' => $target,
        ];
    }
}
