<?php

namespace Modules\CognitoGuard\Providers;

use Exception;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Modules\CognitoGuard\Entities\CognitoUser;
use Modules\CognitoGuard\src\CognitoJWT;

class CognitoGuardProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        Auth::viaRequest('cognito', function ($request) {
            $jwt = $request->bearerToken();
            $region = config('cognitoguard.region');
            $userPoolId = config('cognitoguard.user_pool_id');

            if ($jwt) {
                try {
                    $cognitoUser = CognitoJWT::verifyToken($jwt, $region, $userPoolId);
                    return CognitoUser::firstOrCreate(["username" => $cognitoUser->username]);
                } catch (Exception $e) {
                    return null;
                }
            }
            return null;
        });
    }
}
