<?php
return [
    'name'     => env('APP_NAME', 'InvoiceIQ'),
    'env'      => env('APP_ENV', 'production'),
    'debug'    => (bool) env('APP_DEBUG', false),
    'url'      => env('APP_URL', 'http://localhost'),
    'locale'   => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale'    => env('APP_FAKER_LOCALE', 'en_US'),
    'timezone' => 'Asia/Kolkata',
    'key'      => env('APP_KEY'),
    'cipher'   => 'AES-256-CBC',
    'maintenance' => ['driver' => env('APP_MAINTENANCE_DRIVER', 'file')],
    'providers'   => Illuminate\Support\ServiceProvider::defaultProviders()->merge([
        App\Providers\AppServiceProvider::class,
    ])->toArray(),
    'aliases' => Illuminate\Foundation\AliasLoader::getInstance()->getAliases(),
];
