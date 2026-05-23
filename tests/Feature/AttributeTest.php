<?php

declare(strict_types=1);

use Honed\Crumb\Attributes\Crumb;
use Honed\Crumb\Tests\Fixtures\ProductController;

it('can be set on a class', function () {
    expect(collect((new ReflectionClass(ProductController::class))
        ->getAttributes(Crumb::class)
    )->first()->newInstance()->getCrumbName())->toBe('products');
});

it('can be set on a method', function () {
    expect(collect((new ReflectionMethod(ProductController::class, 'show'))
        ->getAttributes(Crumb::class)
    )->first()->newInstance()->getCrumbName())->toBe('products');
});
