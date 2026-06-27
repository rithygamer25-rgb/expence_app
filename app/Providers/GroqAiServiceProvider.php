<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

class GroqAiServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Macro for Groq API (OpenAI-compatible)
        Http::macro('groq', function () {
            return Http::baseUrl('https://api.groq.com/openai/v1')
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . config('services.groq.key'),
                ]);
        });
    }
}
