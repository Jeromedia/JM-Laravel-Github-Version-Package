<?php

namespace Jeromedia\LaravelGithubService\Commands;

use Illuminate\Console\Command;
use Jeromedia\LaravelGithubService\Services\GithubService;

class ForgetGithubCache extends Command
{
    protected $signature = 'github-service:clear-cache';

    protected $description = 'Clear the cached latest GitHub release';

    public function handle(): int
    {
        $cacheKey = GithubService::getCacheKey();
        $cleared = GithubService::clearCache();

        if ($cleared) {
            $this->info('GitHub release cache cleared.');
        } else {
            $this->info('GitHub release cache was already empty.');
        }

        $this->line('Cache key: ' . $cacheKey);

        return self::SUCCESS;
    }
}
