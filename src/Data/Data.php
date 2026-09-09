<?php

declare(strict_types=1);

namespace Pecotamic\Redirect\Data;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Pecotamic\Redirect\Blueprints\RedirectsBlueprint;
use Statamic\Facades\Blueprint;
use Statamic\Facades\GlobalSet;
use Statamic\Facades\Site;
use Statamic\Support\Str;

class Data
{
    private const HANDLE = 'pecotamic_redirects';

    private array|null $exactRedirects = null;

    private array|null $prefixRedirects = null;

    public function __construct(private Collection $data)
    {
    }

    public function redirects(): \Generator
    {
        foreach ($this->data['redirects'] ?? [] as $data) {
            if ($data['enabled'] ?? true) {
                yield new Redirect($data);
            }
        }
    }

    public static function get(Request $request)
    {
        $site = Site::get($request->site ?? '') ?? Site::selected();

        return new self(GlobalSet::findByHandle(self::HANDLE)
            ->localizations()[$site->handle()]->data());
    }

    public static function setup()
    {
        if (! GlobalSet::findByHandle(self::HANDLE)) {
            GlobalSet::make(self::HANDLE)
                ->title(__('redirect::messages.global_set_title'))
                ->makeLocalization(Site::default()->handle())
                ->save();
        }

        if (! Blueprint::find('globals.'.self::HANDLE)) {
            RedirectsBlueprint::make()
                ->setHandle(self::HANDLE)
                ->setNamespace('globals')
                ->save();
        }
    }

    public function redirectMatching(string $url): Redirect|null
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
