<?php

namespace App\Http\Controllers;

class CommonController extends Controller
{
    /**
     * @OA\Get (path="/",
     *   tags={"web"},
     *   summary="FrontEnd",
     *   description="Обрабатывает все фронт-енд запросы",
     *   @OA\Response(response=200, description="OK"),
     * )
     */
    public function app()
    {
        return view('app');
    }
}
