<?php

use Brunocfalcao\Larapush\Utilities\ScriptType;

return [

    /*
     * Environment type:
     * 'local' = Your local dev machine
     * 'remote' = Your remote codebase server.
     */
    'type' => env('LARAPUSH_TYPE', 'local'),

    /*
     * Your remove server information group.
     */
    'remote' => [
        // Your remote server URL.
        // Manually configured when you install larapush on your local computer.
        'url' => env('LARAPUSH_REMOTE_URL'),

        // Your route prefix, default is <your-server-url>/larapush.
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
            ['cache:clear', ScriptType::ARTISAN],
            ['view:clear', ScriptType::ARTISAN],
            /*
            [MyClass::class, ScriptType::CLASSMETHOD],
            ['MyClass@method', ScriptType::CLASSMETHOD],
            ['composer update', ScriptType::SHELLCMD],
            */
        ],
        'post_scripts' => [
            ['view:clear', ScriptType::ARTISAN],
        ],
    ],

    // What's the codebase you want to upload to your remote server?
    // Each selected folder will contain all the children sub-folders/files.
    'codebase' => [
        'database',
        'app/test.php',
    ],

    // Folder path that will store your transaction codebase folders.
    'storage' => [
        'path' => storage_path('app/larapush'),
    ],

    /*
     * OAuth information + remote<->local token information.
     * Automatically filled on the remote server installation.
     */
    'oauth' => [
        'client' => env('LARAPUSH_OAUTH_CLIENT'),
        'secret' => env('LARAPUSH_OAUTH_SECRET'),
    ],

    /*
     * Local / Remote token. Must be the same in both environments.
     * Automatically created on your local or remote server installations.
     */
    'token' => env('LARAPUSH_TOKEN'),
];
