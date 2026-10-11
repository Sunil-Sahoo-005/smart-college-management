<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\StudentClassEnrollment;
use App\Observers\StudentClassEnrollmentObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        StudentClassEnrollment::observe(StudentClassEnrollmentObserver::class);
    }
}