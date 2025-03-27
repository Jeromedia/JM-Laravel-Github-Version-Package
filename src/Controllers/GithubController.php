<?php

namespace Jeromedia\LaravelGithubService\Controllers;


use Jeromedia\LaravelGithubService\Services\GithubService;
use App\Http\Controllers\Controller;


class GithubController extends Controller
{
    public function __invoke()
    {
        if (app()->environment('production')) {
            $webAppVersion = GithubService::getCurrentWebAppVersion();
            $repoVersion = GithubService::getCurrentRepoVersion();

            $compareVersions = GithubService::compareVersions($webAppVersion, $repoVersion);

            return $compareVersions;
        } else {
            $repoVersion = GithubService::getCurrentRepoVersion();
            return $repoVersion;
        }
    }
}
