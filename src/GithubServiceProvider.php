<?php

namespace Jeromedia\LaravelGithubService;

use Illuminate\Support\ServiceProvider;

class GithubServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register any bindings or services here
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Jeromedia\LaravelGithubService\Commands\ForgetGithubCache::class,
            ]);
        }
        // Step 1: Load the routes from the package (these will be available by default).
        $this->loadRoutesFrom(__DIR__ . '/../routes/github-api.php');

        // Step 2: Optionally, allow users to publish the route file if they need to customize it.
        $this->publishes([
            __DIR__ . '/../routes/github-api.php' => base_path('routes/github-api.php'),
        ], 'routes');

        // Publish config file so users can override it
        $this->publishes([
            __DIR__ . '/../config/github-service.php' => config_path('github-service.php'),
        ], 'config');

        // Merge package config with Laravel app config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/github-service.php',
            'github-service'
        );
    }

}
