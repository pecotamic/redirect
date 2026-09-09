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

Installing the addon adds a **Weiterleitungen** (Redirects) entry to the Globals section of the control panel. Each rule has:

* **Request-URI**: the incoming path to match, e.g. `/old-page`
* **Übereinstimmung** (Match type): `Genau` (exact) matches the path exactly, `Beginnt mit` (starts with) matches any path starting with it
* **Weiterleitung** (Response): the HTTP status code to respond with &mdash; `301` (Moved Permanently), `302` (Moved Temporarily), `403` (Forbidden), `404` (Not Found) or `410` (Gone)
* **Ziel** (Target): the redirect destination, required for `301`/`302`. Accepts a relative path (e.g. `/new-page`) or an absolute `http(s)://` URL
