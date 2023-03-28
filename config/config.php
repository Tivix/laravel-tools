<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tools config
    |--------------------------------------------------------------------------
    |
    | This file is for storing configurations related to tools.
    */
    'date' => [
        'format' => env('KELLTON_TOOLS_DATE_FORMAT', 'Y-m-d'),
        'time_format' => env('KELLTON_TOOLS_TIME_FORMAT', 'H:i:s'),
        'datetime_format' => env('KELLTON_TOOLS_DATE_TIME_FORMAT', 'Y-m-d H:i:s'),
    ],
    'pagination' => [
        'per_page' => env('KELLTON_TOOLS_PAGINATION_PER_PAGE', 20),
    ],
    'open_api' => [
        'default_url' => env('KELLTON_TOOLS_OPEN_API_DEFAULT_URL', env('APP_URL')),
    ],
];
