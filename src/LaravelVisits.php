<?php

namespace Niladam\LaravelVisits;

class LaravelVisits
{
    public function model(): string
    {
        return config('laravel-visits.overwrites.model');
    }

    public function recordVisitJob(): string
    {
        return config('laravel-visits.overwrites.job');
    }

    public function recordVisitUrlJob(): string
    {
        return config('laravel-visits.overwrites.job_url');
    }

    public function middleware(): string
    {
        return config('laravel-visits.overwrites.middleware');
    }
}
