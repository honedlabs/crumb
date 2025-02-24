<?php

declare(strict_types=1);

use Honed\Crumb\Crumb;
use Honed\Crumb\Facades\Crumbs;
use Honed\Crumb\Tests\Stubs\Product;
use Honed\Crumb\Tests\Stubs\Status;
use Honed\Crumb\Trail;

Crumbs::for('basic', fn (Trail $trail) => $trail
    ->add('Home', 'home')
);

Crumbs::for('products', fn (Trail $trail) => $trail
    ->add('Home', 'home')
    ->add('Products', 'products.index')
    ->select(
        Crumb::make(
            fn (Product $product) => $product->name,
            fn (Product $product) => route('products.show', $product)
        ),
        Crumb::make(
            fn (Product $product) => \sprintf('Edit %s', $product->name),
            fn (Product $product) => route('product.edit', $product)
        )
    )
);

Crumbs::for('status', fn (Trail $trail) => $trail
    ->add('Home', 'home')
    ->add(Crumb::make('Statuses')->url('/statuses')) // A page which does not exist
    ->add(Crumb::make(
        fn (Status $status) => $status->value,
        fn (Status $status) => route('status.show', $status))
    )
);
