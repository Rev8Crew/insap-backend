<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Response\Response;
use App\Models\Response\ResponseErrorStatus;
use App\Models\Response\ResponseStatus;
use App\Models\User;
use App\Modules\User\Services\UserService;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers\v1\Auth
 */
class UserController extends Controller
{
    public function get()
    {
    }

    public function view(User $object)
    {
    }

    public function create(UserCreateRequest $request)
    {
        $response = new Response();

        if (User::whereEmail($request->input('email'))->first()) {
            return $response->withError(ResponseErrorStatus::ERROR_BAD_REQUEST, __('user.emailDuplicate'));
        }

        /** @var UserService $userService */
        $userService = app(UserService::class);
        $userService->create( $request->all() );

        return $response->withStatus(ResponseStatus::STATUS_OK);
    }

    public function delete(User $object): Response
    {
        $response = new Response();
        $status = $object->delete();

        return $response->withStatus($status ? ResponseStatus::STATUS_OK : ResponseStatus::STATUS_ERROR);
    }

    public function update(UserUpdateRequest $request, User $object)
    {

    }
}
