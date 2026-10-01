<?php

namespace Jeromedia\LaravelGithubService\Controllers;

use Jeromedia\LaravelGithubService\Services\GithubService;

class GithubController
{
    public function __invoke()
    {
        return response(GithubService::getDisplayVersion(), 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('Cache-Control', 'no-store');
    }
}
