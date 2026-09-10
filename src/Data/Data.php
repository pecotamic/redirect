<?php

declare(strict_types=1);

namespace Pecotamic\Redirect\Data;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Pecotamic\Redirect\Blueprints\RedirectBlueprint;
use Statamic\Events\EntryBlueprintFound;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Collection as CollectionAPI;
use Statamic\Facades\CP\Nav;
use Statamic\Facades\Site;
use Statamic\Support\Str;

class Data
{
    private const COLLECTION_HANDLE = 'redirects';

    private ?array $exactRedirects = null;

    private ?array $prefixRedirects = null;

    public function __construct(private Collection $data) {}

    public function redirects(): \Generator
    {
        foreach ($this->data as $data) {
            yield new Redirect($data);
        }
    }

    public static function get(Request $request)
    {
        $site = Site::get($request->site ?? '') ?? Site::selected();

        $entries = CollectionAPI::findByHandle(self::COLLECTION_HANDLE)
            ->queryEntries()
            ->where('site', $site->handle())
            ->where('published', true)
            ->get()
            ->map(fn ($entry) => $entry->data()->only(['request_uri', 'match_type', 'response_code', 'target'])->all());

        return new self($entries);
    }

    public static function setup()
    {
        if (! CollectionAPI::findByHandle(self::COLLECTION_HANDLE)) {
            CollectionAPI::make(self::COLLECTION_HANDLE)
                ->title('Redirects')
                ->sites(Site::all()->map->handle())
                ->requiresSlugs(false)
                ->titleFormats('{{ request_uri }}')
                ->save();
        }

        if (! Blueprint::find('collections.'.self::COLLECTION_HANDLE.'.redirect')) {
            RedirectBlueprint::make()
                ->setHandle('redirect')
                ->setNamespace('collections.'.self::COLLECTION_HANDLE)
                ->save();
        }

        // The entry blueprint is persisted to disk (Statamic discovers collection
        // entry blueprints purely via a directory scan, with no fallback-closure
        // mechanism like globals have). Refresh its field labels on every request
        // so they still follow the current locale instead of staying frozen at
        // whatever they were when the file was first written.
        Event::listen(EntryBlueprintFound::class, function (EntryBlueprintFound $event) {
            if (
                $event->blueprint->namespace() === 'collections.'.self::COLLECTION_HANDLE
                && $event->blueprint->handle() === 'redirect'
            ) {
                $event->blueprint->setContents(RedirectBlueprint::make()->contents());
            }
        });

        // Give the collection its own top-level nav entry instead of leaving it
        // in the general "Collections" list, where it would sit alongside actual
        // content collections like blog posts or pages.
        Nav::extend(function ($nav) {
            $nav->remove('Content', 'Collections', 'Redirects');

            $nav->content('Redirects')
                ->route('collections.show', self::COLLECTION_HANDLE)
                ->icon('link')
                ->can('view', CollectionAPI::findByHandle(self::COLLECTION_HANDLE));
        });
    }

    public function redirectMatching(string $url): ?Redirect
    {
        $this->indexRedirects();

        if ($redirect = $this->exactRedirects[Str::lower($url)] ?? null) {
            return $redirect;
        }

        foreach ($this->prefixRedirects as $redirect) {
            if (Str::startsWith($url, $redirect->requestUri())) {
                return $redirect;
            }
        }

        return null;
    }

    private function indexRedirects(): void
    {
        if ($this->exactRedirects !== null) {
            return;
        }

        $this->exactRedirects = [];
        $this->prefixRedirects = [];

        foreach ($this->redirects() as $redirect) {
            match ($redirect->matchType()) {
                Redirect::MATCH_TYPE_EXACT => $this->exactRedirects[Str::lower($redirect->requestUri())] = $redirect,
                Redirect::MATCH_TYPE_STARTS_WITH => $this->prefixRedirects[] = $redirect,
            };
        }
    }
}
