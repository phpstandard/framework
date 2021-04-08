<?php

declare(strict_types=1);

namespace Framework\Http;

use Framework\Contracts\Routing\DispatcherInterface;
use Framework\Contracts\Routing\RouteInterface;
use Framework\Http\Exceptions\NotFoundException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandler implements RequestHandlerInterface
{
    /** @var DispatcherInterface|null $route */
    private $dispatcher;

    /** @var MiddlewareInterface[] $middlewareStack */
    private $middlewareStack = [];

    /** @var RouteInterface $route */
    private $route;

    public function __construct(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->route) {
            $route = $this->dispatcher->dispatch($request);

            if (!$route) {
                throw new NotFoundException($request);
            }

            $this->route = $route;
            $this->middlewareStack = $this->route->getMiddlewareStack();
        }

        /** @var MiddlewareInterface $middleware */
        $middleware = array_shift($this->middlewareStack);
        if ($middleware) {
            $response = $middleware->process($request, $this);
        } else {
            $response = $this->route->process($request);

            // Reset
            $this->route = null;
            $this->middlewareStack = [];
        }

        return $response;
    }
}
