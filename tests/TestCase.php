<?php

declare(strict_types=1);

namespace Honed\Crumb\Tests;

use Honed\Crumb\CrumbServiceProvider;
use Honed\Crumb\Tests\Stubs\MethodController;
use Honed\Crumb\Tests\Stubs\ProductController;
use Honed\Crumb\Tests\Stubs\PropertyController;
use Honed\Crumb\Tests\Stubs\Status;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Routing\Middleware\SubstituteBindings;
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
        // config()->set('crumb.files', realpath(__DIR__).'../config/crumbs.php');
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
        $router->middleware(HandlesInertiaRequests::class, SubstituteBindings::class)->group(function ($router) {
            $router->get('/', fn () => inertia('Home'));

            $router->get('/products', [ProductController::class, 'index'])->name('product.index');
            $router->get('/products/{product:public_id}', [ProductController::class, 'show'])->name('product.show');
            $router->get('/products/{product}/edit', [ProductController::class, 'edit'])->name('product.edit');

            $router->get('/status/{status}', [MethodController::class, 'show'])->name('status.show');
            $router->get('/testing/{word}', [PropertyController::class, 'show'])->name('word.show');
        });
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('crumb.files', realpath(__DIR__).'/Stubs/crumbs.php');
    }
}
