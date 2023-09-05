<?php

namespace Niladam\LaravelVisits\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasVisitsTrait
{
    public function visits(): MorphMany
    {
        return $this->morphMany(config('laravel-visits.overwrites.model'), 'visitable');
    }
}