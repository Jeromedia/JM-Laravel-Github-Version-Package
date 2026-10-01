# Laravel GitHub Service

A small Laravel package that automatically shows the version currently deployed on a website and tells you when a newer GitHub release is available.

## How it works

The package uses two sources:

1. **Current website version** — read automatically from the Git tag attached to the deployed `HEAD` commit.
2. **Latest available version** — fetched from GitHub's `/releases/latest` API endpoint.

The GitHub response is cached. The local Git version is read directly and is not cached.

Example:

```text
Deployed website: v1.4.2
Latest GitHub release: v1.5.0

Output: v1.4.2 (update available)
```

If both versions are the same:

```text
v1.5.0
```

If the deployed website is newer than GitHub, the package simply shows the deployed version:

```text
v1.6.0
```

There is no "impossible" state.

## Requirements

- Laravel 9, 10, 11, 12, or 13
- PHP 8+
- Laravel 13 requires PHP 8.3+
- Git must be installed on the production server
- The deployed Laravel project must contain its `.git` directory
- The deployed commit should have a version tag such as `v1.5.0`

## Installation

```bash
composer require jeromedia/laravel-github-service
```

Publish the configuration:

```bash
php artisan vendor:publish \
    --provider="Jeromedia\LaravelGithubService\GithubServiceProvider" \
    --tag=config
```

## One-time `.env` setup

```env
GITHUB_API_OWNER="your-owner"
GITHUB_API_REPO="your-repository"
GITHUB_API_TOKEN="your-token"
GITHUB_API_URL="https://api.github.com/repos"
GITHUB_API_CACHE_TTL=3600
```

`GITHUB_API_CACHE_TTL=3600` means GitHub is checked at most once per hour.

You do **not** configure the website version. The package reads it automatically from Git.

## GitHub token

For a private repository, use a GitHub token with read access to the repository.

For a public repository, GitHub can serve the release endpoint without a token, but using a token provides a higher API rate limit.

## Endpoint

The package automatically registers:

```text
GET /github
```

with the route name:

```php
route('github.api')
```

The endpoint returns plain text.

Possible output:

```text
v1.5.0
```

or:

```text
v1.5.0 (update available)
```

## Vue / Inertia usage

If your Laravel application uses Vue with Inertia, you can fetch the version from the package endpoint directly from your footer component.

Example:

```vue
<script setup>
import { onMounted, ref } from 'vue'

const githubVersion = ref('')

onMounted(async () => {
    const response = await fetch('/github')

    githubVersion.value = await response.text()
})
</script>

<template>
    <span>{{ githubVersion }}</span>
</template>
```

You can place this logic directly inside your existing Vue footer component.

The GitHub token and repository configuration remain on the Laravel backend. Nothing sensitive is exposed to Vue.

## Alpine.js usage

For Laravel applications using Alpine.js:

```html
<div
    x-data="{ github: '' }"
    x-init="fetch('{{ route('github.api') }}')
        .then(response => response.text())
        .then(data => github = data)"
>
    <span class="text-stone-400" x-text="github"></span>
</div>
```

## Cache behavior

Only the latest GitHub release is cached.

The local website version is read directly from Git whenever the package endpoint is requested.

The cache key is specific to the configured owner and repository, so multiple Laravel websites can safely use the same Redis or database cache.

Default cache time:

```env
GITHUB_API_CACHE_TTL=3600
```

With the default value, GitHub is queried at most once per hour for that configured repository.

The comparison itself is inexpensive and is performed when the `/github` endpoint is requested.

To manually clear the GitHub release cache:

```bash
php artisan github-service:clear-cache
```

You normally do not need to run this command.

## When GitHub is unavailable

If GitHub is temporarily unavailable, the package still displays the locally deployed version.

Example:

```text
v1.5.0
```

It will not replace the footer with a GitHub connection error.

## Important deployment note

The package determines the installed version using:

```bash
git describe --tags --exact-match HEAD
```

This checks which Git tag belongs to the exact commit currently deployed.

For example, if the deployed `HEAD` is tagged:

```text
v1.5.0
```

then the package considers the website's current version to be:

```text
v1.5.0
```

The deployed commit must therefore be tagged.

If `.git` is removed during deployment, or the deployed commit has no exact tag, the package cannot automatically know which version is installed and will return:

```text
Version unavailable
```

## Configuration

The published config file is:

```text
config/github-service.php
```

```php
<?php

return [
    'owner' => env('GITHUB_API_OWNER', ''),
    'repo' => env('GITHUB_API_REPO', ''),
    'token' => env('GITHUB_API_TOKEN', ''),
    'api' => env('GITHUB_API_URL', 'https://api.github.com/repos'),
    'cache_ttl' => (int) env('GITHUB_API_CACHE_TTL', 3600),
];
```

No version number is stored in `.env`, config, or a database.

## Version comparison

The package behaves as follows:

```text
Local:  v1.4.2
GitHub: v1.5.0

v1.4.2 (update available)
```

```text
Local:  v1.5.0
GitHub: v1.5.0

v1.5.0
```

```text
Local:  v1.6.0
GitHub: v1.5.0

v1.6.0
```

A local version newer than the latest GitHub release is valid and is not treated as an error.

## Release 2.0.0

Version 2.0.0 changes the package to use the exact Git tag attached to the deployed `HEAD` commit as the website version.

It also:

- caches only the GitHub release lookup,
- uses repository-specific cache keys,
- removes the old "impossible" version state,
- falls back to the local version if GitHub is unavailable,
- removes the manually configured GitHub API version,
- supports Laravel 9 through Laravel 13,
- and documents both Vue / Inertia and Alpine.js frontend usage.
