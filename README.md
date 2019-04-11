<p align="center">
    <img src="http://assets.brunofalcao.me/larapush/github_logo.jpg" alt="Larapush logo" width="300">
</p>

## About Larapush

Larapush is a Laravel library that will allow you to push your codebase to your Laravel app web server using a single artisan command: <b>artisan push</b>, in a <u>secure</u> way.

### Features

- Client <-> Server communications using Laravel Passport OAuth Client Access Grant tokens.
- Run pre-scripts and post-scripts that you defined (artisan commands, invokable classes, or custom methods).
- Specify what codebase you want to upload (files, folders).
- Keeps all of your uploaded codebase inside transactional folders on your storage.
- Asks for a confirmation in case you are uploading to sensitive environments given the app.env label (like production).

### Installation

:hand: You need to have a database configured on your <b>web server</b> to use Larapush (needed for Laravel Passport).

You need to require the package on both your web server and your local dev machine:

```bash
composer require brunocfalcao/larapush --dev
```

Start by installing Larapush on your web server project using the following command:

Larapush have 2 artisan installation commands that you need to use. One for your web server, other for your local dev machine.

```bash
php artisan larapush:install-remote
```


