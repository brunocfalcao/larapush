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

The installer will prompt to insert your URL. Just add it in the FQDN format (e.g.: https://www.nytimes.com).

All done! If it goes well, you should see:

```bash
All good! Now you can push your codebase to your remote server!

Don't forget to update your larapush.php configuration file for the correct codebase files and directories 
that you want to upload.
```

### Configuration

Done on your new larapush.php configuration file. Let's explore it.

#### larapush.type
No need to change this, it is automatically configured by the installers using .env keys that will be registered.

#### larapush.environment
If your web server environment name matches one of the ones specified here, then Larapush will ask you to confirm the upload each time you push your code. This will avoid you to upload your codebase to environments that you might not want to (like a production).

#### larapush.remote
No need to change these keys by default, unless you want to change the main root URL path, or force a web server URL.

#### larapush.scripts (pre_scripts and post_scripts)
You can specify actions to run before and after the code is deployed on your web server.
For each, you can:
- Run an Artisan command. E.g.: ['cache:clear', ScriptType::ARTISAN]





