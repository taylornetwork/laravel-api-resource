<?php


namespace TaylorNetwork\LaravelApiResource;


use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->publishes([
            __DIR__.'/config' => config_path(),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/config/api_resource.php', 'api_resource');
    }
}