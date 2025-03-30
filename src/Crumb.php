<?php

declare(strict_types=1);

namespace Honed\Crumb;

use Honed\Core\Concerns\HasIcon;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasRequest;
use Honed\Core\Concerns\HasRoute;
use Honed\Core\Primitive;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

class Crumb extends Primitive
{
    use HasIcon;
    use HasLabel;
    use HasRequest;
    use HasRoute;

    public function __construct(Request $request)
    {
        $this->request($request);
    }

    /**
     * Make a new crumb instance.
     *
     * @param  string|\Closure(mixed...):string  $label
     * @param  string|\Closure(mixed...):string|null  $route
     * @param  mixed  $parameters
     * @return $this
     */
    public static function make($label, $route = null, $parameters = [])
    {
        $crumb = resolve(static::class)
            ->label($label);

        if ($route) {
            return $crumb->route($route, $parameters);
        }

        return $crumb;
    }

    /**
     * Determine if the current route corresponds to this crumb.
     *
     * @return bool
     */
    public function isCurrent()
    {
        $route = $this->resolveRoute();

        return (bool) ($route ? $this->getRequest()->url() === $route : false);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return [
            'label' => $this->getLabel(),
            'url' => $this->resolveRoute(),
            'icon' => $this->getIcon(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName($parameterName)
    {
        $request = $this->getRequest();

        $parameters = Arr::mapWithKeys(
            $request->route()?->parameters() ?? [],
            static fn ($value, $key) => [$key => [$value]]
        );

        /** @var array<int, mixed> */
        return match ($parameterName) {
            'request' => [$request],
            'route' => [$request->route()],
            default => Arr::get(
                $parameters,
                $parameterName,
                parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
            ),
        };
    }

    /**
     * {@inheritDoc}
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType($parameterType)
    {
        $request = $this->getRequest();

        $parameters = Arr::mapWithKeys(
            $request->route()?->parameters() ?? [],
            static fn ($value) => \is_object($value)
                ? [\get_class($value) => [$value]]
                : [],
        );

        /** @var array<int, mixed> */
        return match ($parameterType) {
            Request::class => [$request],
            Route::class => [$request->route()],
            default => Arr::get(
                $parameters,
                $parameterType,
                [App::make($parameterType)],
            ),
        };
    }
}
