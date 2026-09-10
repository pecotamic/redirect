<?php

namespace Tests;

use Pecotamic\Redirect\Data\Data;
use Statamic\CP\Navigation\Nav as RealNav;

class RedirectsNavTest extends TestCase
{
    public function test_it_moves_the_redirects_collection_out_of_the_default_collections_list(): void
    {
        Data::setup();

        $nav = new RealNav;
        $nav->content('Collections')->children(fn () => collect([
            $nav->item('Redirects'),
            $nav->item('Blog Posts'),
        ]));

        ($this->capturedNavExtension)($nav);

        $collections = collect($nav->items())->first(fn ($item) => $item->section() === 'Content' && $item->display() === 'Collections');

        $this->assertSame(['Blog Posts'], $collections->children()->map->display()->all());
    }

    public function test_it_adds_a_top_level_nav_item_pointing_to_the_collection(): void
    {
        Data::setup();

        $nav = new RealNav;
        ($this->capturedNavExtension)($nav);

        $redirects = collect($nav->items())
            ->first(fn ($item) => $item->section() === 'Content' && $item->display() === 'Redirects' && ! $item->isChild());

        $this->assertNotNull($redirects);
        $this->assertSame(cp_route('collections.show', 'redirects'), $redirects->url());
    }
}
