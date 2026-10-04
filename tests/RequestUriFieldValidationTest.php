<?php

namespace Tests;

use Illuminate\Support\Facades\App;
use Pecotamic\Redirect\Blueprints\RedirectBlueprint;
use PHPUnit\Framework\Attributes\DataProvider;

class RequestUriFieldValidationTest extends TestCase
{
    #[DataProvider('validRequestUris')]
    public function test_it_accepts_paths(string $requestUri): void
    {
        $this->assertFalse($this->validate($requestUri)->errors()->has('request_uri'));
    }

    #[DataProvider('invalidRequestUris')]
    public function test_it_rejects_anything_but_a_path(?string $requestUri): void
    {
        $this->assertTrue($this->validate($requestUri)->errors()->has('request_uri'));
    }

    public function test_the_error_message_is_localized(): void
    {
        App::setLocale('de');
        $german = $this->validate('https://example.com/foo')->errors()->first('request_uri');

        App::setLocale('en');
        $english = $this->validate('https://example.com/foo')->errors()->first('request_uri');

        $this->assertSame(__('redirect::messages.request_uri_must_be_path', [], 'de'), $german);
        $this->assertSame(__('redirect::messages.request_uri_must_be_path', [], 'en'), $english);
        $this->assertNotSame($german, $english);
    }

    public static function validRequestUris(): array
    {
        return [
            'root' => ['/'],
            'simple path' => ['/old-page'],
            'nested path' => ['/blog/2024/old-post'],
            'path with query string' => ['/search?q=foo'],
        ];
    }

    public static function invalidRequestUris(): array
    {
        return [
            'missing' => [null],
            'absolute https url' => ['https://example.com/old-page'],
            'absolute http url' => ['http://example.com/old-page'],
            'protocol-relative url' => ['//example.com/old-page'],
            'host without scheme' => ['example.com/old-page'],
            'path without leading slash' => ['old-page'],
            'path containing whitespace' => ['/old page'],
        ];
    }

    private function validate(?string $requestUri)
    {
        return RedirectBlueprint::make()
            ->fields()
            ->addValues(['request_uri' => $requestUri, 'match_type' => 'exact', 'response_code' => 404])
            ->validator()
            ->validator();
    }
}
