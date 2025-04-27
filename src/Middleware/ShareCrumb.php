<?php

declare(strict_types=1);

namespace Honed\Crumb\Middleware;

use Honed\Crumb\Facades\Crumbs;

class ShareCrumb
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $crumb
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, $next, $crumb = null)
    {
        $crumb ??= $this->getDefaultCrumb();

        if ($crumb) {
            Crumbs::get($crumb)->share();
        }

        return $next($request);
    }

    /**
     * Retrieve the default crumb to use if none is provided.
     *
     * @return string|null
     */
    protected function getDefaultCrumb()
    {
        /** @var string|null */
        return config('crumb.default');
    }
}
