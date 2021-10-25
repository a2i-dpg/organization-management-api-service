<?php

namespace App\Providers;

use App\Helpers\Classes\AuthUtility;
use App\Helpers\Classes\HttpClientRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    private array $policies = [
    ];

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

        if (count($this->policies)) {
            /** Registering Policies
             * @var string $modelName
             * @var string $policyName
             */
            foreach ($this->policies as $modelName => $policyName) {
                Gate::policy($modelName, $policyName);
            }
        }

        $this->app['auth']->viaRequest('token', function (Request $request) {

            $token = $request->bearerToken();

            if (!$token) {
                return null;
            }

            Log::info('Bearer Token: ' . $token);
            $authUser = null;
            $idpServerUserId = AuthUtility::getIdpServerIdFromToken($token);
            Log::info("Auth idp user id-->" . $idpServerUserId);

            if ($idpServerUserId) {
                $clientRequest = new HttpClientRequest();
                $userWithRolePermission = $clientRequest->getAuthPermission($idpServerUserId);
                if ($userWithRolePermission) {
                    $role = app(Role::class);
                    if (isset($userWithRolePermission['role'])) {
                        $role = app(Role::class, $userWithRolePermission['role']);
                    }
                    $authUser = app(User::class, $userWithRolePermission);
                    $authUser->role = $role;

                    $permissions = collect([]);
                    if (isset($userWithRolePermission['permissions'])) {
                        $permissions = collect($userWithRolePermission['permissions']);
                    }
                    $authUser->permissions = $permissions;
                }
                Log::info("userInfoWithIdpId:" . json_encode($authUser));
            }
            return $authUser;
        });

    }


}
