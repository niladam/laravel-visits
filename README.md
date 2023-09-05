# Laravel Visits - simple drop-in package that records visits.

This is a small package that allows you to easily record visits to your models and / or custom URLs, either by using the included middleware or by using the provided helpers.

The package stores the visits in a separate table, so it doesn't bloat your existing tables.

This package makes use of PHP's [inet_ntop](https://www.php.net/manual/en/function.inet-ntop.php) and [inet_pton](https://www.php.net/manual/en/function.inet-pton.php) functions to store the IP addresses in a binary format, reducing your database size.

The package tries to keep the visits table simple, with only the following data being stored:

- The IP address of the visitor
- An optional user ID, if the visitor is authenticated
- The platform (`desktop`, `phone`, `tablet`, `robot` or `other`) (makes use of [jenssegers/agent](https://github.com/jenssegers/agent))
- The referer (if any)
- The entity that's being visited
- The URL that's being visited (if not an entity) - by removing the current app URL from the string.

## Installation

You can install the package via composer:

```shell
composer require niladam/laravel-visits
```

Publish the migration with:

```bash
php artisan vendor:publish --provider="Niladam\LaravelVisits\LaravelVisitsServiceProvider" --tag="migrations"
```

Publish the config file with:

```bash
php artisan vendor:publish --provider="Niladam\LaravelVisits\LaravelVisitsServiceProvider" --tag="config"
```

Run the migrations:

```bash
php artisan migrate --step
````

## Prepare your model, implementing the `CanHaveVisits` interface and the `HasVisits` trait.

Here's an example:

```php
<?php

namespace App\Models;

use Niladam\LaravelVisits\Concerns\CanHaveVisits; // <--- add this to your model
use Niladam\LaravelVisits\Concerns\HasVisitsTrait; // <--- add this to your model

class Product extends Model implements CanHaveVisits // <--- add this to your model
{
    use HasVisitsTrait; // <-- add this to your model
}
```

## Add the included middleware to your route:

```php
use Niladam\LaravelVisits\Middleware\RecordVisitsMiddleware;

Route::get('/products/{product}', function (Product $product) {
    return view('products.show', [
        'product' => $product,
    ]);
})->middleware(RecordVisitsMiddleware::class);
```

That's it. Your visits will be recorded automatically.

## Manually recording visits:

The package provides the following helper to help you record your visits.

The recording will happen async, by default and therefore not slow down your application.

In order to manually record a visit use the `recordVisit` helper:

```php
$product = Product::latest()->first();

recordVisit($product);

$url = 'https://my-cool-domain.app/some-url';

recordVisit($url);
```

You can also record visits using the `lvVisit` helper which looks like this:

```php
$product = Product::latest()->first();

lvVisit(
    visitable: $product, 
    count: 1, // optional, defaults to 1
    referer: 'some-referer', // optional, defaults to null
    platform: 'desktop', // optional, defaults to null, and will be determined from the request
    ipAddress: '127.0.0.1', // optional, defaults to null, and will be set as the request IP or '127.0.0.1'
    async: true // optional, defaults to true. If set to false, the visit will be recorded synchronously
); // <-- This will dispatch a job to create the visit

$url = 'https://my-cool-domain.app/some-url';

lvVisit(
    visitable: $url, 
    count: 4, 
    async: true
); // <-- This will dispatch a job to create 4 visits.
```

## Retrieving the visits:

```php
$product = Product::latest()->first();
$product->visits; // <-- returns a collection of visits
```

## Retrieving visits for a specific platform/type:
The package comes with scopes for all the platform, so you can limit the results to a specific platform.

```php
$product = Product::latest()->first();
$product->visits()->desktop()->get(); // <-- returns a collection of visits from desktop users
$product->visits()->phone()->get(); // <-- returns a collection of visits from phone users
$product->visits()->tablet()->get(); // <-- returns a collection of visits from tablet users
$product->visits()->robot()->get(); // <-- returns a collection of visits from robots
$product->visits()->other()->get(); // <-- returns a collection of visits from other platforms
$product->visits()->mobile()->get(); // <-- returns a collection of visits mobile (phone + tablet) users
$product->visits()->entities()->get(); // <-- returns a collection of visits to entities
$product->visits()->urls()->get(); // <-- returns a collection of visits to URLs
```

## Retrieving the IP address (since it's being stored as binary):

```php
$product = Product::latest()->first();
$product->visits->first()->ip; // <-- returns the IP address as a string
```

## Customise

The package comes with a config file that allows you to customise the jobs, middleware and the default user model.

The config file is published to `config/laravel-visits.php` and looks like this:

```php
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
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email niladam@gmail.com instead of using the issue tracker.

## Credits

-   [Madalin Tache](https://github.com/niladam)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
