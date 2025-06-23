<?php

declare(strict_types=1);

namespace Honed\Crumb;

use BadMethodCallException;
use Closure;
use Honed\Core\Primitive;
use Honed\Crumb\Exceptions\TrailCannotTerminateException;
use Honed\Crumb\Support\Constants;
use Illuminate\Support\Arr;
use Inertia\Inertia;

use function array_map;
use function array_merge;

class Trail extends Primitive
{
    /**
     * List of the crumbs.
     *
     * @var array<int,Crumb>
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
     * @param  Crumb|iterable<int,Crumb>  ...$crumbs
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
     * @param  Crumb|iterable<int,Crumb>  ...$crumbs
     * @return $this
     */
    public function crumbs(...$crumbs)
    {
        $crumbs = Arr::flatten($crumbs);

        $this->crumbs = array_merge($this->crumbs, $crumbs);

        return $this;
    }

    /**
     * Append crumbs to the end of the crumb trail.
     *
     * @param  Crumb|Closure|string  $crumb
     * @param  Closure|string|null  $link
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
     * @param  Crumb  ...$crumbs
     * @return $this
     *
     * @throws BadMethodCallException
     */
    public function select(...$crumbs)
    {
        if ($this->terminated) {
            return $this;
        }

        if (! $this->terminating) {
            TrailCannotTerminateException::throw();
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
     * Retrieve the crumbs
     *
     * @return array<int,Crumb>
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
        Inertia::share(Constants::PROP, $this->toArray());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array_map(
            static fn (Crumb $crumb) => $crumb->toArray(),
            $this->getCrumbs()
        );
    }

    /**
     * Add a single crumb to the list of crumbs.
     *
     * @param  Crumb  $crumb
     * @return $this
     */
    protected function addCrumb($crumb)
    {
        $this->crumbs[] = $crumb;

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
}
