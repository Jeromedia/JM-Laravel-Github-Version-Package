# **Laravel GitHub Service**

_A Laravel package to fetch and compare GitHub repository versions._

## **Description**

This package provides a simple way to:

- Get the current version of a Laravel web application (from Git tags).
- Fetch the latest release tag from a GitHub repository.
- Compare the two versions to check for updates.

---

## **Installation**

### **1. Add the private repository to your Laravel project (`composer.json`)**

If hosted on a private Git repository, add:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "git@github.com:jeromedia/laravel-github-service.git"
    }
],
"require": {
    "jeromedia/laravel-github-service": "dev-main"
}
```

### **2. Install the package using Composer**

```bash
composer require jeromedia/laravel-github-service
```

### **3. Publish the configuration (if needed)**

```bash
php artisan vendor:publish --provider="Jeromedia\LaravelGithubService\GithubServiceProvider" --tag=config
```
Add these to your .env file

```bash
#Github Connect
GITHUB_REPO=""
GITHUB_OWNER=""
GITHUB_TOKEN=""
GITHUB_CACHE_TTL=3600
```

---

## **Usage**

Import and call the service in your controller:

```php
use Jeromedia\Services\GithubService;

$webAppVersion = GithubService::getCurrentWebAppVersion();
$repoVersion = GithubService::getCurrentRepoVersion();
$versionStatus = GithubService::compareVersions($webAppVersion, $repoVersion);

echo $versionStatus;
```

---

## **Development & Contributions**

- **Author:** [Jerome / Jeromedia Team]
- **Website:** [jeromedia.com]
- **Contact:** [info@jeromedia.com]
- **License:** Private (Internal Use)

---

## **Notes**

- This package requires a **GitHub personal access token** to fetch repository data.
- Works best with Laravel 9+ and PHP 8+.

---
