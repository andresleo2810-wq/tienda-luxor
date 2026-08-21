<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 🔒 Fuerza HTTPS solo cuando entras por el túnel (ngrok / cloudflare)
        $host = request()->getHost();
        if (str_contains($host, 'ngrok') || str_contains($host, 'trycloudflare.com')) {
            URL::forceScheme('https');
        }
    }
}