<?php

namespace Tests;

use Pecotamic\Redirect\Data\Data;
use Statamic\Facades\Entry;
use Statamic\Facades\GlobalSet;
use Statamic\Facades\Site;

class MigrateRedirectsCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Data::setup();
    }

    protected function tearDown(): void
    {
        Entry::query()->where('collection', 'redirects')->get()->each->delete();

        GlobalSet::findByHandle('pecotamic_redirects')?->delete();

        parent::tearDown();
    }

    public function test_it_migrates_redirects_from_the_old_global_set(): void
    {
        $this->createLegacyGlobalSet([
            ['enabled' => true, 'request_uri' => '/old', 'match_type' => 'exact', 'response_code' => 301, 'target' => '/new'],
            ['enabled' => false, 'request_uri' => '/gone', 'match_type' => 'exact', 'response_code' => 404, 'target' => null],
        ]);

        $this->artisan('pecotamic:redirects:migrate')->assertExitCode(0);

        $entries = Entry::query()->where('collection', 'redirects')->get()->keyBy->get('request_uri');

        $this->assertCount(2, $entries);
        $this->assertTrue($entries['/old']->published());
        $this->assertFalse($entries['/gone']->published());
        $this->assertSame('/new', $entries['/old']->get('target'));
    }

    public function test_it_deletes_the_old_global_set_after_migrating(): void
    {
        $this->createLegacyGlobalSet([
            ['enabled' => true, 'request_uri' => '/old', 'match_type' => 'exact', 'response_code' => 301, 'target' => '/new'],
        ]);

        $this->artisan('pecotamic:redirects:migrate')->assertExitCode(0);

        $this->assertNull(GlobalSet::findByHandle('pecotamic_redirects'));
    }

    public function test_it_is_safe_to_run_twice(): void
    {
        $this->createLegacyGlobalSet([
            ['enabled' => true, 'request_uri' => '/old', 'match_type' => 'exact', 'response_code' => 301, 'target' => '/new'],
        ]);

        $this->artisan('pecotamic:redirects:migrate')->run();
        $this->artisan('pecotamic:redirects:migrate')->run();

        $this->assertCount(1, Entry::query()->where('collection', 'redirects')->get());
    }

    public function test_it_does_nothing_when_no_legacy_global_set_exists(): void
    {
        $this->artisan('pecotamic:redirects:migrate')->assertExitCode(0);

        $this->assertCount(0, Entry::query()->where('collection', 'redirects')->get());
    }

    private function createLegacyGlobalSet(array $redirects): void
    {
        $globalSet = GlobalSet::make('pecotamic_redirects')->title('Redirects')->save();

        $globalSet->makeLocalization(Site::default()->handle())
            ->data(['redirects' => $redirects])
            ->save();
    }
}
