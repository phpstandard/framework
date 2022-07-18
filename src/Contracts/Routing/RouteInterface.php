<?php

namespace Framework\Contracts\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @package Framework\Contracts\Routing */
interface RouteInterface
{
    /**
     * Get middleware stack. 
     * 
     * Mixed array of instances of MiddlewareInterface or a middleware class 
     * names as a string to be resolved to MiddlewareInterface instance later.
     *
     * @return array
     */
    public function getMiddlewareStack(): array;

    /**
     * Process route's handle.
     * 
     * Call the route handle (controller method or a closure callback). 
     *
     * @param ServerRequestInterface $request A server request that passed 
     * through all middleware stack of the route.
     * 
     * @return ResponseInterface
     */
    public function process(
        ServerRequestInterface $request
    ): ResponseInterface;
}
