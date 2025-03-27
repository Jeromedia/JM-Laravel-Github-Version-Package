<?php

return [
    'token' => env('GITHUB_API_TOKEN', ''),
    'api' => env('GITHUB_API_URL', 'https://api.github.com/repos/your-repo/releases/latest'),
    'cache_ttl' => env('GITHUB_CACHE_TTL', 3600),
];
