<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default hash driver that will be used to hash
    | passwords for your application. By default, bcrypt is used; however,
    | we use custom pbkdf2 to maintain compatibility with the VB.NET app.
    |
    | Supported: "bcrypt", "argon", "argon2id", "pbkdf2"
    |
    | Note: "pbkdf2" is registered as a custom hasher driver in AppServiceProvider.
    |
    */

    'driver' => 'pbkdf2',

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Options
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration options for the bcrypt driver.
    | This will control the complexity factor of the hashes generated.
    |
    | We keep this for the fallback bcrypt verification.
    |
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
        'verify' => true,
    ],

];
