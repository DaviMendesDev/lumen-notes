<?php

namespace App\Providers;

use App\Models\Note;
use App\Models\User;
use App\Models\Workspace;
use App\Policies\NotePolicy;
use App\Policies\WorkspacePolicy;
use App\Services\Common\AuthService;
use Firebase\JWT\JWT;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Http\Request;

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

        $this->app['auth']->viaRequest('api', function (Request $request) {
            /** @var AuthService $auth */
            $auth = app(AuthService::class);

            if (! $accessToken = $request->bearerToken()) {
                throw new AuthenticationException('Access Token not provided!');
            }

            return $auth->authenticate($accessToken);
        });

        Gate::policy(Note::class, NotePolicy::class);
        Gate::policy(Workspace::class, WorkspacePolicy::class);
    }
}
