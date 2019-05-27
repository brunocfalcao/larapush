<?php

use Brunocfalcao\Larapush\Structures\ScriptType;

return [

    /*
    |--------------------------------------------------------------------------
    | Current environment type
    |--------------------------------------------------------------------------
    |
    | This option is configured when you install Larapush as remote or local
    | environment. By default you should not change this option manually.
    |
    | Supported: "local", "remote"
    |
    */

    'type' => env('LARAPUSH_TYPE', 'local'),

    /*
    |--------------------------------------------------------------------------
    | The list of environments that will prompt for your upload to continue
    |--------------------------------------------------------------------------
    |
    | If you have an environment that you want to prompt before uploading data
    | (e.g. a production environment) you can write them here.
    | For each upload, Larapush will FIRST check your web server APP_ENV .env
    | and if is contained on this array values then it will ask for a
    | confirmation before continuing.
    |
    */

    'environment' => [
        'reserved' => ['production'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Web server URL configuration
    |--------------------------------------------------------------------------
    |
    | This configuration is written on your .env automatically when you install
    | Larapush on your web server or local computer.
    |
    */

    'remote' => [

        /*
        |--------------------------------------------------------------------------
        | Web server URL
        |--------------------------------------------------------------------------
        |
        | Your web server URL.
        |
        | Remarks: It is your base URL (.e.g. http://www.google.com).
        |          It should be full qualified domain name (e.g. https://domain.com).
        |          It is concatenated with your URL suffix defined below.
        |
        */

        'url' => env('LARAPUSH_REMOTE_URL'),

        /*
        |--------------------------------------------------------------------------
        | Web server URL suffix
        |--------------------------------------------------------------------------
        |
        | Your web server URL suffix, concatenated to your web server URL.
        |
        | Remarks: It should start with a '/'.
        |
        */

        'suffix' => '/larapush',
    ],

    /*
    |--------------------------------------------------------------------------
    | Runtime scripts execution
    |--------------------------------------------------------------------------
    |
    | In this section you can specify the runtime code you want to be executed
    | prior and after the codebase upload and deployment is done.
    | Each entry array is composed by 2 values:
    | - The command line.
    | - The type of command line.
    |
    | Supported: Script Types:
    |            - ScriptType::ARTISAN for Artisan commands.
    |            - ScriptType::CLASS for Class methods or invokable calls.
    |            - ScriptType::SHELLCMD for shell commands.
    |
    | Examples: ['cache:clear', ScriptType::ARTISAN]
    |           ['composer update', ScriptType::SHELLCMD]
    |           [MyClass::class, ScriptType::CLASSMETHOD]
    |           ['MyClass@test', ScriptType::CLASSMETHOD]
    |
    | Each of the scripts export dumps will be saved in the zip folder under
    | the names "output_post_scripts.log" or "output_pre_scripts.log".
    */

    'scripts' => [

        /*
        |--------------------------------------------------------------------------
        | Pre-deployment execution scripts
        |--------------------------------------------------------------------------
        |
        | Here you can define what scripts/class methods will be called BEFORE your
        | codebase is deployed on your web server.
        |
        */

        'pre_scripts' => [
        ],

        /*
        |--------------------------------------------------------------------------
        | Post-deployment execution scripts
        |--------------------------------------------------------------------------
        |
        | Here you can define what scripts/class methods will be called AFTER your
        | codebase is deployed on your web server.
        |
        */

        'post_scripts' => [
            ['optimize:clear',  ScriptType::ARTISAN],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Codebase folders and files to be deployed on your web server
    |--------------------------------------------------------------------------
    |
    | Here you can list what codebase you want to be deployed on your web server.
    | You can enter values for folders or paths for files.
    |
    | Remarks: Don't explicit the FULL path, but only the relative path on
    |          your web project.
    |          E.g.: [ 'App', 'server.php', 'resources/js/app.js' ].
    |
    */

    'codebase' => [
    ],

    // Differencial upload. If true then only newer or new files will be added to the codebase zip.
    // If false, all files will be added to the codebase zip, not matter if they are new or newer.

    /*
    |--------------------------------------------------------------------------
    | Only changed/newer files deployed
    |--------------------------------------------------------------------------
    |
    | In case you want to upload ONLY changed or newer files, you shoud set
    | this value to true.
    |
    | Remarks: It will not delete files on your web server.
    |          It will include new files too, besides the changes ones.
    |
    */

    'delta_upload' => false,

    /*
    |--------------------------------------------------------------------------
    | Blacklisted files
    |--------------------------------------------------------------------------
    |
    | Sometimes you might wanna include files that shouldn't never be updated
    | by your uploads to the server. E.g. .env.
    | No matter if this file exists on your local computer, it will never be
    | uploaded to the server to be overriden.
    |
    | Remarks: The file paths are CASE SENSITIVE.
    |
    */

    'blacklist' => [
        '.env',
    ],

    /*
    |--------------------------------------------------------------------------
    | Transaction storage folder
    |--------------------------------------------------------------------------
    |
    | Larapush stores all uploads in a storage folder so they can be used by
    | you to rollback a specific codebase that you need. Also, the last folder
    | will be used in case the "delta_upload" flag is true to compare the
    | codebase from the last upload with the one that will be upload for.
    | By default you shouldn't change this value.
    |
    */

    'storage' => [
        'path' => storage_path('app/larapush'),
    ],

    /*
    |--------------------------------------------------------------------------
    | OAuth keys configuration
    |--------------------------------------------------------------------------
    |
    | These keys are automatically set upon your installation.
    | They are given by your web server Larapush installation that also installs
    | Laravel Passport for transaction security and unauthorized access.
    |
    */

    'oauth' => [
        'client' => env('LARAPUSH_OAUTH_CLIENT'),
        'secret' => env('LARAPUSH_OAUTH_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Local Token key extra security layer
    |--------------------------------------------------------------------------
    |
    | Additionally to the OAuth Server and Client tokens, Larapush adds a
    | specific local and server token that should be equal in both sides.
    | This is automatically registered during your installation process.
    |
    */

    'token' => env('LARAPUSH_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | CURL SSL option
    |--------------------------------------------------------------------------
    |
    | In case you don't have a TLS HTTP connection to your server, you can
    | can disable your CURL TLS/SSL verification.
    | Just add the following LARAPUSH_CURL_SSL=false on your .env file.
    |
    | More information on the CURL SSL/TLS verify option:
    | https://guzzle.readthedocs.io/en/latest/request-options.html#verify
    |
    */

    'curl_ssl' => env('LARAPUSH_CURL_SSL', true),
];
