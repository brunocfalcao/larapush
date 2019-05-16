<?php

use Brunocfalcao\Larapush\Utilities\ScriptType;
use Brunocfalcao\Larapush\Utilities\SelectionType;

return [

    /*
     * Environment installation type:
     * 'local' = On your local dev computer.
     * 'remote' = On your web server.
     */
    'type' => env('LARAPUSH_TYPE', 'local'),

    /*
     * Any environment defined here will prompt you to confirm before uploading
     * your codebase to your web server, so you avoid deploying your codebase to
     * specific environments, like production.
     * You can pass several environments like: ['production', 'preproduction'].
     */
    'environment' => [
        'reserved' => ['production'],
    ],

    /*
     * Remote server information group.
     */
    'remote' => [
        // Your web server URL.
        // Manually configured when you install larapush on your local dev computer.
        'url' => env('LARAPUSH_REMOTE_URL'),

        // Route prefix for all web server routes, default is <your-server-url>/larapush.
        'prefix' => '/larapush',
    ],

    /*
     * What scripts/processes do you want to run before and after your
     * codebase push.
     * You can use (as many as you want):
     * Artisan commands. E.g.: 'cache:clear'
     * Invokable Classes: E.g.: MyClass::class (will call your __invoke() directly).
     * Custom Class methods: E.g.: 'MyClass@myMethod'.
     */
    'scripts' => [
        'pre_scripts' => [
        ],
        'post_scripts' => [
            ['cache:clear', ScriptType::ARTISAN],
            ['config:clear', ScriptType::ARTISAN],
            ['view:clear', ScriptType::ARTISAN],
            ['route:clear', ScriptType::ARTISAN],
        ],
    ],

    // What's the codebase you want to upload to your web server?
    // You can add multiple values (files or folder paths).
    // base_path() is the root path. No need to add a full physical path.
    // Examples:
    // 'database', 'app', 'app/User.php'
    'codebase' => [
    ],

    // Differencial upload. If true then only newer or new files will be added to the codebase zip.
    // If false, all files will be added to the codebase zip, not matter if they are new or newer.
    'delta_upload' => true,

    // Any file/folder path added to this configuration key will be skipped if present on the files to be uploaded
    // to the web server.
    // Attention:: The paths are case sensitive!
    'blacklist' => [
        '.env',
    ],

    // Folder path that will store your transaction codebase folders.
    'storage' => [
        'path' => storage_path('app/larapush'),
    ],

    /*
     * OAuth information + remote<->local token information.
     * Automatically filled on the remote server installation.
     * Don't change. Used by the library.
     */
    'oauth' => [
        'client' => env('LARAPUSH_OAUTH_CLIENT'),
        'secret' => env('LARAPUSH_OAUTH_SECRET'),
    ],

    /*
     * Local / Remote token. Must be the same in both environments.
     * Automatically created on your local or remote server installations.
     * Don't change. Used by the library.
     */
    'token' => env('LARAPUSH_TOKEN'),
];
