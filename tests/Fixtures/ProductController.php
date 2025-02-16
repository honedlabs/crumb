<?php

declare(strict_types=1);

namespace Honed\Crumb\Tests\Fixtures;

use Honed\Crumb\Attributes\Crumb;
use Honed\Crumb\Concerns\Crumbs;
use Honed\Crumb\Tests\Stubs\Product;
use Illuminate\Routing\Controller;

#[Crumb('products')]
class ProductController extends Controller
{
    use Crumbs;

    public function index()
    {
        return inertia('Product/Index');
    }

    #[Crumb('products')]
    public function show(Product $product)
    {
        return inertia('Product/Show', [
            'product' => $product,
        ]);
    }

    public function edit(Product $product)
    {
        return inertia('Product/Edit', [
            'product' => $product,
        ]);
    }
}
