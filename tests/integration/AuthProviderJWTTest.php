<?php

namespace Integration;

use TestCase;
use Dotenv\Dotenv;
use App\Models\Pageview;
use Firebase\JWT\JWT;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;

class AuthProviderJWTTest extends TestCase
{
    use DatabaseMigrations;

    public function setup(): void
    {
        Dotenv::createUnsafeMutable(dirname(__DIR__), 'env/.env-authorization.test')->load();
        parent::setup();
        $this->disableAbortLocalhostMiddleware();
    }

    public function testGetPagviewWithJWT(): void
    {
        $payload = array(
            'iss' => 'http://example.org',
            'aud' => 'http://example.com',
            'iat' => time(),
       //     'exp' => time() + 60 * 60,
            'roles' => ['pageview']
        );
        $jwt = JWT::encode($payload, env('JWT_HS256_SECRET'), 'HS256');
        $this->json('GET', '/api/pageview', ['uri' => '/newpage'], ['Authorization' => 'Bearer ' . $jwt]);

        $this->assertResponseStatus(Response::HTTP_OK);
    }
   
    private function disableAbortLocalhostMiddleware(): void {
        $this->app->instance(\App\Http\Middleware\AbortLocalhost::class, new class {
            public function handle($request, $next) {
                return $next($request);
            }
        });
    }
}
