<?php

namespace App\Providers;

use App\Enums\ActivityType;
use App\Enums\WeekScope;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Validator::extend('flightnr', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[A-Z]{2}[0-9]{3,4}$/', $value);
        });

        Validator::extend('airport', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[A-Z]{3,4}$/', $value);
        });

        Validator::extend('activitytype', function ($attribute, $value, $parameters, $validator) {
            if( ActivityType::tryFrom($value) == null ) return false;
            return true;
        });

        Validator::extend('weekscope', function ($attribute, $value, $parameters, $validator) {
            if( WeekScope::tryFrom($value) == null ) return false;
            return true;
        });

    }
}
