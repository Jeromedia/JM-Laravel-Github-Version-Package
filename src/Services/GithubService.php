<?php

namespace Jeromedia\LaravelGithubService\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;
use Throwable;

class GithubService
{
    /**
     * Return the version tag attached to the exact commit currently deployed.
     *
     * Example: v1.4.2
     */
    public static function getCurrentWebAppVersion(): ?string
    {
        try {
            $process = new Process(
                ['git', 'describe', '--tags', '--exact-match', 'HEAD'],
                base_path()
            );

            $process->setTimeout(5);
            $process->run();

            if (! $process->isSuccessful()) {
                return null;
            }

            $version = trim($process->getOutput());

            return self::normalizeVersion($version) === null
                ? null
                : $version;
        } catch (Throwable $exception) {
            return null;
        }
    }

    /**
     * Return the latest published GitHub release tag.
     *
     * The GitHub result is cached, but the local deployed version is not.
     */
    public static function getCurrentRepoVersion(): ?string
    {
        $github = config('github-service', []);

        $owner = trim((string) ($github['owner'] ?? ''));
        $repo = trim((string) ($github['repo'] ?? ''));
        $api = rtrim((string) ($github['api'] ?? 'https://api.github.com/repos'), '/');
        $token = trim((string) ($github['token'] ?? ''));
        $cacheTtl = max(60, (int) ($github['cache_ttl'] ?? 3600));

        if ($owner === '' || $repo === '' || $api === '') {
            return null;
        }

        $cacheKey = self::getCacheKey();
        $cached = Cache::get($cacheKey);

        if (self::normalizeVersion($cached) !== null) {
            return trim($cached);
        }

        try {
            $request = Http::timeout(5)
                ->accept('application/vnd.github+json')
                ->withHeaders([
                    'User-Agent' => 'Jeromedia-Laravel-Github-Service',
                ]);

            if ($token !== '') {
                $request = $request->withToken($token);
            }

            $response = $request->get(
                $api
                . '/'
                . rawurlencode($owner)
                . '/'
                . rawurlencode($repo)
                . '/releases/latest'
            );
        } catch (Throwable $exception) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $tag = $response->json('tag_name');

        if (self::normalizeVersion($tag) === null) {
            return null;
        }

        $tag = trim($tag);

        Cache::put($cacheKey, $tag, $cacheTtl);

        return $tag;
    }

    /**
     * Build the text shown in the footer.
     */
    public static function getDisplayVersion(): string
    {
        $webAppVersion = self::getCurrentWebAppVersion();

        if ($webAppVersion === null) {
            return 'Version unavailable';
        }

        $repoVersion = self::getCurrentRepoVersion();

        return self::compareVersions($webAppVersion, $repoVersion);
    }

    /**
     * Compare the deployed version with the latest GitHub release.
     *
     * Local older than GitHub: "v1.4.2 (update available)"
     * Same version:            "v1.5.0"
     * Local newer than GitHub: "v1.6.0"
     * GitHub unavailable:      local version only
     */
    public static function compareVersions(?string $webAppVersion, ?string $repoVersion): string
    {
        $current = self::normalizeVersion($webAppVersion);

        if ($current === null) {
            return 'Version unavailable';
        }

        $displayVersion = trim((string) $webAppVersion);
        $latest = self::normalizeVersion($repoVersion);

        if ($latest === null) {
            return $displayVersion;
        }

        if (version_compare($current, $latest, '<')) {
            return $displayVersion . ' (update available)';
        }

        return $displayVersion;
    }

    /**
     * Clear the cached GitHub release for the configured repository.
     */
    public static function clearCache(): bool
    {
        return Cache::forget(self::getCacheKey());
    }

    /**
     * Repository-specific cache key so different websites cannot collide.
     */
    public static function getCacheKey(): string
    {
        $owner = strtolower(trim((string) config('github-service.owner', '')));
        $repo = strtolower(trim((string) config('github-service.repo', '')));

        return 'github-service:latest-release:' . sha1($owner . '/' . $repo);
    }

    /**
     * Normalize a Git tag for version_compare while preserving the original
     * tag for display.
     */
    private static function normalizeVersion(mixed $version): ?string
    {
        if (! is_string($version)) {
            return null;
        }

        $version = trim($version);

        if ($version === '') {
            return null;
        }

        if (str_starts_with($version, 'v') || str_starts_with($version, 'V')) {
            $version = substr($version, 1);
        }

        if (! preg_match(
            '/\A[0-9]+(?:\.[0-9]+)*(?:-[0-9A-Za-z]+(?:[.-][0-9A-Za-z]+)*)?(?:\+[0-9A-Za-z]+(?:[.-][0-9A-Za-z]+)*)?\z/',
            $version
        )) {
            return null;
        }

        // Build metadata does not affect precedence in Semantic Versioning.
        return explode('+', $version, 2)[0];
    }
}
