<?php

return [
    'owner' => env('GITHUB_API_OWNER', ''),
    'repo' => env('GITHUB_API_REPO', ''),
    'token' => env('GITHUB_API_TOKEN', ''),
    'api' => env('GITHUB_API_URL', 'https://api.github.com/repos'),
    'cache_ttl' => (int) env('GITHUB_API_CACHE_TTL', 3600),
];
