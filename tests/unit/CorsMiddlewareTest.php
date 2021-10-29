<?php

namespace Unit;

use TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Middleware\CorsMiddleware;

class CorsMiddlewareTest extends TestCase {

    private Request $request;
    private $headers = [
        'Access-Control-Allow-Origin'      => '*',
        'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS, PUT, DELETE',
        'Access-Control-Allow-Credentials' => 'true',
        'Access-Control-Max-Age'           => '0',
        'Access-Control-Allow-Headers'     => 'Content-Type, Authorization'
    ];

    public function testOptionsRequest(): void {

        $middleware = new CorsMiddleware();
        $next = function() {};
        
        $this->request = $this->createStub('Illuminate\Http\Request');
        $this->request
            ->expects($this->any())
            ->method('isMethod')
            ->willReturn(True);
            
        $response = $middleware->handle($this->request, $next);

        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertHeaders($response);
    }    

    public function testNonOptionsRequest(): void {

        $middleware = new CorsMiddleware();
        $next = function() { 
            $response = new Response();
            $response->setContent('NonOptionsRequest');
            return $response; 
        };
        
        $request = $this->createStub('Illuminate\Http\Request');
        $request
            ->expects($this->any())
            ->method('isMethod')
            ->willReturn(False);
            
        $response = $middleware->handle($request, $next);

        $this->assertEquals('NonOptionsRequest', $response->content());
        $this->assertHeaders($response);
    }    

    private function assertHeaders($response): void {
        foreach($this->headers as $key => $value)
        {
            $this->assertEquals($response->headers->get($key), $value);
        }
    }
}
