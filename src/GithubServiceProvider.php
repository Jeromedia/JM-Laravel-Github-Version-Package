<?php

namespace Jeromedia\LaravelGithubService;

use Illuminate\Support\ServiceProvider;
use Jeromedia\LaravelGithubService\Commands\ForgetGithubCache;

class GithubServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/github-service.php',
            'github-service'
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/github-api.php');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ForgetGithubCache::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/github-service.php' => config_path('github-service.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../routes/github-api.php' => base_path('routes/github-api.php'),
            ], 'routes');
        }
    }
}
