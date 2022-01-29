<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="InSap OpenApi",
 *      description="InSap Swagger OpenApi description",
 *      @OA\Contact(
 *          email="dimaelik71@gmail.com"
 *      )
 * )
 *
 * @see https://github.com/zircote/swagger-php/blob/master/Examples/petstore.swagger.io/controllers/UserController.php
 * @see https://github.com/DarkaOnLine/L5-Swagger/blob/master/tests/storage/annotations/OpenApi/Anotations.php
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
