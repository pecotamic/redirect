<?php

namespace Pecotamic\Redirect\Listeners;

use Pecotamic\Redirect\Data\Data;
use Pecotamic\Redirect\Data\Redirect;
use Statamic\Contracts\Entries\Entry;
use Statamic\Events\EntrySaved;
use Statamic\StaticCaching\Cacher;

/**
 * Statamic only invalidates an entry's own URL when it is saved, and redirect
 * entries have none (the collection has no route). A page that was statically
 * cached under a redirect's source path would therefore keep being served —
 * with full-measure caching straight from the web server, before the redirect
 * middleware ever runs. So drop that path (or, for "starts with" rules, every
 * cached path under it) from the static cache whenever a redirect is saved.
 */
class InvalidateStaticCache
{
    public function __construct(private Cacher $cacher) {}

    public function handle(EntrySaved $event): void
    {
        $entry = $event->entry;

        if ($entry->collectionHandle() !== Data::COLLECTION_HANDLE || ! $entry->published()) {
            return;
        }

        $this->cacher->invalidateUrls([$this->cachedUrl($entry)]);
    }

    private function cachedUrl(Entry $entry): string
    {
        // request_uri already holds the full request path, including any
        // subdirectory a site is mounted under, so only take the site's origin.
        $siteUrl = parse_url($entry->site()->absoluteUrl());
        $origin = $siteUrl['scheme'].'://'.$siteUrl['host'].(isset($siteUrl['port']) ? ':'.$siteUrl['port'] : '');

        $url = $origin.$entry->get('request_uri');

        return $entry->get('match_type') === Redirect::MATCH_TYPE_STARTS_WITH ? $url.'*' : $url;
    }
}
