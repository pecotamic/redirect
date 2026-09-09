# Redirect Addon for Statamic

![Statamic 5](https://img.shields.io/badge/Statamic-5.0+-26BBDD?style=for-the-badge&link=https://statamic.com)
![Statamic 6](https://img.shields.io/badge/Statamic-6.0+-FF269E?style=for-the-badge&link=https://statamic.com)

[![Tests](https://github.com/pecotamic/redirect/actions/workflows/tests.yml/badge.svg)](https://github.com/pecotamic/redirect/actions/workflows/tests.yml)

Adds 301 and 302 redirects, and 403, 404 and 410 aborts, to your Statamic web site. Rules are managed directly from the Statamic control panel &mdash; no code or config changes needed.

## How to Install

You can search for this addon in the `Tools > Addons` section of the Statamic control panel and click **install**, or run the following command from your project root:

``` bash
composer require pecotamic/redirect
```

The package requires PHP 8.2+. It will auto register.

## Usage

Installing the addon adds a **Redirects** collection to the control panel, with one entry per redirect rule &mdash; so the list stays fast and searchable even with a large number of redirects. Its field labels follow the control panel's active language (German and English are built in). Each rule has:

* **Request URI**: the incoming path to match, e.g. `/old-page`
* **Match type**: `Exact` matches the path exactly, `Starts with` matches any path starting with it
* **Response**: the HTTP status code to respond with &mdash; `301` (Moved Permanently), `302` (Moved Temporarily), `403` (Forbidden), `404` (Not Found) or `410` (Gone)
* **Target**: the redirect destination, required for `301`/`302`. Accepts a relative path (e.g. `/new-page`) or an absolute `http(s)://` URL

A redirect can be unpublished (via the entry's regular publish/unpublish control) to disable it without deleting it.

## Upgrading from 1.x

Versions up to 1.1.1 stored redirects in a single "Weiterleitungen" global set instead of a collection. After upgrading, run:

``` bash
php artisan pecotamic:redirects:migrate
```

This copies existing redirects into the new collection (an unpublished/disabled rule stays unpublished). It's safe to run more than once &mdash; existing entries are skipped, not duplicated. The old global set is left untouched, so you can verify the result before removing it yourself.
