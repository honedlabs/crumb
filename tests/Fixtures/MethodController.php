<?php

declare(strict_types=1);

namespace Honed\Crumb\Tests\Fixtures;

use Honed\Crumb\Concerns\HasCrumbs;
use Honed\Crumb\Tests\Stubs\Status;
use Illuminate\Routing\Controller;

class MethodController extends Controller
{
    use HasCrumbs;

    public function show(Status $status)
    {
        return inertia('Status/Show', ['status' => $status]);
    }

    public function crumb()
    {
        return 'status';
    }
}
