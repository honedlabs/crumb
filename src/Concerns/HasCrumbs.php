<?php

declare(strict_types=1);

namespace Honed\Crumb\Concerns;

use Honed\Crumb\Attributes\Crumb;
use Honed\Crumb\Exceptions\ClassDoesNotExtendControllerException;
use Honed\Crumb\Facades\Crumbs as Crumbs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * @mixin \Illuminate\Routing\Controller
 */
trait HasCrumbs
{
    final public function __construct()
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
            throw new ClassDoesNotExtendControllerException(\class_basename($this));
        }

        $this->middleware(function (Request $request, \Closure $next) {
            $name = $this->getCrumbName();

            // If no, don't share breadcrumbs and don't error.
            if ($name) {
                Crumbs::get($name)->share();
            }

            return $next($request);
        });
    }

    /**
     * Retrieve the crumb name to use from the method call, or class instance.
     */
    public function getCrumbName(): ?string
    {
        return match (true) {
            (bool) ($c = collect((new \ReflectionMethod($this, Route::getCurrentRoute()->getActionMethod()))
                ->getAttributes(Crumb::class)
            )->first()?->newInstance()->getCrumb()) => $c,

            (bool) ($c = collect((new \ReflectionClass($this))
                ->getAttributes(Crumb::class)
            )->first()?->newInstance()->getCrumb()) => $c,

            \property_exists($this, 'crumb') => $this->crumb,

            \method_exists($this, 'crumb') => $this->crumb,
            default => null,
        };
    }
}
