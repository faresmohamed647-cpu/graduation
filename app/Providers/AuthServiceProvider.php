<?php

namespace App\Providers;

use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Driver;
use App\Models\Student;
use App\Models\Trip;
use App\Policies\SchoolScopedPolicy;
use App\Policies\StudentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Student::class => StudentPolicy::class,
        Bus::class => SchoolScopedPolicy::class,
        Driver::class => SchoolScopedPolicy::class,
        BusRoute::class => SchoolScopedPolicy::class,
        Trip::class => SchoolScopedPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
