<?php

namespace App\Services\RefererHash;

use App\Events\NewSaltEvent;
use App\Services\TimeBucket\TimeBucketCalculatorService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

abstract class RefererHashBaseService implements RefererHashCreator {

    protected function createNewTimeBucketSalt($timestamp): string {
        $lock = Cache::lock('salt_lock', 5);

        if ($lock->get()) {
            $salt = $this->createSalt();
            Cache::add($timestamp, $salt, $this->calculateCacheTTL());
            $lock->release();
            Event::dispatch(new NewSaltEvent);
        } else {
            $salt = $lock->block(5, function () use ($timestamp) {
                Log::warning('Concurrent salt generation');
                return Cache::get($timestamp);
            });
        }
        Log::notice('New salt generated');
        return $salt;
    }

    protected abstract function createSalt(): string;

    private function calculateCacheTTL(): int {
        return (TimeBucketCalculatorService::getTimeBucketPeriodInHours() + 1) * 60 * 60;
    }
}
