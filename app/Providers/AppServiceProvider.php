<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
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
        Paginator::useBootstrap();

        // Add additional methods to Collections

        // Get the localized name of an \App\Element contained by a collection of \App\Value
        Collection::macro('getLocalizedName', function ($element) {
            return optional($this->firstWhere('element_fk', $element))->value ?? __('common.missing_translation');
        });
    }
}
