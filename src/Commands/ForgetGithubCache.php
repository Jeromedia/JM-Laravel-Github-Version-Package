<?php

namespace Jeromedia\LaravelGithubService\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ForgetGithubCache extends Command
{
    protected $signature = 'github-service:clear-cache';
    protected $description = 'Clear the GitHub repository version cache';

    public function handle()
    {
        if (Cache::forget('github-tagname')) {
            $this->info('GitHub version cache cleared successfully.');
        } else {
            $this->warn('Cache key "github-tagname" does not exist or is already cleared.');
        }
    }
}
