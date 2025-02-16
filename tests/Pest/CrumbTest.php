<?php

declare(strict_types=1);

use Honed\Crumb\Crumb;
use Honed\Crumb\Tests\Stubs\Product;
use Honed\Crumb\Tests\Stubs\Status;

use function Pest\Laravel\get;

it('can be made', function () {
    expect(Crumb::make('Home', 'home'))->toBeInstanceOf(Crumb::class)
        ->getRoute()->toBe(route('home'))
        ->getName()->toBe('Home')
        ->hasIcon()->toBeFalse();
});

it('has array representation', function () {
    expect(Crumb::make('Home', 'home')->toArray())
        ->toEqual([
            'name' => 'Home',
            'url' => route('home'),
            'icon' => null,
        ]);
});

it('can resolve route model binding', function () {
    $p = product();

    get(route('products.show', $p));

    $crumb = Crumb::make(
        fn ($product) => $product->name, 
        fn (Product $typed) => route('products.show', $typed)
    );

    expect($crumb->toArray())
        ->toEqual([
            'name' => $p->name,
            'url' => route('products.show', $p),
            'icon' => null,
        ]); 
});

it('can resolve route enum binding when retrieving array form', function () {
    $e = Status::AVAILABLE;

    get(route('status.show', $e));

    $crumb = Crumb::make(fn ($status) => sprintf('Status: %s', $status->value), fn (Status $typed) => route('status.show', $typed));

    expect($crumb->toArray())
        ->toEqual([
            'name' => \sprintf('Status: %s', $e->value),
            'url' => route('status.show', $e),
            'icon' => null,
        ]);
});

it('can resolve other bindings', function () {
    $s = 'test';

    get(route('word.show', $s));

    $crumb = Crumb::make(fn ($word) => sprintf('Given %s', $word), fn ($word) => route('word.show', $word));

    expect($crumb->toArray())
        ->toEqual([
            'name' => \sprintf('Given %s', $s),
            'url' => route('word.show', $s),
            'icon' => null,
        ]);
});

it('checks if a crumb is current', function () {
    // Basic
    get(route('home'));

    $crumb = Crumb::make('Home', 'home');
    expect($crumb->isCurrent())->toBeTrue();

    // With binding
    $a = product();
    get(route('products.show', $a));

    $crumb = Crumb::make('View', fn (Product $product) => route('products.show', $product));
    expect($crumb->isCurrent())->toBeTrue();

    // Same route, different binding
    $b = product();
    get(route('products.show', $b));

    $crumb = Crumb::make('View', 'products.show', $a);
    expect($crumb->isCurrent())->toBeFalse();    
});
