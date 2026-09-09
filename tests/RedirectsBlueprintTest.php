<?php

namespace Tests;

use Illuminate\Support\Facades\App;
use Pecotamic\Redirect\Data\Data;
use Statamic\Facades\Blueprint;

class RedirectsBlueprintTest extends TestCase
{
    public function test_field_labels_follow_the_german_locale(): void
    {
        App::setLocale('de');

        $this->assertSame('Weiterleitungen', $this->fieldDisplay('redirects'));
    }

    public function test_field_labels_follow_the_english_locale(): void
    {
        App::setLocale('en');

        $this->assertSame('Redirects', $this->fieldDisplay('redirects'));
    }

    public function test_blueprint_is_never_persisted_to_disk(): void
    {
        Data::setup();

        $this->assertFileDoesNotExist(Blueprint::find('globals.pecotamic_redirects')->path());
    }

    private function fieldDisplay(string $handle): string
    {
        Data::setup();

        return Blueprint::find('globals.pecotamic_redirects')->fields()->all()->get($handle)->display();
    }
}
