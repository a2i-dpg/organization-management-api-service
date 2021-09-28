<?php

namespace App\Providers;

use App\Helpers\Classes\HttpClientRequest;
use App\Models\User;
use App\Services\UserRolePermissionManagementServices\UserService;
use Illuminate\Support\Facades\Gate;
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
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('token', function ($request) {
            $token = $request->header('Token');
            Log::info($token);
            $authUser = null;
            if ($token) {
                $header = explode(" ", $token);
                if (count($header) > 1) {
                    $tokenParts = explode(".", $header[1]);
                    if (count($tokenParts) == 3) {
                        $tokenPayload = base64_decode($tokenParts[1]);
                        $jwtPayload = json_decode($tokenPayload);
                        $clientRequest = new HttpClientRequest();
                        $authUser = $clientRequest->getAuthPermission($jwtPayload->sub ?? null);
                    }
                }
                Log::info("userInfoWithIdpId:" . json_encode($authUser));
            }
            return $authUser;
        });
    }
}
