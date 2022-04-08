<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Common\Response;
use App\Models\User;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\User\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers\v1\Auth
 */
class AuthController extends Controller
{
    /**
     * @OA\Post (
     *     path="web/auth/login",
     *     tags={"auth", "web"},
     *     summary="Login",
     *     description="Аутентификация пользователя",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Успешная авторизация",
     *          @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Ошибка в авторизации"
     *     )
     * )
     * @param LoginRequest $request
     * @return Response
     */
    public function login(LoginRequest $request): Response
    {
        $response = new Response();

        if (!auth()->attempt($request->only('email', 'password'))) {
            return $response->withError(SymfonyResponse::HTTP_BAD_REQUEST, trans('auth.failed'));
        }

        /**
         * @var User $user
         */
        $user = $request->user();
        $token = $user->createToken('web');

        $resource = new UserResource($user);
        $resource->setToken($token->plainTextToken);

        return $response->withData( $resource );
    }

    /**
     * @OA\Post (
     *     path="web/auth/logout",
     *     tags={"auth", "web"},
     *     summary="Logout",
     *     description="Логаут пользователя",
     *     @OA\Response(
     *          response=200,
     *          description="ОК"
     *     )
     * )
     *
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

        return $response->success();
    }

    /**
     * @OA\Post (
     *     path="web/auth/me",
     *     tags={"auth", "web"},
     *     summary="User Info",
     *     description="Получение информации о пользователе",
     *     @OA\Response(
     *          response=200,
     *          description="ОК",
     *          @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     )
     * )

     * @return Response
     */
    public function me() : Response {
        $response = new Response();
        $resource = new UserResource( request()->user());

        return $response->withData( $resource );
    }
}
