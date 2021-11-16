<?php

namespace App\Providers;

use App\Services\JWT\JWTDecoder;
use Illuminate\Auth\GenericUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            return ($request->isMethod("post")) ?
                $this->handleNonAdminRoutesAuth($request) :
                $this->handleAdminRoutesAuth($request);
        });
    }

    private function handleAdminRoutesAuth(Request $request)
    {
        if  ($request->header('X-API-KEY') == env('API_KEY_ADMIN') ||
            (JWTDecoder::isJWTTokenValid($request->bearerToken()))) {
            return new GenericUser(['id' => 1, 'name' => 'Admin User']);
        }
    }

    private function handleNonAdminRoutesAuth(Request $request)
    {
        if ($request->header('X-API-KEY') == env('API_KEY_PAGEVIEW')) {
            return new GenericUser(['id' => 1, 'name' => 'Authorized User']);
        }
    }
}
