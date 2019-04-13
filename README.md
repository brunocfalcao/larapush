<p align="center">
    <img src="http://assets.brunofalcao.me/larapush/github_logo.jpg" alt="Larapush logo" width="300">
</p>
<br/>

## About Larapush

Larapush is a Laravel library that allows you to upload your codebase to your Web Server using a single artisan command:
<p align="center"><b>php artisan push</b></p>

### Features

- Client <-> Server communications using Laravel Passport OAuth Client Access Grant tokens.
- Can run pre-scripts and post-scripts that you define (artisan commands, invokable classes, or custom methods).
- Stores all output from your pre-scripts and post-scripts in folders inside your storage path.
- Allows you to specify what codebase you want to upload (files, folders).
- Keeps all of your uploaded codebase inside versioned folders on your storage path.
- Asks for a confirmation in case you are uploading to sensitive environments (like production).
- Works on both UNIX and Windows web server operating systems.

### Security above all
Larapush was developed taking security very seriously. At the end, anyone that knows your endpoint would be able to upload a malicious codebase to your web server. That's why Larapush uses OAuth client grant access tokens for each of the HTTP transactions that are handshaken between your web server and your local computer. At the end of the HTTP transaction, the client access token is marked as used, so it cannot be used again.

When you install Larapush on your web server, it installs Laravel Passport and generates a specific client token that will be unique between your web server and your local computer. Henceforth any transaction is made by your client, it need to first request a new client grant token and then use that token on the very next operation. Additionally there is also a token that is passed on each request that needs to be the same between your local computer and the web server. If not, the transaction aborts in error.

### Requirements
> Database is needed only your <b>web server</b> (used by Laravel Passport) <br>
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

Copy+paste the one generated on your web server so you can use it later on your local installation.

On your <b>local development computer</b>, run the following commands:

```bash
composer require brunocfalcao/larapush --dev
```

Then, the copied line from your web server (this one is just an example):

```bash
php artisan larapush:install-local --client=4 --secret=5DrehY2gjPWTPL4rxzQwseHiQHWq8FXaH0Y --token=WXD2W6ZVK5
```

The installer will prompt to insert your URL. Just add it in the FQDN format (e.g.: https://www.johnsmith.com).

All done! If it goes well, you should see:

```bash
All good! Now you can push your codebase to your web server!

Don't forget to update your larapush.php configuration file for the correct codebase files and directories
that you want to upload.
```

### Usage
Henceforth all you have to do is to run the command:
```bash
php artisan push
```
It will upload your codebase that you define on your larapush.php configuration file (larapush.codebase) and the scripts that you also specified (larapush.scripts).

### Configuration

A new larapush.php configuration file is created. Let's explore it.

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
- Execute an invokable method. E.g.: [MyClass::class, ScriptType::CLASSMETHOD]
- Execute a specific object method: E.g.: ['MyClass@myMethod', ScriptType::CLASSMETHOD]
- Execute a system shell commands: E.g.:"['composer dumpautoload', ScriptType::SHELLCMD]

You can add as much as you want. All outputs are stored inside your transaction folder (later to be explained).

#### larapush.codebase
Important part, where you can specify your codebase folders and files. Just add them as array values, E.g.:

```php
    'codebase' => [
        'App', 'database', 'resources/views/file.blade.php', 'webpack.js'
    ],
```

#### larapush.storage
This is the path where all of your codebase push transactions are stored. See it like a versioning way of storing all of your codebase along time.

#### larapush.oauth
This are security tokens generated by Laravel Passport. Don't change them, they are recorded on your .env file.

#### larapush.token
This is an extra security layer, of a token that needs to be the same in both web server and your local dev computer. Registered via your installation process.

### Codebase transaction repository
Each time you upload your codebase, Larapush stores that codebase plus your configured "pre" and "post" scripts both in your local computer and in your web server. This is called a transaction. By default these transaction folders are on your storage_path("app/larapush"). You can change this path on your larapush.php configuration file.

As example, you are pushing your codebase and you see this line on your artisan command info:

```bash
[...]

Creating local environment codebase repository (20190413-211644-GFKXN)...

[...]
```

That code between parenthesis is the transaction code. You can later revisit the folder and inspect what codebase was uploaded.

