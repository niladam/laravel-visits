<?php

namespace Niladam\LaravelVisits\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface CanHaveVisits
{
    public function visits(): MorphMany;
}