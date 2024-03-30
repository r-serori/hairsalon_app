<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'register',
        'dashboard',
        'logout',
        'user',
        'user/*',
        'attendances',
        'attendances/*',
        'attendance_times',
        'attendance_times/*',
        'attendance_times/*/search',
        'courses',
        'courses/*',
        'customers',
        'customers/*',
        'customers/*/schedule',
        'daily_sales',
        'daily_sales/*',
        'daily_sales/update-daily-sales',
        'hairstyles',
        'hairstyles/*',
        'monthly_sales',
        'monthly_sales/*',
        'monthly_sales/update-monthly-sales',
        'options',
        'options/*',
        'schedules',
        'schedules/*',
        'schedules/create/*',
        'schedules/update-daily-sales',
        'yearly_sales',
        'yearly_sales/*',
        'yearly_sales/update-yearly-sales',
        'yearly_sales/update-daily-sales',
        'yearly_sales/update-monthly-sales',
        'yearly_sales/update-weekly'

        



    ];
}
