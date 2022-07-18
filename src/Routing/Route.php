<?php

namespace Framework\Routing;

use Framework\Contracts\Routing\RouteInterface;
use Framework\Routing\Traits\MiddlewareAwareTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/** @package Framework\Routing */
class Route implements RouteInterface
{
    use MiddlewareAwareTrait;

    /**
     * Route parameters
     *
     * @var array
     */
    private array $params = [];

    /**
     * @param string $method 
     * @param string $path 
     * @param callable|string $handler 
     * @param null|string $name 
     * @return void 
     */
    public function __construct(
        private string $method,
        private string $path,
        private callable|string $handler,
        private ?string $name = null
    ) {
        $this->setPath($path);
    }

    /**
     * Get route method
     *
     * @return  string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Set route method
     * 
     * @param string $method 
     * @return Route 
     */
    public function setMethod(string $method): Route
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Get route path
     *
     * @return  string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set route path
     *
     * @param string $path Route path
     * @return Route
     */
    public function setPath(string $path): Route
    {
        if ($path != '*' && substr($path, -4) !== '[/]?') {
            $path = rtrim($path, '/') . '[/]?';
        }

        $this->path = $path;
        return $this;
    }

    /**
     * Get route handler
     *
     * @return callable|string
     */
    public function getHandler(): callable|string
    {
        return $this->handler;
    }

    /**
     * Set route handler
     *
     * @param callable|string|null $handler Route handler
     * @return Route
     */
    public function setHandler(callable|string|null $handler): Route
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * Get route name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set route name
     *
     * @param  string|null  $name  Route name
     * @return  self
     */
    public function setName(?string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get route parameters
     *
     * @return  array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Add route params
     *
     * @param string $key
     * @param mixed $value
     * @return Route
     */
    public function addParam(string $key, $value): Route
    {
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function process(
        ServerRequestInterface $request
    ): ResponseInterface {
        $handler = $this->getHandler();
        $response = $handler($request, ...array_values($this->getParams()));

        return $response;
    }
}
