<?php

declare(strict_types=1);

namespace Honed\Crumb;

use Honed\Core\Primitive;
use Honed\Crumb\Exceptions\NonTerminatingCrumbException;
use Honed\Crumb\Support\Parameters;
use Illuminate\Support\Arr;
use Inertia\Inertia;

/**
 * @extends \Honed\Core\Primitive<string, mixed>
 */
class Trail extends Primitive
{
    use Concerns\IsTerminable;
    use Concerns\HasCrumbs;

    /**
     * Make a new trail instance.
     * 
     * @param  array<int,\Honed\Crumb\Crumb>  $crumbs
     */
    public static function make(...$crumbs): static
    {
        return resolve(static::class)
            ->crumbs($crumbs);
    }

    /**
     * Get the trail as an array.
     */
    public function toArray(): array
    {
        return $this->crumbsToArray();
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
        
        $this->terminate($this->isTerminating() && $crumb->isCurrent());

        return $this;
    }

    /**
     * Select and add the first matching crumb to the trail.
     *
     * @return $this
     *
     * @throws NonTerminatingCrumbException
     */
    public function select(Crumb ...$crumbs): static
    {
        if ($this->hasTerminated()) {
            return $this;
        }

        if (! $this->isTerminating()) {
            throw new NonTerminatingCrumbException;
        }

        $crumb = Arr::first($crumbs, fn (Crumb $crumb): bool => $crumb->isCurrent());

        if ($crumb) {
            $this->crumbs[] = $crumb;
            $this->terminated = true;
        }

        return $this;
    }

    /**
     * Share the crumbs with Inertia.
     *
     * @return $this
     */
    public function share(): static
    {
        Inertia::share(Parameters::Prop, $this->getCrumbs());

        return $this;
    }
}
