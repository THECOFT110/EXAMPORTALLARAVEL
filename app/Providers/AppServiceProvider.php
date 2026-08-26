<?php

namespace App\Providers;

use Composer\CaBundle\CaBundle;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\ServiceProvider;
use Resend\Client as ResendClient;
use Resend\Contracts\Client as ClientContract;
use Resend\Transporters\HttpTransporter;
use Resend\ValueObjects\ApiKey;
use Resend\ValueObjects\Transporter\BaseUri;
use Resend\ValueObjects\Transporter\Headers;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ClientContract::class, static function (): ResendClient {
            $apiKey = config('resend.api_key', env('RESEND_API_KEY'));

            if (! is_string($apiKey) || empty($apiKey)) {
                $apiKey = 'dummy_key';
            }

            $caPath = class_exists(CaBundle::class) ? CaBundle::getSystemCaRootBundlePath() : true;

            $client = new GuzzleClient([
                'verify' => (is_string($caPath) && file_exists($caPath)) ? $caPath : true,
                'timeout' => 30,
            ]);

            $transporter = new HttpTransporter(
                $client,
                BaseUri::from('api.resend.com'),
                Headers::withAuthorization(ApiKey::from($apiKey))
            );

            return new ResendClient($transporter);
        });

        $this->app->alias(ClientContract::class, 'resend');
        $this->app->alias(ClientContract::class, ResendClient::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
