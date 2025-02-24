<?php

declare(strict_types=1);

namespace Honed\Crumb;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CrumbServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/crumb.php', 'crumb');

        $this->registerMiddleware();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/crumb.php' => config_path('crumb.php'),
        ], 'crumb-config');

        $this->bootCrumbs();
    }

    /**
     * Bootstrap the crumbs.
     */
    protected function bootCrumbs(): void
    {
        /**
         * @var string|array<int,string>
         */
        $files = config('crumb.files');

        if (! $files) {
            return;
        }

        if (\is_string($files) && ! \is_file($files)) {
            return;
        }

        foreach ((array) $files as $file) {
            require $file;
        }
    }

    /**
     * Register the middleware alias.
     */
    protected function registerMiddleware(): void
    {
        Route::aliasMiddleware('crumb', Middleware\ShareCrumb::class);
    }
}
