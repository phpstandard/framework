<?php

namespace Framework\Providers;

use Framework\Contracts\Container\ContainerInterface;
use Framework\Contracts\Container\ServiceProviderInterface;
use Framework\Contracts\Emitter\EmitterInterface;
use Framework\Contracts\Routing\DispatcherInterface;
use Framework\Contracts\Support\CallbackResolverInterface;
use Framework\Contracts\Support\MiddlewareResolverInterface;
use Framework\Emitter\SapiEmitter;
use Framework\Http\RequestHandler;
use Framework\Routing\Dispatcher;
use Framework\Support\CallbackResolver;
use Framework\Support\MiddlewareResolver;
use Psr\Http\Server\RequestHandlerInterface;

class RoutingServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(ContainerInterface $container)
    {
        $container
            ->set(EmitterInterface::class, SapiEmitter::class, true)
            ->set(RequestHandlerInterface::class, RequestHandler::class, true)
            ->set(DispatcherInterface::class, Dispatcher::class, true)
            ->set(MiddlewareResolverInterface::class, MiddlewareResolver::class)
            ->set(CallbackResolverInterface::class, CallbackResolver::class, true);
    }
}
