<?php

declare(strict_types=1);

namespace Honed\Crumb\Tests\Pest;

use Honed\Crumb\Crumb;
use Honed\Crumb\Trail;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\get;

it('makes', function () {
    $trail = Trail::make(Crumb::make('Home', 'home'));

    expect($trail)->toBeInstanceOf(Trail::class)
        ->hasCrumbs()->toBeTrue()
        ->getCrumbs()->toEqual([
            Crumb::make('Home', 'home'),
        ]);
});

it('has array representation', function () {
    $crumb = Crumb::make('Home', 'home');

    expect(Trail::make($crumb)->toArray())
        ->toEqual([
            $crumb->toArray(),
        ]);
});

it('adds', function () {
    $trail = Trail::make()
        ->add(Crumb::make('Home', 'home'));

    expect($trail)->toBeInstanceOf(Trail::class)
        ->hasCrumbs()->toBeTrue()
        ->getCrumbs()->toEqual([
            Crumb::make('Home', 'home'),
        ]);
});

it('shares crumbs', function () {
    expect(Trail::make(Crumb::make('Home', 'home'))->share())
        ->toBeInstanceOf(Trail::class);

    $response = get(route('home'));

    $response->assertInertia(fn (Assert $page) => $page->has('crumbs')
        ->count('crumbs', 1)
        ->where('crumbs.0', [
            'label' => 'Home',
            'url' => route('home'),
            'icon' => null,
        ]));
});

it('selects', function () {
    $product = product();

    get(route('products.show', $product));

    $trail = Trail::make()
        ->terminating()
        ->add('Products', 'products.index')
        ->select(
            Crumb::make('Edit', fn ($product) => route('products.edit', $product)),
            Crumb::make('Show', fn ($product) => route('products.show', $product)),
        );

    expect($trail->getCrumbs())
        ->toHaveCount(2)
        ->{0}->scoped(fn ($crumb) => $crumb
            ->isCurrent()->toBeFalse()
            ->getLabel()->toBe('Products')
            ->getRoute()->toBe(route('products.index'))
        )
        ->{1}->scoped(fn ($crumb) => $crumb
            ->isCurrent()->toBeTrue()
            ->getLabel()->toBe('Show')
            ->getRoute()->toBe(route('products.show', $product))
        );
});

// it('throws error if trying to access non-terminating trail', function () {
//     Trail::make(Crumb::make('Home', '/'))->select(Crumb::make('Products', '/products'));
// })->throws(NonTerminatingCrumbException::class);
