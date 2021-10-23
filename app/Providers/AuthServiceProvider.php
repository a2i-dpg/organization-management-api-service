<?php

namespace App\Providers;

use App\Helpers\Classes\AuthUtility;
use App\Helpers\Classes\HttpClientRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
     * @throws RequestException
     * @throws \Throwable
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('token', function (Request $request) {

            $token = $request->bearerToken();

            if (!$token) {
                return null;
            }

            Log::info('Bearer Token: ' . $token);
            $authUser = null;
            $idpServerId = AuthUtility::getIdpServerIdFromToken($token);
            Log::info("Auth idp user id-->" . $idpServerId);

            if ($idpServerId) {
                $clientRequest = new HttpClientRequest();
                $user = $clientRequest->getAuthPermission($idpServerId);
                if ($user) {
                    $role = new Role($user['role']);
                    $authUser = new User($user);
                    $authUser->role = $role;
                    $authUser->permissions = collect($user['permissions']);
                }
                Log::info("userInfoWithIdpId:" . json_encode($authUser));

            }
            return $authUser;
        });

    }


}
