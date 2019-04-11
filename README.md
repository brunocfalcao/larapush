<p align="center">
    <img src="http://assets.brunofalcao.me/larapush/github_logo.jpg" alt="Larapush logo" width="300">
</p>

## About Larapush

Larapush is a Laravel library that will allow you to push your codebase to your Laravel app web server using a single artisan command: <b>artisan push</b>, using OAuth autorization to guarantee transactional security.

### Features

- Client <-> Server communications using Laravel Passport OAuth Client Access Grant tokens.
- Run pre-scripts and post-scripts that you define (artisan commands, invokable classes, or custom methods).
- Specify what codebase you want to upload (files, folders).
- Keeps all of your uploaded codebase inside versioned folders on your storage path.
- Asks for a confirmation in case you are uploading to sensitive environments (like production).

### Requirements
> Database needed in your <b>web server</b> (used by Laravel Passport) <br>
> PHP 7.2+ <br/>
> Laravel 5.8+ <br/>

### Installation

You need to require the package on both your web server and your local dev machine:

```bash
composer require brunocfalcao/larapush --dev
```

>In order to generate your OAuth client and token, you need to first install Larapush on your web server:

```bash
php artisan larapush:install-remote
```

