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

    public function __construct(private Collection $data)
    {
    }

    public function redirects(): \Generator
    {
        foreach ($this->data['redirects'] ?? [] as $data) {
            yield new Redirect($data);
        }
    }

    public static function get(Request $request)
    {
        $site = $request->site ? Site::get($request->site) : Site::selected();

        return new self(GlobalSet::findByHandle(self::HANDLE)
            ->localizations()[$site->handle()]->data());
    }

    public static function setup()
    {
        if (! GlobalSet::findByHandle(self::HANDLE)) {
            GlobalSet::make(self::HANDLE)
                ->title('Weiterleitungen')
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
        foreach ($this->redirects() as $redirect) {
            if (match ($redirect->matchType()) {
                Redirect::MATCH_TYPE_EXACT => strcasecmp($redirect->requestUri(), $url) === 0,
                Redirect::MATCH_TYPE_STARTS_WITH => Str::startsWith($url, $redirect->requestUri()),
            }) {
                return $redirect;
            }
        }

        return null;
    }
}
