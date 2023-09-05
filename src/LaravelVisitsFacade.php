<?php

namespace Niladam\LaravelVisits;

use Illuminate\Support\Facades\Facade;

class LaravelVisitsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return LaravelVisits::class;
    }
}
