<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Default Boilerplate values
     |--------------------------------------------------------------------------
     |
     | Here you can specify all custom values you want to use in the code
     |
     */

    'roles' => [
        'super_admin' => 'super_admin',
        'user' => 'user'
    ],
    'api_rate_limit' => env('API_RATE_LIMIT', 60),
];
