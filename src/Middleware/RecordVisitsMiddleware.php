<?php

namespace Niladam\LaravelVisits\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RecordVisitsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $value = $request->route()?->parameters();

        $visitable = $this->isModel($value)
            ? $value[array_key_first($value)]
            : $request->fullUrlWithQuery($request->query());

        recordVisit($visitable);

        return $next($request);
    }

    protected function isModel($value): bool
    {
        return count($value) >= 1 && is_a($value[array_key_first($value)], Model::class);
    }
}