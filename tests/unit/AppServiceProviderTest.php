<?php

namespace Unit;

use TestCase;
use Dotenv\Dotenv;
use App\Exceptions\InvalidRefererHashAlgorithmException;
use App\Providers\AppServiceProvider;
use App\Services\RefererHash\RefererHashBlowfishService;
use App\Services\RefererHash\RefererHashCreator;


class AppServiceProviderTest extends TestCase {
    protected $originalExceptionHandler;

    public function testReferHashBlowfish(): void {
        config(['REFERER_HASH_ALGORITHM' => 'BLOWFISH']);

        $provider = new AppServiceProvider($this->app);
        $provider->register();

        $this->assertTrue($this->app->make(RefererHashCreator::class) instanceof RefererHashBlowfishService);
    }

    public function testInvalidRefererHashAlgorithmInEnv(): void {
        Dotenv::createUnsafeMutable(dirname(__DIR__), 'env/.env-referer-hash.test')->load();
        $this->expectException(InvalidRefererHashAlgorithmException::class);

        $provider = new AppServiceProvider($this->app);
        $provider->register();
        $this->app->make(RefererHashCreator::class);
    }
}
