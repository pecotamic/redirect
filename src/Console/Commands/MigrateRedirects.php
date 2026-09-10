<?php

namespace Pecotamic\Redirect\Console\Commands;

use Illuminate\Console\Command;
use Statamic\Facades\Entry;
use Statamic\Facades\GlobalSet;
use Statamic\Facades\Site;

class MigrateRedirects extends Command
{
    protected $signature = 'pecotamic:redirects:migrate';

    protected $description = 'Migrate redirects from the old "pecotamic_redirects" global set into the redirects collection';

    public function handle(): int
    {
        $globalSet = GlobalSet::findByHandle('pecotamic_redirects');

        if (! $globalSet) {
            $this->info('No "pecotamic_redirects" global set found. Nothing to migrate.');

            return self::SUCCESS;
        }

        $migrated = 0;
        $skipped = 0;

        foreach (Site::all() as $site) {
            $localization = $globalSet->in($site->handle());
            $redirects = $localization?->data()->get('redirects', []) ?? [];

            foreach ($redirects as $redirect) {
                if ($this->entryExists($site->handle(), $redirect['request_uri'], $redirect['match_type'])) {
                    $skipped++;

                    continue;
                }

                Entry::make()
                    ->collection('redirects')
                    ->locale($site->handle())
                    ->published($redirect['enabled'] ?? true)
                    ->data([
                        'request_uri' => $redirect['request_uri'],
                        'match_type' => $redirect['match_type'],
                        'response_code' => $redirect['response_code'],
                        'target' => $redirect['target'] ?? null,
                    ])
                    ->save();

                $migrated++;
            }
        }

        $this->info("Migrated {$migrated} redirect(s), skipped {$skipped} already present.");

        $globalSet->delete();
        $this->info('Removed the old "pecotamic_redirects" global set.');

        return self::SUCCESS;
    }

    private function entryExists(string $site, string $requestUri, string $matchType): bool
    {
        return Entry::query()
            ->where('collection', 'redirects')
            ->where('site', $site)
            ->where('request_uri', $requestUri)
            ->where('match_type', $matchType)
            ->exists();
    }
}
