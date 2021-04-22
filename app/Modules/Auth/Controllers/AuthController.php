<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\Response\Response;
use App\Models\Response\ResponseErrorStatus;
use App\Models\Response\ResponseStatus;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers\v1\Auth
 */
class AuthController extends Controller
{
    /**
     *  Auth user via email and password
     *
     * @param LoginRequest $request
     * @return Response
     */
    public function login(LoginRequest $request): Response
    {
        $response = new Response();

        if ( !Auth::attempt($request->only('email', 'password'))) {
            return $response->withError( ResponseErrorStatus::ERROR_UNAUTHORIZED, trans('auth.failed'));
        }

        /**
         * @var User $user
         */
        $user = $request->user();
        $token = $user->createToken('web');

        $resource = new UserResource($user);
        $resource->setToken($token->plainTextToken);

        return $response->withData( $resource->toArray($request) );
    }

    /**
     * @return Response
     */
    public function logout(): Response
    {
        $response = new Response();
        $user = request()->user();

        if ($user) {
            $user->tokens()->delete();
            Auth::logout();
        }

        return $response->withStatus(ResponseStatus::STATUS_OK);
    }

    /**
     * Return info about current user
     * @return Response
     */
    public function me() : Response {
        $response = new Response();
        return $response->withData( (new UserResource(request()->user()))->toArray(request()));
    }
}
