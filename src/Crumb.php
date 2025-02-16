<?php

declare(strict_types=1);

namespace Honed\Crumb;

use Honed\Core\Primitive;
use Illuminate\Http\Request;
use Honed\Core\Concerns\HasIcon;
use Honed\Core\Concerns\HasName;
use Honed\Core\Concerns\HasRequest;
use Honed\Core\Concerns\HasRoute;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;

/**
 * @extends \Honed\Core\Primitive<string, mixed>
 */
class Crumb extends Primitive
{
    use HasIcon;
    use HasName;
    use HasRoute;
    use HasRequest;

    public function __construct(Request $request)
    {
        $this->request($request);
    }

    /**
     * Make a new crumb instance.
     *
     * @return $this
     */
    public static function make(string|\Closure $name, string|\Closure $link = null, mixed $parameters = []): static
    {
        return resolve(static::class)
            ->name($name)
            ->route($link, $parameters);
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(array $parameters = [], array $typed = []): static
    {
        $this->resolveName($parameters, $typed);
        $this->resolveRoute($parameters, $typed);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        $this->resolve();

        return [
            'name' => $this->getName(),
            'url' => $this->getRoute(),
            'icon' => $this->getIcon(),
        ];
    }

    /**
     * Determine if the current route corresponds to this crumb.
     */
    public function isCurrent(): bool
    {
        $route = $this->resolveRoute();

        return (bool) ($route ? $this->getRequest()->url() === $route : false);
    }

    /**
     * {@inheritDoc}
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        $request = $this->getRequest();

        $parameters = Arr::mapWithKeys(
            $request->route()?->parameters() ?? [],
            static fn ($value, $key) => [$key => [$value]]
        );

        return match ($parameterName) {
            'request' => [$request],
            'route' => [$request->route()],
            default => Arr::get($parameters, $parameterName, []),
        };
    }

    /**
     * {@inheritDoc}
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $request = $this->getRequest();

        $parameters = Arr::mapWithKeys(
            $request->route()?->parameters() ?? [],
            $this->getTypedParameters(...)
        );

        return match ($parameterType) {
            Request::class => [$request],
            Route::class => [$request->route()],
            default => Arr::get($parameters, $parameterType, []),
        };
    }

    /**
     * Retrieve the classes for evaluation by type.
     * 
     * @return array<class-string, mixed>
     */
    protected function getTypedParameters(mixed $value): array
    {
        return \is_object($value) ? [\get_class($value) => [$value]] : [];
    }
}
