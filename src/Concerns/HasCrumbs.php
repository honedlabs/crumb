<?php

declare(strict_types=1);

namespace Honed\Crumb\Concerns;

use Honed\Crumb\Crumb;
use Illuminate\Contracts\Support\Arrayable;

trait HasCrumbs
{
    /**
     * List of the crumbs.
     *
     * @var array<string,\Honed\Crumb\Trail>
     */
    protected $crumbs = [];

    /**
     * Merge a set of crumbs with existing.
     *
     * @param  iterable<\Honed\Crumb\Trail>  $crumbs
     * @return $this
     */
    public function crumbs(iterable $crumbs): static
    {
        if ($crumbs instanceof Arrayable) {
            $crumbs = $crumbs->toArray();
        }

        /**
         * @var array<int, \Honed\Crumb\Crumb> $crumbs
         */
        $this->crumbs = \array_merge($this->crumbs ?? [], $crumbs);

        return $this;
    }

    /**
     * Add a single crumb to the list of crumbs.
     *
     * @return $this
     */
    public function addCrumb(Crumb $crumb): static
    {
        $this->crumbs[] = $crumb;

        return $this;
    }

    /**
     * Retrieve the crumbs
     *
     * @return array<string,\Honed\Crumb\Trail>
     */
    public function getCrumbs(): array
    {
        return $this->crumbs;
    }

    /**
     * Determine if the instance has crumbs.
     */
    public function hasCrumbs(): bool
    {
        return filled($this->getCrumbs());
    }

    /**
     * Get the crumbs as an array.
     *
     * @return array<int,mixed>
     */
    public function crumbsToArray(): array
    {
        return \array_map(
            static fn (Crumb $crumb) => $crumb->toArray(),
            $this->getCrumbs()
        );
    }
}
