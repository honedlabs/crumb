<?php

declare(strict_types=1);

use Honed\Crumb\Crumb;
use Honed\Crumb\Facades\Crumbs;
use Honed\Crumb\Trail;
use Honed\Crumb\TrailManager;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\get;

beforeEach(function () {
    $this->crumb = 'basic';
});

it('can be accessed via facade', function () {
    expect(Crumbs::getFacadeRoot())->toBeInstanceOf(TrailManager::class);
});

it('autoloads from `routes` file', function () {
    get('/');
    expect(Crumbs::get($this->crumb))
        ->toBeInstanceOf(Trail::class)
        ->toArray()
        ->toHaveCount(1);
});

it('can set crumbs before all other crumbs', function () {
    get('/');

    Crumbs::before(function (Trail $trail) {
        $trail->add(Crumb::make('Products')->url('/products'));
    });

    expect(Crumbs::get($this->crumb))
        ->toBeInstanceOf(Trail::class)
        ->toArray()->toHaveCount(2);
});

it('throws error if the key does not exist', function () {
    get('/');
    Crumbs::get('not-found');
})->throws(InvalidArgumentException::class);

it('throws error if the key already exists', function () {
    get('/');
    Crumbs::for($this->crumb, function (Trail $trail) {
        $trail->add(Crumb::make('Home', '/'));
    });
})->throws(InvalidArgumentException::class);

it('retrieves crumbs', function () {
    $product = product();
    $response = get(route('products.show', $product));

    $response->assertInertia(fn (Assert $page) => $page->has('crumbs')
        ->count('crumbs', 3)
        ->where('crumbs.0', [
            'label' => 'Home',
            'url' => route('home'),
            'icon' => null,
        ])
        ->where('crumbs.1', [
            'label' => 'Products',
            'url' => route('products.index'),
            'icon' => null,
        ])
        ->where('crumbs.2', [
            'label' => $product->name,
            'url' => route('products.show', $product),
            'icon' => null,
        ]));
});

it('can shortcut breadcrumbs', function () {
    $response = get(route('products.index'));

    $response->assertInertia(fn (Assert $page) => $page->has('crumbs')
        ->count('crumbs', 2)
        ->where('crumbs.0', [
            'label' => 'Home',
            'url' => route('home'),
            'icon' => null,
        ])
        ->where('crumbs.1', [
            'label' => 'Products',
            'url' => route('products.index'),
            'icon' => null,
        ]));
});
