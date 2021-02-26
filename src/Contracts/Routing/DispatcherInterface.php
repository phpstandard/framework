<?php

namespace Framework\Contracts\Routing;

use Psr\Http\Message\ServerRequestInterface;

interface DispatcherInterface
{
    /**
     * Dispatch a server request and resolve the matched route
     *
     * @param ServerRequestInterface $request
     * @return RouteInterface|null
     */
    public function dispatch(ServerRequestInterface $request): ?RouteInterface;
}
