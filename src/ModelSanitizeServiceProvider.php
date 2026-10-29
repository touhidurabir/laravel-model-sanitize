<?php

namespace Touhidurabir\ModelSanitize;

use Illuminate\Support\ServiceProvider;

class ModelSanitizeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('model-sanitize', function () {
            return new ModelSanitizer();
        });

        $this->app->alias('model-sanitize', ModelSanitizer::class);
    }


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

}
