<?php

return [

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'register',
        'forgotPassword',
        'resetPassword',
        'verify-email/*',
        'updateInfo/*',
        'vio-role',
        'check-session',
        'logout',
        'getKey',
        'ownerRegister',
        'updateOwner',
        'getOwner',
        'showUser',
        'getUsers',
        'updatePermission',
        'deleteUser',
        'staffRegister',
        'csrf-token',
        'attendance_times/*',
        'firstAttendanceTimes/*',
        'customers/*',
        'courses/*',
        'options/*',
        'merchandises/*',
        'hairstyles/*',
        'schedules/*',
        'daily_sales/*',
        'monthly_sales/*',
        'yearly_sales/*',
        'stocks/*',
        'stock_categories/*',
    ],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => [env('FRONTEND_URL')],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['*'],

    'max_age' => 0,

    'supports_credentials' => true,
];
