<?php

namespace Framework\Providers;

use Framework\Contracts\Container\ContainerInterface;
use Framework\Contracts\Container\ServiceProviderInterface;
use Framework\Http\HttpFactory;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

class HttpFactoryServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(ContainerInterface $container)
    {
        $factory = new HttpFactory;

        $container->set(RequestFactoryInterface::class, $factory)
            ->set(ResponseFactoryInterface::class, $factory)
            ->set(ServerRequestFactoryInterface::class, $factory)
            ->set(StreamFactoryInterface::class, $factory)
            ->set(UploadedFileFactoryInterface::class, $factory)
            ->set(UriFactoryInterface::class, $factory);
    }
}
