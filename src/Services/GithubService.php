<?php

namespace Jeromedia\LaravelGithubService\Services;


use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GithubService
{
    public static function getCurrentWebAppVersion()
    {
        $version = Process::run('git describe --tags --abbrev=0')->output();
        return trim($version);
    }
    public static function getCurrentRepoVersion()
    {
        $github = config('github-service');
        return Cache::remember('github-tagname', $github['cache_ttl'], function () use ($github) {
            $response = Http::withToken($github['token'])->withHeaders([
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => $github['github_version'],
            ])->get($github['api'] . "/" . $github['owner'] . "/" . $github['repo'] . "/releases/latest");
            if ($response->successful()) {
                return $response->json('tag_name');
            }
            return "error";
        });
    }
    public static function compareVersions($webAppVersion, $repoVersion)
    {
        $result = version_compare($webAppVersion, $repoVersion);

        if ($result === -1) {
            return $webAppVersion . " (new version available)";
        } elseif ($result === 1) {
            return $webAppVersion . " (impossible)";
        } else {
            return $webAppVersion;
        }
    }

}
