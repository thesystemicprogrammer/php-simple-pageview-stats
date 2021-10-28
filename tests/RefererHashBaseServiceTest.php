<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\RefererHash\RefererHashBaseService;

class RefererHashBaseServiceTest extends TestCase
{
    const SALT = "ABCDEFG";
    const SALT_IF_LOCKED = "ZGDBEHD";
   
    public function testCreateNewTimeBucketSaltString(): void {

        $service = new ConcreteTestableRefererHashBaseService();
        
        $salt = $service->testableCreateNewTimeBucketSalt(time());

        $this->assertEquals(self::SALT, $salt);
    }

    public function testCreateNewTimeBucketSaltCache(): void {

        Cache::flush();
        $service = new ConcreteTestableRefererHashBaseService();
        $timestamp = time();

        $service->testableCreateNewTimeBucketSalt($timestamp);

        $this->assertEquals(self::SALT, Cache::get($timestamp));
    }

    public function testReturnCachedSaltIfAlreadyLocked(): void {

        Cache::flush();
        $service = new ConcreteTestableRefererHashBaseService();
        $timestamp = time();
        Cache::add($timestamp, self::SALT_IF_LOCKED, 10);
        $lock = Cache::lock('salt_lock', 1);
        $lock->get();

        $salt = $service->testableCreateNewTimeBucketSalt($timestamp);

        $this->assertEquals(self::SALT_IF_LOCKED, $salt);
    }

}

class ConcreteTestableRefererHashBaseService extends RefererHashBaseService {
    protected function createSalt(): string {  return RefererHashBaseServiceTest::SALT; }
    public function createRefererHash(Request $request, int $timestamp): string { return ""; }
    public function testableCreateNewTimeBucketSalt($timestamp): string {
        return $this->createNewTimeBucketSalt($timestamp);
    }
}
