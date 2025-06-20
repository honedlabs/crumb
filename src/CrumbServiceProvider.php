<?php

declare(strict_types=1);

namespace Honed\Crumb;

use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class CrumbServiceProvider extends ServiceProvider
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
        ], 'config');

        Event::listen(RouteMatched::class, function () {
            $this->registerCrumbs();
        });
    }

    /**
     * Register the middleware alias.
     */
    protected function registerMiddleware(): void
    {
        Route::aliasMiddleware('crumb', Middleware\ShareCrumb::class);
    }

    /**
     * Register the crumbs.
     */
    protected function registerCrumbs(): void
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
}
