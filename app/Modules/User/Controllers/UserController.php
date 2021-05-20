<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Response\Response;
use App\Models\Response\ResponseStatus;
use App\Models\User;
use App\Modules\User\Requests\UserCreateRequest;
use App\Modules\User\Requests\UserUpdateRequest;
use App\Modules\User\Resources\UserResource;
use App\Modules\User\Services\UserService;

/**
 * Class UserController
 * @package App\Modules\User\Controllers
 */
class UserController extends Controller
{
    /**
     * @return Response
     */
    public function get(): Response
    {
        $response = new Response();
        $resource = UserResource::collection( User::all() );

        return $response->withData( $resource );
    }

    /**
     * @param User $user
     * @return Response
     */
    public function view(User $user): Response
    {
        $response = new Response();
        $resource = (new UserResource($user));

        return $response->withData( $resource );
    }

    /**
     * @param UserCreateRequest $request
     * @return Response
     */
    public function create(UserCreateRequest $request): Response
    {
        $response = new Response();

        /** @var UserService $userService */
        $userService = app(UserService::class);
        $userService->create( $request->all() );

        return $response->withStatus(ResponseStatus::STATUS_OK);
    }

    /**
     * @param User $user
     * @return Response
     * @throws \Exception
     */
    public function delete(User $user): Response
    {
        $response = new Response();

        /** @var UserService $userService */
        $userService = app(UserService::class);
        $userService->delete($user);

        return $response->withStatus(ResponseStatus::STATUS_OK);
    }

    /**
     * @param UserUpdateRequest $request
     * @param User $user
     * @return Response
     */
    public function update(UserUpdateRequest $request, User $user): Response
    {
        $response = new Response();

        /** @var UserService $userService */
        $userService = app(UserService::class);
        $userService->update( $user, $request->all() );

        return $response->withStatus(ResponseStatus::STATUS_OK);
    }

    /**
     * @param User $user
     *
     * @return Response
     */
    public function activate(User $user): Response
    {
        $response = new Response();

        /** @var UserService $userService */
        $userService = app(UserService::class);
        $userService->activate($user);

        return $response->withStatus(ResponseStatus::STATUS_OK);
    }

    /**
     * @param User $user
     *
     * @return Response
     */
    public function deactivate(User $user): Response
    {
        $response = new Response();

        /** @var UserService $userService */
        $userService = app(UserService::class);
        $userService->deactivate($user);

        return $response->withStatus(ResponseStatus::STATUS_OK);
    }
}
