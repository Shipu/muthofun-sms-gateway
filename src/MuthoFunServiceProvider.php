<?php

namespace Shipu\MuthoFun;

use Illuminate\Container\Container;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Shipu\MuthoFun\Facades\MuthoFun as MuthoFunFacade;
use Laravel\Lumen\Application as LumenApplication;

class MuthoFunServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->setupConfig();
    }
    /**
     * Register the application services.
     */
    public function register()
    {
        $this->registerMuthoFun();
    }
    /**
     * Setup the config.
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/../config/muthofun.php');
        // Check if the application is a Laravel OR Lumen instance to properly merge the configuration file.
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('muthofun.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('muthofun');
        }
        $this->mergeConfigFrom($source, 'muthofun');
    }
    /**
     * Register MuthoFun class.
     */
    protected function registerMuthoFun()
    {
        $this->app->bind('muthofun', function (Container $app) {
            return new MUTHOFUN($app['config']->get('muthofun'));
        });
        $this->app->alias('muthofun', MuthoFunFacade::class);
    }
    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            'muthofun'
        ];
    }
}
