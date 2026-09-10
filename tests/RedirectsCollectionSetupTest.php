<?php

namespace Tests;

use Illuminate\Support\Facades\App;
use Pecotamic\Redirect\Data\Data;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Collection;

class RedirectsCollectionSetupTest extends TestCase
{
    public function test_it_creates_the_redirects_collection(): void
    {
        Data::setup();

        $collection = Collection::findByHandle('redirects');

        $this->assertNotNull($collection);
        $this->assertFalse($collection->requiresSlugs());
        $this->assertTrue($collection->autoGeneratesTitles());
    }

    public function test_it_registers_a_discoverable_entry_blueprint(): void
    {
        Data::setup();

        $collection = Collection::findByHandle('redirects');

        $this->assertNotNull(Blueprint::find('collections.redirects.redirect'));

        $entryBlueprints = $collection->entryBlueprints();
        $this->assertCount(1, $entryBlueprints);
        $this->assertSame('redirect', $entryBlueprints->first()->handle());
    }

    public function test_setup_is_idempotent(): void
    {
        Data::setup();
        Data::setup();

        $this->assertNotNull(Collection::findByHandle('redirects'));
    }

    public function test_entry_blueprint_labels_follow_the_german_locale(): void
    {
        App::setLocale('de');
        Data::setup();

        $this->assertSame('Request-URI', $this->requestUriFieldDisplay());
    }

    public function test_entry_blueprint_labels_follow_the_english_locale(): void
    {
        App::setLocale('en');
        Data::setup();

        $this->assertSame('Request URI', $this->requestUriFieldDisplay());
    }

    private function requestUriFieldDisplay(): string
    {
        return Collection::findByHandle('redirects')
            ->entryBlueprint('redirect')
            ->fields()->all()->get('request_uri')
            ->display();
    }
}
