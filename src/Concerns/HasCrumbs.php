<?php

declare(strict_types=1);

namespace Honed\Crumb\Concerns;

use Honed\Crumb\Attributes\Crumb;
use Honed\Crumb\Exceptions\ClassDoesNotExtendControllerException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

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
     * @throws ClassDoesNotExtendControllerException
     */
    public function configureCrumbs()
    {
        if (! \in_array('Illuminate\Routing\Controller', \class_parents($this))) {
            static::throwControllerExtensionException(\class_basename($this));
        }

        $name = $this->getCrumbName();

        $this->middleware('crumb:'.$name);
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
        $attribute = Arr::first(
            (new \ReflectionClass($this))->getAttributes(Crumb::class)
        );

        return $attribute?->newInstance()->getCrumbName();
    }

    /**
     * Throw an exception for when the using class is not a controller.
     *
     * @param  string  $class
     * @return never
     */
    protected static function throwControllerExtensionException($class)
    {
        throw new \LogicException(\sprintf(
            'Class [%s] does not extend the [Illuminate\Routing\Controller] controller class.',
            $class
        ));
    }
}
