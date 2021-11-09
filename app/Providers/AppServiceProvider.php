<?php

namespace App\Providers;

use App\Exceptions\InvalidRefererHashAlgorithmException;
use App\Services\RefererHash\RefererHashBlowfishService;
use App\Services\RefererHash\RefererHashCreator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(RefererHashCreator::class, function ($app) {
            if (env('REFERER_HASH_ALGORITHM') == 'BLOWFISH') {
                return new RefererHashBlowfishService();
            }
		  
            throw new InvalidRefererHashAlgorithmException('The provided refer hash algorithm in .env is invalid.');
        });
    }
}
