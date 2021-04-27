<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Response\Response;
use App\Models\Response\ResponseStatus;
use App\Models\User;
use App\Modules\User\Requests\UserCreateRequest;
use App\Modules\User\Requests\UserUpdateRequest;
use App\Modules\User\Services\UserService;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers\v1\Auth
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

        return $response->withData( $resource->toArray(request()));
    }

    /**
     * @param User $object
     * @return Response
     */
    public function view(User $object): Response
    {
        $response = new Response();
        $resource = (new UserResource($object));

        return $response->withData( $resource->toArray(request()));
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
     * @param User $object
     * @return Response
     * @throws \Exception
     */
    public function delete(User $object): Response
    {
        $response = new Response();
        $status = $object->delete();

        return $response->withStatus($status ? ResponseStatus::STATUS_OK : ResponseStatus::STATUS_ERROR);
    }

    /**
     * @param UserUpdateRequest $request
     * @param User $object
     * @return Response
     */
    public function update(UserUpdateRequest $request, User $object): Response
    {
        $response = new Response();

        /** @var UserService $userService */
        $userService = app(UserService::class);
        $userService->update( $object, $request->all() );

        return $response->withStatus(ResponseStatus::STATUS_OK);
    }
}
