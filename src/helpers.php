<?php

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Facades\Agent;
use Niladam\LaravelVisits\Concerns\CanHaveVisits;
use Niladam\LaravelVisits\Jobs\RecordVisitJob;
use Niladam\LaravelVisits\Jobs\RecordVisitUrlJob;
use Niladam\LaravelVisits\Models\Visit;

if (! function_exists('visitUrl')) {
    function visitUrl(
        string $url,
        array $record,
        int $count = 1,
        bool $async = true,
    ) {
        $record['url'] = str($url)
            ->when(config('laravel-visits.remove_current_app_domain'), fn ($str) => $str->replace(search: config('app.url'), replace: ''))
            ->when(! empty(config('laravel-visits.replace_in_urls')), fn ($str) => $str->replace(search: config('laravel-visits.replace_in_urls'), replace: ''))
            ->replaceLast(search: '?', replace: '')
            ->toString();

        $job = config('laravel-visits.overwrites.job_url', RecordVisitUrlJob::class);
        $model = config('laravel-visits.overwrites.model', Visit::class);

        if ($async) {
            return dispatch(new $job(visitable: $url, record: $record, count: $count))->afterResponse();
        }

        return match (true) {
            $count > 1 => $model::insert(
                array_fill(
                    start_index: 0,
                    count: $count,
                    value: $record
                )
            ),
            default => $model::create($record),
        };
    }
}

if (! function_exists('visitModel')) {
    function visitModel(
        $visitable,
        array $record,
        int $count = 1,
        bool $async = true,
    ) {
        $job = config('laravel-visits.overwrites.job', RecordVisitJob::class);

        if ($async) {
            return dispatch(new $job(visitable: $visitable, record: $record, count: $count))->afterResponse();
        }

        return match (true) {
            $count > 1 => $visitable->visits()->createMany(array_fill(0, $count, $record)),
            default => $visitable->visits()->create($record),
        };
    }
}

if (! function_exists('visit')) {
    function visit(
        CanHaveVisits|string $visitable,
        int $count = 1,
        string $referer = null,
        string $platform = null,
        string $ipAddress = null,
        bool $async = true,
    ) {
        $referer ??= request()->headers->get('referer');

        $referer = str($referer)
            ->when(config('laravel-visits.remove_current_app_domain'), fn ($str) => $str->replace(search: config('app.url'), replace: ''))
            ->when(! empty(config('laravel-visits.replace_in_urls')), fn ($str) => $str->replace(search: config('laravel-visits.replace_in_urls'), replace: ''))
            ->toString();

        $platform ??= Agent::deviceType();

        $record = [
            'ip_address' => $ipAddress ?: request()?->ip() ?? '127.0.0.1',
            'user_id' => auth()->id(),
            'platform' => $platform,
            'referrer' => $referer,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (is_a($visitable, Model::class)) {
            return visitModel(visitable: $visitable, record: $record, count: $count, async: $async);
        }

        return visitUrl(url: $visitable, record: $record, count: $count, async: $async);
    }
}

if (! function_exists('recordVisit')) {
    function recordVisit(CanHaveVisits|string $visitable)
    {
        return visit($visitable);
    }
}
