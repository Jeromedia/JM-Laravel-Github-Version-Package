# **Laravel GitHub Service**

_A Laravel package to fetch and compare GitHub repository versions._

## **Description**

This package provides a simple way to:

- Get the current version of a Laravel web application (from Git tags).
- Fetch the latest release tag from a GitHub repository.
- Compare the two versions to check for updates.

---

## **Installation**

### **1. Install the package using Composer**

```bash
composer require jeromedia/laravel-github-service
```

### **2. Publish the configuration - Important**

```bash
php artisan vendor:publish --provider="Jeromedia\LaravelGithubService\GithubServiceProvider" --tag=config
```

### **3. The Github Full APi link for fetching the latest release**

```bash
https://api.github.com/repos/{OWNER}/{REPO}/releases/latest
```

### **4. Add to your .env file**

```bash
#Github Connect
GITHUB_API_REPO=""
GITHUB_API_OWNER=""
GITHUB_API_VERSION="2022-11-28"
GITHUB_API_URL="https://api.github.com/repos"
GITHUB_API_TOKEN=""
GITHUB_API_CACHE_TTL=3600
```

---

## **Usage**

### **1. Add the following code to your footer**

```bash
<div class="container mx-auto relative"
x-data="{ github: '' }"
x-init="fetch('{{route('github.api')}}')
        .then(response => response.text())
        .then(data => { github = data; })">
```

Somewhere in your footer

```bash
<span class="text-stone-400" x-text="github"></span>
```

### **2. Add the route to your api routes file or publish the route**

```bash
Route::get('/github', GithubController::class)->name('github.api');
```

### **3. Or publish the route**

```bash
php artisan vendor:publish --provider="Jeromedia\LaravelGithubService\GithubServiceProvider" --tag=routes

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
