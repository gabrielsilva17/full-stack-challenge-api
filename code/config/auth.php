<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        // opcional: você pode deixar 'web' e apenas especificar auth:sanctum nas rotas,
        // ou já apontar direto para a API:
        'guard'     => 'api',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */
    'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],

        'api' => [
            // este guard vai usar Sanctum para Bearer tokens
            'driver'   => 'sanctum',
            'provider' => 'users',
            // 'hash' => false, // só se você quiser tokens auto-hash
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\User::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Reset de Senha
    |--------------------------------------------------------------------------
    */
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Time de timeout de confirmação de senha
    |--------------------------------------------------------------------------
    */
    'password_timeout' => 10800,

];
