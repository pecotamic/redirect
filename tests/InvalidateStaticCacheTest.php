<?php

namespace Tests;

use Mockery\MockInterface;
use Pecotamic\Redirect\Data\Data;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;
use Statamic\StaticCaching\Cacher;

class InvalidateStaticCacheTest extends TestCase
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

    public function test_it_invalidates_the_source_path_of_an_exact_redirect(): void
    {
        $this->expectInvalidatedUrls(['http://localhost/old-page']);

        $this->saveRedirect('/old-page', 'exact');
    }

    public function test_it_invalidates_every_path_under_a_starts_with_redirect(): void
    {
        $this->expectInvalidatedUrls(['http://localhost/blog*']);

        $this->saveRedirect('/blog', 'starts_with');
    }

    public function test_it_ignores_unpublished_redirects(): void
    {
        $this->mock(Cacher::class, fn (MockInterface $mock) => $mock->shouldNotReceive('invalidateUrls'));

        $this->saveRedirect('/old-page', 'exact', published: false);
    }

    public function test_it_ignores_entries_of_other_collections(): void
    {
        $this->mock(Cacher::class, fn (MockInterface $mock) => $mock->shouldNotReceive('invalidateUrls'));

        Collection::make('pages')->save();
        Entry::make()->collection('pages')->locale(Site::default()->handle())->data(['request_uri' => '/x'])->save();
        Entry::query()->where('collection', 'pages')->get()->each->delete();
        Collection::findByHandle('pages')->delete();
    }

    private function expectInvalidatedUrls(array $urls): void
    {
        $this->mock(Cacher::class, fn (MockInterface $mock) => $mock->shouldReceive('invalidateUrls')->once()->with($urls));
    }

    private function saveRedirect(string $requestUri, string $matchType, bool $published = true): void
    {
        Entry::make()
            ->collection('redirects')
            ->locale(Site::default()->handle())
            ->published($published)
            ->data([
                'request_uri' => $requestUri,
                'match_type' => $matchType,
                'response_code' => 301,
                'target' => '/new',
            ])
            ->save();
    }
}
