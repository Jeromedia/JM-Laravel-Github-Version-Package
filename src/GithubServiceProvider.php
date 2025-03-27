<?php

namespace MyPackage;

use Illuminate\Support\ServiceProvider;

class GithubServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register any bindings or services here
    }

    public function boot()
    {
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
