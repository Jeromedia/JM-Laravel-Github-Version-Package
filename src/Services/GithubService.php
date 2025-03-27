<?php

namespace App\Services;

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
        $github = config('github-api');
        return Cache::remember('github-tagname', $github['cache-ttl'], function () use ($github) {
            $response = Http::withToken($github['token'])->get($github['api']);
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
