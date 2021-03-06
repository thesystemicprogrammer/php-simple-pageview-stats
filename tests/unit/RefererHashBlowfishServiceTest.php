<?php

namespace Unit;

use App\Events\NewSaltEvent;
use TestCase;
use Illuminate\Http\Request;
use App\Services\RefererHash\RefererHashBlowfishService;


class RefererHashBlowfishServiceTest extends TestCase
{
    private Request $request;

    public function testSameRequestAndSameTimestampLeadsToEqualHash(): void
    {
        $this->expectsEvents(NewSaltEvent::class);
        $service = new RefererHashBlowFishService();
        $timestamp = time();

        $this->request = $this->createStub('Illuminate\Http\Request');
        $this->request
            ->expects($this->exactly(2))
            ->method('ip')
            ->willReturnOnConsecutiveCalls(
                "192.168.1.214",
                "192.168.1.214"
            );
        $this->request
            ->expects($this->exactly(2))
            ->method('userAgent')
            ->willReturnOnConsecutiveCalls(
                "agent",
                "agent"
            );

        $firstHash = $service->createRefererHash($this->request, $timestamp);
        $secondHash = $service->createRefererHash($this->request, $timestamp);

        $this->assertEquals($firstHash, $secondHash);
    }

    public function testSameRequestAndDifferentTimestampLeadsToDifferentHash(): void
    {
        $this->expectsEvents(NewSaltEvent::class);
        $service = new RefererHashBlowFishService();
        $this->request = $this->createStub('Illuminate\Http\Request');
        $this->request
            ->expects($this->exactly(2))
            ->method('ip')
            ->willReturnOnConsecutiveCalls(
                "192.168.1.214",
                "192.168.1.214"
            );
        $this->request
            ->expects($this->exactly(2))
            ->method('userAgent')
            ->willReturnOnConsecutiveCalls(
                "agent",
                "agent"
            );

        $firstHash = $service->createRefererHash($this->request, time());
        $secondHash = $service->createRefererHash($this->request, time() + 1);

        $this->assertNotEquals($firstHash, $secondHash);
    }

    public function testDifferentRequestAndSameTimestampLeadsToDifferentHash(): void
    {
        $this->expectsEvents(NewSaltEvent::class);
        $service = new RefererHashBlowFishService();
        $timestamp = time();
        $this->request = $this->createStub('Illuminate\Http\Request');
        $this->request
            ->expects($this->exactly(2))
            ->method('ip')
            ->willReturnOnConsecutiveCalls(
                "192.168.1.214",
                "192.168.1.215"
            );
        $this->request
            ->expects($this->exactly(2))
            ->method('userAgent')
            ->willReturnOnConsecutiveCalls(
                "agent",
                "agent"
            );

        $firstHash = $service->createRefererHash($this->request, $timestamp);
        $secondHash = $service->createRefererHash($this->request, $timestamp);

        $this->assertNotEquals($firstHash, $secondHash);
    }
}
