<?php


namespace App\helpers;


class RouteHelper
{
    const ROUTE_CREATE = 'create';
    const ROUTE_UPDATE = 'update/{object}';
    const ROUTE_INDEX = 'get';
    const ROUTE_VIEW = 'view/{object}';
    const ROUTE_DELETE = 'delete/{object}';
}
