<?php

namespace App\Providers;

use App\Models\VerificationApproval;
use App\Models\VerificationPeriod;
use App\Observers\VerificationApprovalObserver;
use App\Observers\VerificationPeriodObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        VerificationApproval::observe(VerificationApprovalObserver::class);
        VerificationPeriod::observe(VerificationPeriodObserver::class);
    }
}
