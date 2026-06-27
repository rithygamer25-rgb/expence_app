<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

class GoogleAiServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Optional: Macro for future use (currently not used in aiScan)
        Http::macro('googleai', function () {
            return Http::baseUrl('https://generativelanguage.googleapis.com/v1beta')
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]);
        });
    }
}
