<?php

declare(strict_types=1);

namespace Honed\Crumb;

use Honed\Core\Concerns\HasIcon;
use Honed\Core\Concerns\HasMeta;
use Honed\Core\Concerns\HasName;
use Honed\Core\Primitive;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

class Crumb extends Primitive
{
    use HasIcon;
    use HasMeta;
    use HasName;

    /**
     * Parameters used to resolve the link from.
     *
     * @var non-empty-array<int,array<string,mixed>>|null
     */
    protected $parameters;

    /**
     * The link for the crumb.
     *
     * @var string|(\Closure(mixed...):string)|null
     */
    protected $link;

    /**
     * Create a new crumb instance.
     *
     * @param  string|(\Closure(mixed...):string)  $name
     * @param  string|(\Closure(mixed...):string)|null  $link
     */
    public function __construct(
        string|\Closure $name,
        string|\Closure|null $link = null,
        ?string $icon = null,
    ) {
        $this->setName($name);
        $this->setLink($link);
        $this->setIcon($icon);
    }

    /**
     * Make a new crumb instance.
     *
     * @param  string|(\Closure(mixed...):string)  $name
     * @param  string|(\Closure(mixed...):string)|null  $link
     * @return $this
     */
    public static function make(string|\Closure $name, string|\Closure|null $link = null, ?string $icon = null): static
    {
        return resolve(static::class, compact('name', 'link', 'icon'));
    }

    /**
     * Get the crumb as an array
     *
     * @return non-empty-array<'name'|'url'|'icon',string|null>
     */
    public function toArray(): array
    {
        [$named, $typed] = $this->getClosureParameters();

        return [
            'name' => $this->getName($named, $typed),
            'url' => $this->getLink($named, $typed),
            ...($this->hasIcon() ? ['icon' => $this->getIcon()] : []),
        ];
    }

    /**
     * Set the link as a route, quietly.
     *
     * @param  array<array-key, mixed>  $parameters
     */
    public function setRoute(string $name, mixed $parameters = [], bool $absolute = true): void
    {
        $this->link = route($name, $parameters, $absolute);
    }

    /**
     * Set the route as a route, chainable.
     *
     * @param  array<array-key, mixed>  $parameters
     * @return $this
     */
    public function route(string $name, mixed $parameters = [], bool $absolute = true): static
    {
        $this->setRoute($name, $parameters, $absolute);

        return $this;
    }

    /**
     * Set the url, quietly.
     *
     * @param  string|(\Closure(mixed...):string)  $url
     */
    public function setUrl(string|\Closure $url): void
    {
        $this->link = $url;
    }

    /**
     * Set the url, chainable.
     *
     * @param  string|(\Closure(mixed...):string)  $url
     * @return $this
     */
    public function url(string|\Closure $url): static
    {
        $this->setUrl($url);

        return $this;
    }

    /**
     * Set the link, quietly.
     *
     * @param  string|(\Closure(mixed...):string)|null  $link
     * @param  array<array-key, mixed>  $parameters
     */
    public function setLink(string|\Closure|null $link, mixed $parameters = [], bool $absolute = true): void
    {
        if (\is_null($link)) {
            return;
        }

        match (true) {
            \is_callable($link) => $this->link = $link,
            $this->isUrl($link) => $this->setUrl($link),
            default => $this->setRoute($link, $parameters, $absolute),
        };
    }

    /**
     * Set the link, chainable.
     *
     * @param  string|(\Closure(mixed...):string)  $link
     * @param  array<array-key, mixed>  $parameters
     * @return $this
     */
    public function link(string|\Closure $link, mixed $parameters = [], bool $absolute = true): static
    {
        $this->setLink($link, $parameters, $absolute);

        return $this;
    }

    /**
     * Determine if the crumb has a link.
     */
    public function hasLink(): bool
    {
        return ! \is_null($this->link);
    }

    /**
     * Retrieve the link, evaluating it if it is a closure.
     *
     * @param  array<string, mixed>  $named
     * @param  array<string, mixed>  $typed
     */
    public function getLink(array $named = [], array $typed = []): ?string
    {
        return $this->evaluate($this->link, $named, $typed);
    }

    /**
     * Determine if the current route corresponds to this crumb.
     */
    public function isCurrent(): bool
    {
        [$named, $typed] = $this->getClosureParameters();

        $this->link = $this->getLink($named, $typed);

        return (bool) $this->link ? Request::url() === url($this->link) : false;
    }

    /**
     * Determine if the link is a url, or a named route.
     *
     * @internal
     */
    protected function isUrl(string $link): bool
    {
        return str($link)->startsWith(['http', 'https', '/', '#', 'www.']);
    }

    /**
     * Get the binding parameters to pass to a closure.
     *
     * @return non-empty-array<int,array<string,mixed>>
     */
    protected function getClosureParameters(): array
    {
        return $this->parameters ??= $this->makeClosureParameters();
    }

    /**
     * Get the binding parameters from the current route actions.
     *
     * @return non-empty-array<int,array<string,mixed>>
     */
    protected function makeClosureParameters(): array
    {
        $parameters = Route::current()?->parameters() ?? [];
        $request = Request::capture();
        $route = Route::current();
        $values = \array_values($parameters);

        $mapped = \array_combine(
            \array_map(static fn ($value) => \is_object($value) ? \get_class($value) : \gettype($value), $values),
            $values
        );

        return [
            [
                'request' => $request,
                'route' => $route,
                ...$parameters,
            ],
            [
                Request::class => $request,
                Route::class => $route,
                ...$mapped,
            ],
        ];
    }
}
