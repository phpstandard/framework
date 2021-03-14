<?php

/**
 * Dispatcher::match() and Dispatcher::compilePath() methods are heavily inspired 
 * by AltoRouter
 * 
 * @see https://altorouter.com
 */

declare(strict_types=1);

namespace Framework\Routing;

use Framework\Contracts\Routing\DispatcherInterface;
use Framework\Contracts\Routing\RouteInterface;
use Framework\Contracts\Support\CallbackResolverInterface;
use Framework\Support\CallbackResolver;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

class Dispatcher implements DispatcherInterface
{
    /** @var RouteCollector $collector */
    private $collector;

    /** @var CallbackResolverInterface $resolver */
    private $resolver;

    /** @var ContainerInterface $container */
    private $container;

    public function __construct(
        RouteCollector $collector,
        ContainerInterface $container,
        ?CallbackResolverInterface $resolver = null
    ) {
        $this->collector = $collector;
        $this->container = $container;
        $this->resolver = $resolver ?: new CallbackResolver($this->container);
    }

    /**
     * @inheritDoc
     */
    public function dispatch(ServerRequestInterface $request): ?RouteInterface
    {
        $uri = $request->getUri();
        $route = $this->match(
            $uri->getPath(),
            $request->getMethod()
        );

        if ($route) {
            $this->resolveMiddlewares($route)
                ->resolveHandler($route);
        }

        return $route;
    }

    /**
     * Match a given Request Url against stored routes
     * 
     * @param string $request_url
     * @param string $request_method
     * @return array|boolean Array with route information on success, false on failure (no match).
     */
    private function match(string $request_url, string $request_method): ?Route
    {
        $params = [];
        $routes = $this->collector->getRoutes();

        // Strip query string (?a=b) from Request Url
        if (($strpos = strpos($request_url, '?')) !== false) {
            $request_url = substr($request_url, 0, $strpos);
        }

        // Last character of the request url
        $last_char = $request_url ? $request_url[strlen($request_url) - 1] : '';

        foreach ($routes as $route) {
            $methods = explode("|", $route->getMethod());
            $path = $route->getPath();

            // Method did not match, continue to next route.
            if (!in_array($request_method, $methods)) {
                continue;
            }

            if ($path === '*') {
                // * wildcard (matches all)
                $match = true;
            } elseif (isset($path[0]) && $path[0] === '@') {
                // @ regex delimiter
                $pattern = '`' . substr($path, 1) . '`u';
                $match = preg_match($pattern, $request_url, $params) === 1;
            } elseif (($position = strpos($path, '[')) === false) {
                // No params in url, do string comparison
                $match = strcmp($request_url, $path) === 0;
            } else {
                // Compare longest non-param string with url before moving on to regex
                // Check if last character before param is a slash, because it could be optional if param is optional too
                if (strncmp($request_url, $path, $position) !== 0 && ($last_char === '/' || $path[$position - 1] !== '/')) {
                    continue;
                }

                $regex = $this->compilePath($path);
                $match = preg_match($regex, $request_url, $params) === 1;
            }

            if ($match) {
                if ($params) {
                    foreach ($params as $key => $value) {
                        if (!is_numeric($key)) {
                            $route->addParam($key, $value);
                        }
                    }
                }

                return $route;
            }
        }

        return null;
    }

    /**
     * Compile the regex for a given route path (EXPENSIVE)
     * 
     * @param $path
     * @return string
     */
    protected function compilePath($path)
    {
        if (preg_match_all('`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`', $path, $matches, PREG_SET_ORDER)) {
            $matchTypes = $this->matchTypes;
            foreach ($matches as $match) {
                list($block, $pre, $type, $param, $optional) = $match;

                if (isset($matchTypes[$type])) {
                    $type = $matchTypes[$type];
                }
                if ($pre === '.') {
                    $pre = '\.';
                }

                $optional = $optional !== '' ? '?' : null;

                //Older versions of PCRE require the 'P' in (?P<named>)
                $pattern = '(?:'
                    . ($pre !== '' ? $pre : null)
                    . '('
                    . ($param !== '' ? "?P<$param>" : null)
                    . $type
                    . ')'
                    . $optional
                    . ')'
                    . $optional;

                $path = str_replace($block, $pattern, $path);
            }
        }

        return "`^$path$`u";
    }

    /**
     * Resolve the middlewares
     *
     * @return self
     */
    private function resolveMiddlewares(Route $route): self
    {
        $resolved = [];

        foreach ($route->getMiddlewareStack() as $middleware) {
            $resolved[] = $this->resolveMiddleware($middleware);
        }

        $route->clearMiddlewareStack();
        $route->middleware($resolved);

        return $this;
    }

    /**
     * Resolve a middleware implementation, optionally from a container
     *
     * @param MiddlewareInterface|string $middleware
     *
     * @return MiddlewareInterface
     */
    private function resolveMiddleware($middleware): MiddlewareInterface
    {
        if (is_string($middleware)) {
            $middleware = $this->container->get($middleware);
        }

        if ($middleware instanceof MiddlewareInterface) {
            return $middleware;
        }

        throw new InvalidArgumentException(
            sprintf('Could not resolve middleware class: %s', $middleware)
        );
    }

    /**
     * Resolve handle
     *
     * @return self
     */
    private function resolveHandler(Route $route): self
    {
        $handler = $route->getHandler();
        $handler = $this->resolver->resolve($handler);
        $route->setHandler($handler);

        return $this;
    }
}
