<?php

declare(strict_types=1);

namespace Honed\Crumb\Concerns;

use Honed\Crumb\Attributes\Crumb;
use Honed\Crumb\Exceptions\ClassDoesNotExtendControllerException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

trait Crumbs
{
    public function __construct()
    {
        $this->configureCrumbs();
    }

    /**
     * Share the crumbs relevant to the current request to your Inertia view.
     *
     * @throws ClassDoesNotExtendControllerException
     */
    public function configureCrumbs(): void
    {
        if (! \in_array('Illuminate\Routing\Controller', \class_parents($this))) {
            static::throwControllerExtensionException(\class_basename($this));
        }

        $name = $this->getCrumbName();

        $this->middleware('crumb:'.$name);
    }

    /**
     * Retrieve the crumb name to use from the method call, or class instance.
     */
    public function getCrumbName(): ?string
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
     */
    protected function getMethodCrumbAttribute(): ?string
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
     */
    protected function getClassCrumbAttribute(): ?string
    {
        $attribute = Arr::first(
            (new \ReflectionClass($this))->getAttributes(Crumb::class)
        );

        return $attribute?->newInstance()->getCrumbName();
    }

    /**
     * Throw an exception for when the using class is not a controller.
     */
    protected static function throwControllerExtensionException(string $class): never
    {
        throw new \LogicException(\sprintf(
            'Class [%s] does not extend the [Illuminate\Routing\Controller] controller class.',
            $class
        ));
    }
}
