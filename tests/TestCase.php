<?php

declare(strict_types=1);

namespace Honed\Crumb\Tests;

use Honed\Crumb\CrumbServiceProvider;
use Honed\Crumb\Tests\Fixtures\MethodController;
use Honed\Crumb\Tests\Fixtures\ProductController;
use Honed\Crumb\Tests\Fixtures\PropertyController;
use Honed\Crumb\Tests\Stubs\Status;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Inertia\Middleware as HandlesInertiaRequests;
use Inertia\ServiceProvider as InertiaServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        View::addLocation(__DIR__.'/Stubs');
        Inertia::setRootView('app');

        config()->set('inertia.testing.ensure_pages_exist', false);
        config()->set('inertia.testing.page_paths', [realpath(__DIR__)]);

        $this->withoutExceptionHandling();
    }

    protected function getPackageProviders($app)
    {
        return [
            CrumbServiceProvider::class,
            InertiaServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default(Status::AVAILABLE->value);
            $table->unsignedInteger('price')->default(0);
            $table->boolean('best_seller')->default(false);
            $table->timestamps();
        });
    }

    protected function defineRoutes($router)
    {
        $router->middleware(HandlesInertiaRequests::class, SubstituteBindings::class)->group(function (Router $router) {
            $router->get('/', fn () => inertia('Home'))->name('home');

            $router->resource('products', ProductController::class)
                ->only('index', 'show', 'edit');

            $router->get('/status/{status}', [MethodController::class, 'show'])->name('status.show');
            $router->get('/testing/{word}', [PropertyController::class, 'show'])->name('word.show');
        });
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('crumb.files', realpath(__DIR__).'/Fixtures/crumbs.php');
    }
}
