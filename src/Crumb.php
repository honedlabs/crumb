<?php

declare(strict_types=1);

namespace Honed\Crumb;

use Honed\Core\Concerns\HasIcon;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasRequest;
use Honed\Core\Concerns\HasUrl;
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
    use HasUrl;

    public function __construct(Request $request)
    {
        parent::__construct();

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
            return $crumb->url($route, $parameters);
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
        $url = $this->getUrl();

        return (bool) ($url ? $this->getRequest()->url() === $url : false);
    }

    /**
     * {@inheritDoc}
     */
    protected function representation(): array
    {
        return [
            'label' => $this->getLabel(),
            'url' => $this->getUrl(),
            'icon' => $this->getIcon(),
        ];
    }

    /**
     * Provide a selection of default dependencies for evaluation by name.
     *
     * @return list<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        $request = $this->getRequest();

        $parameters = Arr::mapWithKeys(
            $request->route()?->parameters() ?? [],
            static fn ($value, $key) => [$key => [$value]]
        );

        if (isset($parameters[$parameterName])) {
            return $parameters[$parameterName];
        }

        /** @var array<int, mixed> */
        return match ($parameterName) {
            'request' => [$request],
            'route' => [$request->route()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * Provide a selection of default dependencies for evaluation by type.
     *
     * @return list<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
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
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }
}
