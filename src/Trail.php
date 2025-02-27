<?php

declare(strict_types=1);

namespace Honed\Crumb;

use Honed\Core\Primitive;
use Honed\Crumb\Support\Parameters;
use Illuminate\Support\Arr;
use Inertia\Inertia;

/**
 * @extends \Honed\Core\Primitive<string, mixed>
 */
class Trail extends Primitive
{
    use Concerns\HasCrumbs;
    use Concerns\IsTerminable;

    /**
     * Make a new trail instance.
     */
    public static function make(Crumb ...$crumbs): static
    {
        return resolve(static::class)
            ->crumbs($crumbs);
    }

    /**
     * Append crumbs to the end of the crumb trail.
     *
     * @return $this
     */
    public function add(string|\Closure|Crumb $crumb, string|\Closure|null $link = null, mixed $parameters = []): static
    {
        if ($this->hasTerminated()) {
            return $this;
        }

        $crumb = $crumb instanceof Crumb ? $crumb : Crumb::make($crumb, $link, $parameters);

        $this->addCrumb($crumb);

        $this->terminate($crumb->isCurrent());

        return $this;
    }

    /**
     * Select and add the first matching crumb to the trail.
     *
     * @return $this
     *
     * @throws \BadMethodCallException
     */
    public function select(Crumb ...$crumbs): static
    {
        if ($this->hasTerminated()) {
            return $this;
        }

        if (! $this->isTerminating()) {
            static::throwNonTerminatingCrumbException();
        }

        $crumb = Arr::first(
            $crumbs,
            static fn (Crumb $crumb): bool => $crumb->isCurrent()
        );

        if ($crumb) {
            $this->addCrumb($crumb);
            $this->terminate();
        }

        return $this;
    }

    /**
     * Get the trail as an array.
     */
    public function toArray(): array
    {
        return $this->crumbsToArray();
    }

    /**
     * Share the crumbs with Inertia.
     *
     * @return $this
     */
    public function share()
    {
        Inertia::share(Parameters::Prop, $this->toArray());

        return $this;
    }

    /**
     * Throw an exception if `select` is called on a non-terminating crumb.
     *
     * @return never
     */
    protected static function throwNonTerminatingCrumbException()
    {
        throw new \BadMethodCallException(
            'This method is only available on terminating crumbs.'
        );
    }
}
