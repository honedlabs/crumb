<?php

declare(strict_types=1);

use Honed\Crumb\Crumb;
use Honed\Crumb\Facades\Crumbs;
use Honed\Crumb\Manager;
use Honed\Crumb\Trail;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\get;

it('can be accessed via facade', function () {
    expect(Crumbs::getFacadeRoot())->toBeInstanceOf(Manager::class);
});

it('autoloads from `routes` file', function () {
    expect(Crumbs::get('basic'))->toBeInstanceOf(Trail::class)
        ->toArray()->toHaveCount(1);
});

it('can set crumbs before all other crumbs', function () {
    Crumbs::before(function (Trail $trail) {
        $trail->add(Crumb::make('Products')->url('/products'));
    });

    expect(Crumbs::get('basic'))->toBeInstanceOf(Trail::class)
        ->toArray()->toHaveCount(2);
});

it('throws error if the key does not exist', function () {
    Crumbs::get('not-found');
})->throws(\InvalidArgumentException::class);

it('throws error if the key already exists', function () {
    Crumbs::for('basic', function (Trail $trail) {
        $trail->add(Crumb::make('Home', '/'));
    });
})->throws(\InvalidArgumentException::class);

it('retrieves crumbs', function () {
    $product = product();

    $response = get(route('products.show', $product));

    $response->assertInertia(fn (Assert $page) => $page->has('crumbs')
        ->count('crumbs', 3)
        ->where('crumbs.0', [
            'name' => 'Home',
            'url' => route('home'),
            'icon' => null,
        ])
        ->where('crumbs.1', [
            'name' => 'Products',
            'url' => route('products.index'),
            'icon' => null,
        ])
        ->where('crumbs.2', [
            'name' => $product->name,
            'url' => route('products.show', $product),
            'icon' => null,
        ]));
});

it('can shortcut breadcrumbs', function () {
    $response = get(route('products.index'));

    $response->assertInertia(fn (Assert $page) => $page->has('crumbs')
        ->count('crumbs', 2)
        ->where('crumbs.0', [
            'name' => 'Home',
            'url' => route('home'),
            'icon' => null,
        ])
        ->where('crumbs.1', [
            'name' => 'Products',
            'url' => route('products.index'),
            'icon' => null,
        ]));
});
