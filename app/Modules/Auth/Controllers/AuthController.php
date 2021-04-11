<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Response\Response;
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
     * @return Response
     */
    public function login(): Response
    {
        $response = new Response();
        if ( !Auth::attempt(request()->only('email', 'password'))) {
            request()->session()->regenerate(true);
            return $response->withError( Response::ERROR_UNAUTHORIZED, trans('auth.failed'));
        }

        return $response->withData(Auth::user()->toArray());
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

        return $response->withStatus(Response::STATUS_OK);
    }

    public function me() : Response {
        $response = new Response();
        return $response->withData(Auth::user()->toArray());
    }
}
