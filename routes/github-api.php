<?php

use Illuminate\Support\Facades\Route;
use Jeromedia\LaravelGithubService\Controllers\GithubController;

Route::get('/github', GithubController::class)->name('github.api');