<p align="center">
    <img src="http://assets.brunofalcao.me/larapush/github_logo.jpg" alt="Larapush logo" width="300">
</p>
<br/><br/>

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

:exclamation: In order to generate your OAuth client and token, you need to first install Larapush on your web server!

Enter on your web server and run the following commands:

```bash
composer require brunocfalcao/larapush --dev
```

```bash
php artisan larapush:install-remote
```

At the end, it will generate your command line that you will need to use to install it on your local computer.

Like this example:
```bash
ALL DONE!

Please install Larapush on your local Laravel app and run the following artisan command:

php artisan larapush:install-local --client=4 --secret=5DrehY2gjPWTPL4rxzQwseHiQHWq8FXaH0Y --token=WXD2W6ZVK5
```

Copy+paste it somewhere, so you can use it later!

On your local development computer, run the following commands:

```bash
composer require brunocfalcao/larapush --dev
```

Then, the copied line from your web server (this one is just an example):

```bash
php artisan larapush:install-local --client=4 --secret=5DrehY2gjPWTPL4rxzQwseHiQHWq8FXaH0Y --token=WXD2W6ZVK5
```

