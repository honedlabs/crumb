<?php

declare(strict_types=1);

namespace Honed\Crumb\Concerns;

use Honed\Crumb\Attributes\Trail;
use Honed\Crumb\Exceptions\ControllerExtensionException;
use Honed\Crumb\Middleware\ShareCrumb;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

/**
 * @phpstan-require-extends \Illuminate\Routing\Controller
 */
trait HasCrumbs
{
    public function __construct()
    {
        $this->configureCrumbs();
    }

    /**
     * Share the crumbs relevant to the current request to your Inertia view.
     *
     * @return void
     *
     * @throws \Honed\Crumb\Exceptions\ControllerExtensionException
     */
    public function configureCrumbs()
    {
        $name = $this->getCrumbName();

        $this->middleware(ShareCrumb::trail($name));
    }

    /**
     * Retrieve the crumb name to use from the method call, or class instance.
     *
     * @return string|null
     */
    public function getCrumbName()
    {
        return match (true) {
            (bool) ($name = $this->getMethodCrumbAttribute()) => $name,
            (bool) ($name = $this->getClassCrumbAttribute()) => $name,
            isset($this->crumb) => $this->crumb,
            \method_exists($this, 'crumb') => $this->crumb(),
            default => null,
        };
    }

    /**
     * Get the crumb attribute on the active method.
     *
     * @return string|null
     */
    protected function getMethodCrumbAttribute()
    {
        $action = Route::getCurrentRoute()?->getActionMethod();

        if (! $action) {
            return null;
        }
        /** @var \ReflectionAttribute<\Honed\Crumb\Attributes\Crumb>|null $attribute */
        $attribute = Arr::first(
            (new \ReflectionMethod($this, $action))->getAttributes(Crumb::class)
        );

        return $attribute?->newInstance()->getCrumbName();
    }

    /**
     * Get the crumb attribute on the class.
     *
     * @return string|null
     */
    protected function getClassCrumbAttribute()
    {
        $trail = Arr::first(
            (new \ReflectionClass($this))->getAttributes(Trail::class)
        );

        return $trail?->newInstance()->getTrail();
    }
}
