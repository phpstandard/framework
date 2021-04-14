<?php

/**
 * Dispatcher::match() and Dispatcher::compilePath() methods are heavily 
 * inspired by AltoRouter
 * 
 * @see https://altorouter.com
 */

declare(strict_types=1);

namespace Framework\Routing;

use Framework\Contracts\Routing\DispatcherInterface;
use Framework\Contracts\Routing\RouteInterface;
use Framework\Contracts\Support\CallbackResolverInterface;
use Framework\Contracts\Support\MiddlewareResolverInterface;
use Psr\Http\Message\ServerRequestInterface;

class Dispatcher implements DispatcherInterface
{
    /** @var RouteCollector $collector */
    private $collector;

    /** @var CallbackResolverInterface $resolver */
    private $callbackResolver;

    /** @var MiddlewareResolverInterface $middlewareResolver */
    private $middlewareResolver;

    /** @var string[] Array of the match types  */
    protected $matchTypes = [
        'i'  => '[0-9]++', // Integer
        'a'  => '[0-9A-Za-z]++', // Alphanumeric
        'h'  => '[0-9A-Fa-f]++', // Hexadecimal
        's'  => '[0-9A-Za-z\-]++', // url slug
        '*'  => '.+?', // 
        '**' => '.++',
        ''   => '[^/\.]++'
    ];

    public function __construct(
        RouteCollector $collector,
        MiddlewareResolverInterface $middleware_resolver,
        CallbackResolverInterface $callback_resolver
    ) {
        $this->collector = $collector;
        $this->middlewareResolver = $middleware_resolver;
        $this->callbackResolver = $callback_resolver;
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
            foreach ($matches as $match) {
                list($block, $pre, $type, $param, $optional) = $match;

                if (isset($this->matchTypes[$type])) {
                    $type = $this->matchTypes[$type];
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
            $resolved[] = $this->middlewareResolver->resolve($middleware);
        }

        $route->clearMiddlewareStack();
        $route->middleware($resolved);

        return $this;
    }

    /**
     * Resolve handle
     *
     * @return self
     */
    private function resolveHandler(Route $route): self
    {
        $handler = $route->getHandler();
        $handler = $this->callbackResolver->resolve($handler);
        $route->setHandler($handler);

        return $this;
    }
}
