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

/** @package Framework\Http */
class RequestHandler implements RequestHandlerInterface
{
    /** @var MiddlewareInterface[] $middlewareStack */
    private array $middlewareStack = [];
    private ?RouteInterface $route = null;

    /**
     * @param DispatcherInterface $dispatcher
     * @return void
     */
    public function __construct(
        private DispatcherInterface $dispatcher
    ) {
    }

    /**
     * @inheritDoc
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
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
