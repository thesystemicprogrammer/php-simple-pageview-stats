<?php

namespace Unit;

use TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Middleware\AbortLocalhost;

class AbortLocalhostTest extends TestCase {

    private Request $request;

    public function testAbortLocalhostIPV4(): void {

        $middleware = new AbortLocalhost();
        $next = function() {};
        
        $this->request = $this->createStub('Illuminate\Http\Request');
        $this->request
            ->expects($this->any())
            ->method('ip')
            ->willReturn('127.0.0.1');
            
        $response = $middleware->handle($this->request, $next);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->status());
    }

    public function testAbortLocalhostIPV6(): void {

        $middleware = new AbortLocalhost();
        $next = function() {};
        
        $this->request = $this->createStub('Illuminate\Http\Request');
        $this->request
            ->expects($this->any())
            ->method('ip')
            ->willReturn('::1');
            
        $response = $middleware->handle($this->request, $next);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->status());
    }

    public function testLetNonLocalhostPass(): void {

        $middleware = new AbortLocalhost();
        $next = function() { return 'passed'; };
        
        $this->request = $this->createStub('Illuminate\Http\Request');
        $this->request
            ->expects($this->any())
            ->method('ip')
            ->willReturn('10.10.10.10');
            
        $response = $middleware->handle($this->request, $next);

        $this->assertEquals('passed', $response);
    }
}
