<?php

namespace App\Service\RouterService;

/**
 * Class AbstractRouterService
 *
 * @package App\Service\RouterService
 */
abstract class AbstractRouterService
{
    /** @var string route prefix */
    const ROUTE_VERSION_PREFIX = 'v0';

    /** @var string Prefix for api requests */
    const API_PREFIX = 'api';
    /** @var string Prefix for backend requests */
    const BACKEND_PREFIX = 'web';

    /** @var string|null Namespace */
    protected ?string $namespace;

    /**
     *  Register api and web routes with route prefix
     * @return mixed
     */
    abstract public function registerRoutes();

    /**
     * @param string|null $namespace
     */
    public function setNamespace(?string $namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     *  Get path for router file
     * @return string
     */
    public function getBasePath(): string
    {
        return 'routes' . DIRECTORY_SEPARATOR . static::ROUTE_VERSION_PREFIX . DIRECTORY_SEPARATOR;
    }
}