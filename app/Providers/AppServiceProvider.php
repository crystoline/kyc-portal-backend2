<?php

namespace App\Providers;

use App\Models\VerificationApproval;
use App\Observers\VerificationApprovalObserver;
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
    }
}
