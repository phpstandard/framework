<?php

namespace Framework\Routing;

use Framework\Contracts\Routing\RouteInterface;
use Framework\Routing\Traits\MiddlewareAwareTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Route implements RouteInterface
{
    use MiddlewareAwareTrait;

    /**
     * Route method
     *
     * @var string
     */
    private $method;

    /**
     * Route path
     *
     * @var string
     */
    private $path;

    /**
     * Route handler
     *
     * @var callable|string
     */
    private $handler;

    /**
     * Route name
     *
     * @var string|null
     */
    private $name;

    /**
     * Route parameters
     *
     * @var array
     */
    private $params = [];

    /**
     * Route constructor
     *
     * @param string $method
     * @param string $path
     * @param callable|string $handler
     * @return void
     */
    public function __construct(
        string $method,
        string $path,
        $handler,
        ?string $name = null
    ) {
        $this->setMethod($method)
            ->setPath($path)
            ->setHandler($handler)
            ->setName($name);
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
     * @param  string  $method  Route method
     *
     * @return  self
     */
    public function setMethod(string $method): self
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
     * @param  string  $path  Route path
     *
     * @return  self
     */
    public function setPath(string $path): self
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
     * @return  callable|string
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Set route handler
     *
     * @param  callable|string|null  $handler  Route handler
     *
     * @return  self
     */
    public function setHandler($handler): self
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * Get route name
     *
     * @return  string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set route name
     *
     * @param  string|null  $name  Route name
     *
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
     * @return self
     */
    public function addParam(string $key, $value): self
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
