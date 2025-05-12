<?php

declare(strict_types=1);

namespace Honed\Crumb;

use Honed\Core\Primitive;
use Honed\Crumb\Support\Parameters;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class Trail extends Primitive
{
    /**
     * List of the crumbs.
     *
     * @var array<int,\Honed\Crumb\Crumb>
     */
    protected $crumbs = [];

    /**
     * Whether the trail can terminate.
     *
     * @var bool
     */
    protected $terminating = false;

    /**
     * Whether the trail has terminated.
     *
     * @var bool
     */
    protected $terminated = false;

    /**
     * Make a new trail instance.
     *
     * @param  \Honed\Crumb\Crumb|iterable<int,\Honed\Crumb\Crumb>  ...$crumbs
     * @return static
     */
    public static function make(...$crumbs)
    {
        return resolve(static::class)
            ->crumbs($crumbs);
    }

    /**
     * Merge a set of crumbs with existing.
     *
     * @param  \Honed\Crumb\Crumb|iterable<int,\Honed\Crumb\Crumb>  ...$crumbs
     * @return $this
     */
    public function crumbs(...$crumbs)
    {
        $crumbs = Arr::flatten($crumbs);

        $this->crumbs = \array_merge($this->crumbs, $crumbs);

        return $this;
    }

    /**
     * Append crumbs to the end of the crumb trail.
     *
     * @param  \Honed\Crumb\Crumb|\Closure|string  $crumb
     * @param  \Closure|string|null  $link
     * @param  mixed  $parameters
     * @return $this
     */
    public function add($crumb, $link = null, $parameters = [])
    {
        if ($this->terminated) {
            return $this;
        }

        $crumb = $crumb instanceof Crumb
            ? $crumb
            : Crumb::make($crumb, $link, $parameters);

        $this->crumbs[] = $crumb;

        $this->terminated = $crumb->isCurrent();

        return $this;
    }

    /**
     * Select and add the first matching crumb to the trail.
     *
     * @param  \Honed\Crumb\Crumb  ...$crumbs
     * @return $this
     *
     * @throws \BadMethodCallException
     */
    public function select(...$crumbs)
    {
        if ($this->terminated) {
            return $this;
        }

        if (! $this->terminating) {
            static::throwNonTerminatingCrumbException();
        }

        $crumb = Arr::first(
            $crumbs,
            static fn (Crumb $crumb): bool => $crumb->isCurrent()
        );

        if ($crumb) {
            $this->crumbs[] = $crumb;
            $this->terminated = true;
        }

        return $this;
    }

    /**
     * Add a single crumb to the list of crumbs.
     *
     * @param  \Honed\Crumb\Crumb  $crumb
     * @return $this
     */
    protected function addCrumb($crumb)
    {
        $this->crumbs[] = $crumb;

        return $this;
    }

    /**
     * Retrieve the crumbs
     *
     * @return array<int,\Honed\Crumb\Crumb>
     */
    public function getCrumbs()
    {
        return $this->crumbs;
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
     * Set the trail to terminate when a crumb in the trail matches.
     *
     * @param  bool  $terminating
     * @return $this
     */
    protected function terminating($terminating = true)
    {
        $this->terminating = $terminating;

        return $this;
    }

    /**
     * Determine if the trail is terminating.
     *
     * @return bool
     */
    protected function isTerminating()
    {
        return $this->terminating;
    }

    /**
     * Set the trail to have been terminated.
     *
     * @return $this
     */
    protected function terminate()
    {
        $this->terminated = true;
    }

    /**
     * Determine if the trail has been terminated.
     *
     * @return bool
     */
    protected function isTerminated()
    {
        return $this->terminated;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return \array_map(
            static fn (Crumb $crumb) => $crumb->toArray(),
            $this->getCrumbs()
        );
    }

    /**
     * Throw an exception if `select` is called on a non-terminating crumb.
     *
     * @return never
     *
     * @throws \BadMethodCallException
     */
    public static function throwNonTerminatingCrumbException()
    {
        throw new \BadMethodCallException(
            'This method is only available on terminating crumbs.'
        );
    }
}
