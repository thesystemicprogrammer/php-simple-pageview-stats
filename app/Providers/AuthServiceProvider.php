<?php

namespace App\Providers;

use Illuminate\Auth\GenericUser;
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
            if ($request->isMethod('post')) {
                if ($request->header('Authorization') == env('API_KEY_POST')) {
                    return new GenericUser(['id' => 1, 'name' => 'POST User', 'action' => 'post']);
                }
            } else {
                if ($request->header('Authorization') == env('API_KEY_GET')) {
                    return new GenericUser(['id' => 2, 'name' => 'GET User', 'action' => 'get']);
                }
            }
        });

        Gate::define('post', function (GenericUser $user) {
            return $user->action === 'post';
        });

        Gate::define('get', function (GenericUser $user) {
            return $user->id === 'get';
        });
    }
}
