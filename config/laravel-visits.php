<?php

return [
    /**
     * When saving the URLs to the database, it might make sense to remove
     * the current application URL from the logged URL.
     */
    'remove_current_app_domain' => env('LARAVEL_VISITS_REMOVE_CURRENT_APP_DOMAIN', true),

    /**
     * When saving the URLs to the database, it might make sense to remove some strings
     * from it. You can define them here. These will be removed from the URL
     * that will be saved.
     */
    'replace_in_urls' => [],

    /**
     * Overwrite these values to use your own models, jobs and middleware.
     */
    'overwrites' => [
        'user_model' => '\App\Models\User',
        'model' => \Niladam\LaravelVisits\Models\Visit::class,
        'job' => \Niladam\LaravelVisits\Jobs\RecordVisitJob::class,
        'job_url' => \Niladam\LaravelVisits\Jobs\RecordVisitUrlJob::class,
        'middleware' => \Niladam\LaravelVisits\Middleware\RecordVisitsMiddleware::class,
    ],
];