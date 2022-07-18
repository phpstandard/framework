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

/** @package Framework\Routing */
class Dispatcher implements DispatcherInterface
{
    /** @var string[] Array of the match types  */
    protected array $matchTypes = [
        'i'  => '[0-9]++', // Integer
        'a'  => '[0-9A-Za-z]++', // Alphanumeric
        'h'  => '[0-9A-Fa-f]++', // Hexadecimal
        's'  => '[0-9A-Za-z\-]++', // url slug
        '*'  => '.+?', // 
        '**' => '.++',
        ''   => '[^/\.]++'
    ];

    /**
     * @param RouteCollector $collector 
     * @param MiddlewareResolverInterface $middlewareResolver 
     * @param CallbackResolverInterface $callbackResolver 
     * @return void 
     */
    public function __construct(
        private RouteCollector $collector,
        private MiddlewareResolverInterface $middlewareResolver,
        private CallbackResolverInterface $callbackResolver
    ) {
    }

    /**
     * @inheritDoc
     */
    public function dispatch(ServerRequestInterface $request): ?RouteInterface
    {
        $uri = $request->getUri();
        $route = $this->matchRoute(
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
     * @param string $url 
     * @param string $method 
     * @return null|Route 
     */
    private function matchRoute(
        string $url,
        string $method
    ): ?Route {
        $params = [];
        $routes = $this->collector->getRoutes();

        // Strip query string (?a=b) from Request Url
        if (($strpos = strpos($url, '?')) !== false) {
            $url = substr($url, 0, $strpos);
        }

        // Last character of the request url
        $lastChar = $url ? $url[strlen($url) - 1] : '';

        foreach ($routes as $route) {
            $methods = explode("|", $route->getMethod());
            $path = $route->getPath();

            // Method did not match, continue to next route.
            if (!in_array($method, $methods)) {
                continue;
            }

            if ($path === '*') {
                // * wildcard (matches all)
                $match = true;
            } elseif (isset($path[0]) && $path[0] === '@') {
                // @ regex delimiter
                $pattern = '`' . substr($path, 1) . '`u';
                $match = preg_match($pattern, $url, $params) === 1;
            } elseif (($position = strpos($path, '[')) === false) {
                // No params in url, do string comparison
                $match = strcmp($url, $path) === 0;
            } else {
                // Compare longest non-param string with url before moving on to
                // regex. Check if last character before param is a slash, 
                // because it could be optional if param is optional too
                if (
                    strncmp($url, $path, $position) !== 0
                    && ($lastChar === '/' || $path[$position - 1] !== '/')
                ) {
                    continue;
                }

                $regex = $this->compilePath($path);
                $match = preg_match($regex, $url, $params) === 1;
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
     * @param string $path 
     * @return string 
     */
    protected function compilePath(string $path): string
    {
        $pattern = '`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`';
        if (preg_match_all($pattern, $path, $matches, PREG_SET_ORDER)) {
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
     * @param Route $route 
     * @return Dispatcher 
     */
    private function resolveMiddlewares(Route $route): Dispatcher
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
     * @param Route $route 
     * @return Dispatcher 
     */
    private function resolveHandler(Route $route): Dispatcher
    {
        $handler = $route->getHandler();
        $handler = $this->callbackResolver->resolve($handler);
        $route->setHandler($handler);

        return $this;
    }
}
