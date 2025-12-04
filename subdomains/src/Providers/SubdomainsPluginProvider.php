<?php

namespace Boy132\Subdomains\Providers;

use App\Models\Role;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class SubdomainsPluginProvider extends ServiceProvider
{
    public function register(): void
    {
        Role::registerCustomDefaultPermissions('cloudflare_domain');
    }

    public function boot(): void
    {
        Http::macro(
            'cloudflare',
            fn () => Http::acceptJson()
                ->withHeaders([
                    'X-Auth-Email' => config('subdomains.email'),
                    'X-Auth-Key' => config('subdomains.key'),
                ])
                ->timeout(5)
                ->connectTimeout(1)
                ->baseUrl('https://api.cloudflare.com/client/v4/')
                ->throw()
        );
    }
}
