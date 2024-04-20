<?php

use App\Models\attendances;

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => [
        'api/*', 'sanctum/csrf-cookie',
        'csrf-token',
        'login', 'register', 'dashboard', 'logout', 'user', 'user/*', 'attendances', 'attendances/*', 'attendance_times', 'attendance_times/*', 'attendance_times/*/search', 'courses', 'courses/*', 'customers', 'customers/*', 'customers/*/schedule', 'daily_sales', 'daily_sales/*', 'daily_sales/update-daily-sales', 'hairstyles', 'hairstyles/*', 'monthly_sales', 'monthly_sales/*', 'monthly_sales/update-monthly-sales', 'options', 'options/*', 'schedules', 'schedules/*', 'schedules/create/*', 'schedules/update-daily-sales', 'yearly_sales', 'yearly_sales/*', 'stocks', 'stocks/*', 'stocks/*/search', 'stock_categories', 'stock_categories/*', 'stock_categories/*/search',
        'customer_schedules', 'course_customers', 'customer_attendances', 'hairstyle_customers', 'hairstyle_schedules', 'merchandise_customers', 'merchandise_schedules', 'option_customers', 'option_schedules',
    ],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => ['http://localhost:3000'],

    'allowed_origins_patterns' => ['*'],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
