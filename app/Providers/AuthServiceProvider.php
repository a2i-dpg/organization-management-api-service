<?php

namespace App\Providers;

use App\Helpers\Classes\HttpClientRequest;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use App\Policies\OrganizationPolicy;
use App\Services\UserRolePermissionManagementServices\UserService;
use Illuminate\Http\Client\RequestException;
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
     * @throws RequestException
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('token', function ($request) {
            $token = $request->header('Authorization');
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
                        $user = $clientRequest->getAuthPermission($jwtPayload->sub ?? null);
                        if ($user) {
                            $role = new Role($user['role']);
                            $authUser = new User($user);
                            $authUser->role = $role;
                            $authUser->permissions = collect($user['permissions']);
                        }
                    }
                }

                Log::info("userInfoWithIdpId:" . json_encode($authUser));
            }
            return $authUser;
        });
    }
}
